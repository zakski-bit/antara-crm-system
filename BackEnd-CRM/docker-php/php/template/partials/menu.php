<?php
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$page = end($link_array);

// Include main menu components for pages that are not excluded
if ($page != 'under-maintenance.php' &&
    $page != 'under-construction.php' &&
    $page != 'coming-soon.php' &&
    $page != 'error-404.php' &&
    $page != 'error-500.php' &&
    $page != 'two-step-verification-3.php' &&
    $page != 'two-step-verification-2.php' &&
    $page != 'two-step-verification.php' &&
    $page != 'email-verification-3.php' &&
    $page != 'email-verification-2.php' &&
    $page != 'email-verification.php' &&
    $page != 'reset-password-3.php' &&
    $page != 'reset-password-2.php' &&
    $page != 'reset-password.php' &&
    $page != 'forgot-password-3.php' &&
    $page != 'forgot-password-2.php' &&
    $page != 'forgot-password.php' &&
    $page != 'register-3.php' &&
    $page != 'register-2.php' &&
    $page != 'register.php' &&
    $page != 'login-3.php' &&
    $page != 'login-2.php' &&
    $page != 'login.php' &&
    $page != 'success.php' &&
    $page != 'success-2.php' &&
    $page != 'success-3.php' &&
    $page != 'lock-screen.php' &&
    $page != 'job-grid-2.php' &&
    $page != 'job-list-2.php' &&
    $page != 'job-details.php') {
    
    include 'topbar.php';
    include 'sidebar.php';
    include 'horizontal-sidebar.php';
    include 'twocolumn-sidebar.php';
    include 'stacked-sidebar.php';
}

// Include job header for specific job-related pages
if ($page === 'job-grid-2.php' || $page === 'job-list-2.php' || $page === 'job-details.php') {
    include 'job-header.php';
}
