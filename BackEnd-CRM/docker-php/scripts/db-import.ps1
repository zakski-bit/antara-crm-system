param(
    [string]$Input = "$PSScriptRoot\backup.sql",
    [string]$Container = "app_mariadb",
    [string]$RootPassword = $env:DB_ROOT_PASSWORD
)

function Get-DotEnvValue {
    param(
        [string]$Path,
        [string]$Key
    )

    if (-not (Test-Path $Path)) {
        return $null
    }

    foreach ($line in Get-Content -Path $Path) {
        if ($line -match '^\s*#' -or $line -match '^\s*$') {
            continue
        }
        if ($line -match '^\s*([^=]+)=(.*)$') {
            $name = $matches[1].Trim()
            if ($name -ne $Key) {
                continue
            }
            return $matches[2].Trim().Trim('"')
        }
    }

    return $null
}

if (-not (Test-Path $Input)) {
    Write-Error "File backup tidak ditemukan: $Input"
    exit 1
}

if (-not $RootPassword) {
    $envPath = Join-Path $PSScriptRoot "..\.env"
    $RootPassword = Get-DotEnvValue -Path $envPath -Key "DB_ROOT_PASSWORD"
}

if (-not $RootPassword) {
    Write-Error "DB_ROOT_PASSWORD belum diset. Isi di .env atau pakai -RootPassword."
    exit 1
}

Get-Content -Path $Input | docker exec -i $Container mysql -uroot -p$RootPassword
if ($LASTEXITCODE -ne 0) {
    throw "Import gagal. Pastikan container MariaDB berjalan."
}

Write-Host "Import selesai dari $Input"
