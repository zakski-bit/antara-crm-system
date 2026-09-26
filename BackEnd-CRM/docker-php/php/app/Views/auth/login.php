<?php
$baseUrl = trim(\App\Core\Config::get('app.base_url', ''), '/');
$loginAction = $baseUrl === '' ? '/login' : '/' . $baseUrl . '/login';
$pageError = $error ?? null;
$oldInput = $old ?? [];

require BASE_PATH . '/template/src/login.php';
