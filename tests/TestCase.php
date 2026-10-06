<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function actingAs(Authenticatable $user, $guard = null)
    {
        // Symulacja nowego logowania przy zmianie użytkownika w jednym teście.
        $this->withSession(['password_hash_'.($guard ?? 'web') => $user->getAuthPassword()]);

        return parent::actingAs($user, $guard);
    }
}
