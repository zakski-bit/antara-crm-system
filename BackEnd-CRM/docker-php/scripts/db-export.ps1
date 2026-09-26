param(
    [string]$XamppMysqlBin = "C:\xampp\mysql\bin",
    [string]$Output = "$PSScriptRoot\backup.sql",
    [string[]]$Databases = @("antara_crm"),
    [string]$Host = "127.0.0.1",
    [int]$Port = 3306,
    [string]$User = "root",
    [string]$Password = ""
)

$mysqldump = Join-Path $XamppMysqlBin "mysqldump.exe"
if (-not (Test-Path $mysqldump)) {
    Write-Error "mysqldump.exe tidak ditemukan di $XamppMysqlBin"
    exit 1
}

$args = @("-u", $User)
if ($Password -ne "") {
    $args += "-p$Password"
}
$args += @("-h", $Host, "-P", $Port)
$args += @("--databases")
$args += $Databases
$args += @("--routines", "--triggers", "--events", "--default-character-set=utf8mb4")

& $mysqldump @args | Out-File -FilePath $Output -Encoding ascii
if ($LASTEXITCODE -ne 0) {
    throw "Export gagal. Cek kredensial atau nama database."
}

Write-Host "Backup tersimpan di $Output"
