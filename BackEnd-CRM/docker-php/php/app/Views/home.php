<?php
/** @var string $title */
/** @var string $message */

$title = $title ?? 'Antara News Portal';
$message = $message ?? 'Backend scaffold is up and running.';
$baseUrl = rtrim(\App\Core\Config::get('app.base_url', ''), '/');
$mitraUrl = ($baseUrl === '' ? '' : $baseUrl) . '/crm/mitra';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="h3 mb-3"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <p class="text-muted"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
                        <a href="<?= htmlspecialchars($mitraUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                            Buka Modul Mitra &amp; Korporasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
