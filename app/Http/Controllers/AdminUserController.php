<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminUserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', ['users' => User::orderBy('id')->paginate(20)]);
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['role' => 'customer'])]);
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function store(Request $request)
    {
        User::create($this->data($request));

        return redirect()->route('admin.users.index')->with('status', 'Użytkownik dodany.');
    }

    public function update(Request $request, User $user)
    {
        $data = $this->data($request, $user);
        DB::transaction(function () use ($data, $user) {
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get();
            $user->refresh();
            if ($user->role === 'admin' && $data['role'] !== 'admin' && $admins->count() <= 1) {
                throw ValidationException::withMessages(['role' => 'Nie można odebrać uprawnień ostatniemu administratorowi.']);
            }
            $user->update($data);
        });

        return redirect()->route('admin.users.index')->with('status', 'Użytkownik zapisany.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            throw ValidationException::withMessages(['user' => 'Nie możesz usunąć własnego konta administratora.']);
        }
        DB::transaction(function () use ($user) {
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get();
            $user->refresh();
            if ($user->role === 'admin' && $admins->count() <= 1) {
                throw ValidationException::withMessages(['user' => 'Nie można usunąć ostatniego administratora.']);
            }
            $user->delete();
        });

        return back()->with('status', 'Użytkownik usunięty. Historia zamówień pozostaje zachowana.');
    }

    private function data(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:100', 'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'role' => 'required|in:customer,moderator,admin',
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'max:72', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).+$/s'],
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        }

return $data;
    }
}
