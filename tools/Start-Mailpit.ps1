$ErrorActionPreference = 'Stop'
$mailpitDir = 'C:\xampp\tools\mailpit'
$mailpitExe = Join-Path $mailpitDir 'mailpit.exe'
if (-not (Test-Path -LiteralPath $mailpitExe)) {
    New-Item -ItemType Directory -Path $mailpitDir -Force | Out-Null
    $archive = Join-Path $mailpitDir 'mailpit-v1.31.4.zip'
    Invoke-WebRequest -Uri 'https://github.com/axllent/mailpit/releases/download/v1.31.4/mailpit-windows-amd64.zip' -OutFile $archive -UseBasicParsing
    $expectedHash = '0dd6ec909a6f5b2b66682b22d82abce97ab793e30766bf2f642b691e70a4a16b'
    if ((Get-FileHash -LiteralPath $archive -Algorithm SHA256).Hash.ToLowerInvariant() -ne $expectedHash) {
        throw 'Nieprawidlowy SHA256 pobranego pliku Mailpit. Instalacja przerwana.'
    }
    Expand-Archive -LiteralPath $archive -DestinationPath $mailpitDir -Force
}
$listener = Get-NetTCPConnection -LocalPort 8025 -State Listen -ErrorAction SilentlyContinue
if ($listener -and (Get-Process -Id $listener[0].OwningProcess).ProcessName -ne 'mailpit') {
    throw 'Port 8025 jest zajety przez inny program. Nie uruchomiono Mailpit.'
}
if (-not $listener) {
    Start-Process -FilePath $mailpitExe -ArgumentList '--listen','127.0.0.1:8025','--smtp','127.0.0.1:1025','--database',(Join-Path $mailpitDir 'mailpit.db') -WindowStyle Hidden
}
for ($attempt = 0; $attempt -lt 30; $attempt++) {
    $smtpListener = Get-NetTCPConnection -LocalPort 1025 -State Listen -ErrorAction SilentlyContinue
    if ($smtpListener -and (Get-Process -Id $smtpListener[0].OwningProcess).ProcessName -eq 'mailpit') { break }
    Start-Sleep -Milliseconds 100
}
if (-not $smtpListener -or (Get-Process -Id $smtpListener[0].OwningProcess).ProcessName -ne 'mailpit') {
    throw 'Mailpit nie slucha na porcie SMTP 1025. Sprawdz, czy port nie jest zajety.'
}
Write-Host 'Skrzynka testowa: http://127.0.0.1:8025'
Write-Host 'Wiadomosci zostaja na tym komputerze. Mailpit nie wysyla ich do Internetu.'
