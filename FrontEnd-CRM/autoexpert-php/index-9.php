<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$head_title = "ANTARA - Berita Terkini dan Terpercaya";
$header_outer_box_layout = 'login-call';
?>

<?php require_once('parts/header/header.php'); ?>

<?php

require_once('parts/home9/banner.php');
require_once('parts/home9/services.php');
require_once('parts/home8/news.php');
require_once('parts/home9/project.php');
require_once('parts/home9/funfact.php');
require_once('parts/home9/team.php');
require_once('parts/home9/testimonials.php');
require_once('parts/home9/contact.php');

?>

<?php require_once('parts/footer/footer.php'); ?>