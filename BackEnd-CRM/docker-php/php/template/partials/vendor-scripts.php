<?php
$link = $_SERVER[ 'PHP_SELF' ];
$link_array = explode( '/', $link );
$page = end( $link_array );

$assetBaseConfig = '';
if (class_exists('\App\Core\Config')) {
	$assetBaseConfig = trim(\App\Core\Config::get('app.base_url', ''), '/');
}
$assetBaseUrl = $assetBaseConfig === '' ? '/assets' : '/' . $assetBaseConfig . '/assets';
$asset = static function (string $path) use ($assetBaseUrl): string {
	return htmlspecialchars($assetBaseUrl . '/' . ltrim($path, '/'), ENT_QUOTES, 'UTF-8');
};
?>
    <!-- jQuery -->
    <script src="<?= $asset('js/jquery-3.7.1.min.js'); ?>"></script>

    <!-- Bootstrap Core JS -->
    <script src="<?= $asset('js/bootstrap.bundle.min.js'); ?>"></script>

    <!-- Feather Icon JS -->
    <script src="<?= $asset('js/feather.min.js'); ?>"></script>

<?php if($page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'two-step-verification-3.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification.php' && $page !== 'email-verification-3.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification.php' && $page !== 'reset-password-3.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password.php' && $page !== 'forgot-password-3.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password.php' && $page !== 'register-3.php' && $page !== 'register-2.php' && $page !== 'register.php' && $page !== 'login-3.php' && $page !== 'login-2.php' && $page !== 'login.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'lock-screen.php') {?>
    <!-- Slimscroll JS -->
    <script src="<?= $asset('js/jquery.slimscroll.min.js'); ?>"></script>

    <!-- Summernote JS -->
    <script src="<?= $asset('plugins/summernote/summernote-lite.min.js'); ?>"></script>    

    <!-- Daterangepikcer JS -->
    <script src="<?= $asset('js/moment.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/daterangepicker/daterangepicker.js'); ?>"></script>    
<?php } ?>

<?php if($page === 'add-invoices.php' || $page === 'edit-invoices.php' || $page === 'file-manager.php' || $page === 'group-video-call.php' || $page === 'invoice-details.php' || $page === 'invoices.php' || $page === 'manage-jobs.php' || $page === 'maps-leaflet.php' || $page === 'maps-vector.php' || $page === 'payslip.php' || $page === 'promotion.php' || $page === 'resignation.php' || $page === 'termination.php') {?>
    <!-- Player JS -->
    <script src="<?= $asset('js/plyr-js.js'); ?>"></script>
<?php } ?>    

<?php if($page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'two-step-verification-3.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification.php' && $page !== 'email-verification-3.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification.php' && $page !== 'reset-password-3.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password.php' && $page !== 'forgot-password-3.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password.php' && $page !== 'register-3.php' && $page !== 'register-2.php' && $page !== 'register.php' && $page !== 'login-3.php' && $page !== 'login-2.php' && $page !== 'login.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'lock-screen.php') {?>
    <!-- Color Picker JS -->
    <script src="<?= $asset('plugins/@simonwep/pickr/pickr.es5.min.js'); ?>"></script>

    <!-- Datatable JS -->
    <script src="<?= $asset('js/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?= $asset('js/dataTables.bootstrap5.min.js'); ?>"></script>	
    <script src="<?= $asset('js/jszip.min.js'); ?>"></script>
    <script src="<?= $asset('js/pdfmake.min.js'); ?>"></script>
    <script src="<?= $asset('js/vfs_fonts.js'); ?>"></script>
    <script src="<?= $asset('js/dataTables.buttons.min.js'); ?>"></script>
    <script src="<?= $asset('js/buttons.bootstrap5.min.js'); ?>"></script>
    <script src="<?= $asset('js/buttons.html5.min.js'); ?>"></script>
  
    <!-- Bootstrap Tagsinput JS -->
    <script src="<?= $asset('plugins/bootstrap-tagsinput/bootstrap-tagsinput.js'); ?>"></script>
<?php } ?>

<?php if($page === 'add-invoices.php' || $page === 'edit-invoices.php' || $page === 'file-manager.php' || $page === 'group-video-call.php' || $page === 'invoice-details.php' || $page === 'invoices.php' || $page === 'manage-jobs.php' || $page === 'maps-leaflet.php' || $page === 'maps-vector.php' || $page === 'payslip.php' || $page === 'promotion.php' || $page === 'resignation.php' || $page === 'termination.php') {?>    
    <!-- Owl Carousel -->
    <script src="<?= $asset('plugins/owlcarousel/owl.carousel.min.js'); ?>"></script>
<?php } ?>

<?php if($page === 'job-grid-2.php' || $page === 'job-list-2.php' || $page === 'plugin.php' || $page === 'ui-rangeslider.php' || $page === 'ui-rating.php') {?>
    <!-- Rangeslider JS -->
    <script src="<?= $asset('plugins/ion-rangeslider/js/ion.rangeSlider.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/ion-rangeslider/js/custom-rangeslider.js'); ?>"></script>
<?php } ?>

<?php if($page === 'calendar.php' || $page === 'incoming-call.php' || $page === 'outgoing-call.php' || $page === 'video-call.php' || $page === 'voice-call.php') {?>
    <!-- Fullcalendar JS -->
    <script src="<?= $asset('plugins/fullcalendar/index.global.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/fullcalendar/calendar-data.js'); ?>"></script>
<?php } ?>

<?php if($page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'two-step-verification-3.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification.php' && $page !== 'email-verification-3.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification.php' && $page !== 'reset-password-3.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password.php' && $page !== 'forgot-password-3.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password.php' && $page !== 'register-3.php' && $page !== 'register-2.php' && $page !== 'register.php' && $page !== 'login-3.php' && $page !== 'login-2.php' && $page !== 'login.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'lock-screen.php') {?>    
    <!-- Datetimepicker JS -->
    <script src="<?= $asset('js/bootstrap-datetimepicker.min.js'); ?>"></script>

    <!-- Select2 JS -->
    <script src="<?= $asset('plugins/select2/js/select2.min.js'); ?>"></script>
<?php } ?>

<?php if($page === 'calendar.php' || $page === 'file-manager.php' || $page === 'incoming-call.php' || $page === 'outgoing-call.php' || $page === 'plugin.php' || $page === 'video-call.php') {?>    
    <!-- Theiastickysidebar JS -->
    <script src="<?= $asset('plugins/theia-sticky-sidebar/theia-sticky-sidebar.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/theia-sticky-sidebar/ResizeSensor.min.js'); ?>"></script>
<?php } ?>

<?php if($page === 'email-reply.php' || $page === 'email.php' || $page === 'notes.php' || $page === 'plugin.php' || $page === 'project-details.php' || $page === 'social-feed.php' || $page === 'task-details.php') {?>
    <!-- Owl Carousel JS -->
    <script src="<?= $asset('js/owl.carousel.min.js'); ?>"></script>
<?php } ?>

<?php if($page === 'ui-clipboard.php') {?>
    <!-- Clipboard JS -->
    <script src="<?= $asset('plugins/clipboard/clipboard.min.js'); ?>"></script>
<?php } ?>

<?php if($page === 'maps-vector.php') {?>
    <script src="<?= $asset('plugins/jsvectormap/js/jsvectormap.min.js'); ?>"></script>
    <!-- JSVector Maps MapsJS -->
    <script src="<?= $asset('plugins/jsvectormap/maps/world-merc.js'); ?>"></script>
    <script src="<?= $asset('js/us-merc-en.js'); ?>"></script>
    <script src="<?= $asset('js/russia.js'); ?>"></script>
    <script src="<?= $asset('js/spain.js'); ?>"></script>
    <script src="<?= $asset('js/canada.js'); ?>"></script>
    <script src="<?= $asset('js/jsvectormap.js'); ?>"></script>
    <script src="<?= $asset('plugins/@simonwep/pickr/pickr.min.js'); ?>"></script>
<?php } ?>

<?php if($page === 'maps-leaflet.php') {?>
    <script src="<?= $asset('plugins/leaflet/leaflet.js'); ?>"></script>
    <script src="<?= $asset('js/leaflet.js'); ?>"></script>
<?php } ?>

<?php if($page === 'ui-drag-drop.php') {?>
    <!-- Dragula JS -->
    <script src="<?= $asset('plugins/dragula/js/dragula.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/dragula/js/drag-drop.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/dragula/js/draggable-cards.js'); ?>"></script>
<?php } ?>

<?php if($page === 'ui-sweetalerts.php' || $page === 'ui-ribbon.php') {?>
    <!-- Sweetalert 2 -->
    <script src="<?= $asset('plugins/sweetalert/sweetalert2.all.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/sweetalert/sweetalerts.min.js'); ?>"></script>
<?php } ?>

<?php if($page === 'ui-stickynote.php' || $page === 'kanban-view.php' || $page === 'task-board.php' || $page === 'deals-grid.php' || $page === 'leads-grid.php' || $page === 'candidates-kanban.php') {?>
    <!-- Stickynote JS -->
    <script src="<?= $asset('js/jquery-ui.min.js'); ?>"></script>
    <script src="<?= $asset('js/jquery.ui.touch-punch.min.js'); ?>"></script>    
<?php } ?>

<?php if($page === 'plugin.php' || $page === 'ui-stickynote.php') {?>
    <script src="<?= $asset('plugins/stickynote/sticky.js'); ?>"></script>
<?php } ?>

<?php if($page === 'chart-apex.php' || $page === 'index.php' || $page === 'employee-dashboard.php' || $page === 'deals-dashboard.php' || $page === 'leads-dashboard.php' || $page === 'file-manager.php' || $page === 'dashboard.php' || $page === 'companies.php' || $page === 'packages.php' || $page === 'layout-horizontal.php' || $page === 'layout-detached.php' || $page === 'layout-modern.php' || $page === 'layout-horizontal-overlay.php' || $page === 'layout-hovered.php' || $page === 'layout-box.php' || $page === 'layout-horizontal-single.php' || $page === 'layout-horizontal-box.php' || $page === 'layout-horizontal-fullwidth.php' || $page === 'layout-horizontal-sidemenu.php' || $page === 'layout-vertical-transparent.php' || $page === 'layout-without-header.php' || $page === 'layout-rtl.php' || $page === 'layout-stacked.php' || $page === 'layout-dark.php' || $page === 'analytics.php' || $page === 'expenses-report.php' || $page === 'invoice-report.php' || $page === 'payment-report.php' || $page === 'project-report.php' || $page === 'task-report.php' || $page === 'user-report.php' || $page === 'employee-report.php' || $page === 'payslip-report.php' || $page === 'attendance-report.php' || $page === 'leave-report.php' || $page === 'daily-report.php') {?>
    <!-- Chart JS -->
    <script src="<?= $asset('plugins/apexchart/apexcharts.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/apexchart/chart-data.js'); ?>"></script>
<?php } ?>

<?php if($page === 'chart-c3.php') {?>
    <!-- Chart JS -->
    <script src="<?= $asset('plugins/c3-chart/d3.v5.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/c3-chart/c3.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/c3-chart/chart-data.js'); ?>"></script>
<?php } ?>

<?php if($page === 'chart-js.php' || $page === 'index.php' || $page === 'deals-dashboard.php' || $page === 'dashboard.php' || $page === 'companies.php' || $page === 'layout-horizontal.php' || $page === 'layout-detached.php' || $page === 'layout-modern.php' || $page === 'layout-horizontal-overlay.php' || $page === 'layout-hovered.php' || $page === 'layout-box.php' || $page === 'layout-horizontal-single.php' || $page === 'layout-horizontal-box.php' || $page === 'layout-horizontal-fullwidth.php' || $page === 'layout-horizontal-sidemenu.php' || $page === 'layout-vertical-transparent.php' || $page === 'layout-without-header.php' || $page === 'layout-rtl.php' || $page === 'layout-stacked.php' || $page === 'layout-dark.php' || $page === 'analytics.php') {?>
    <!-- Chart JS -->
    <script src="<?= $asset('plugins/chartjs/chart.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/chartjs/chart-data.js'); ?>"></script>
<?php } ?>

<?php if($page === 'chart-morris.php') {?>
    <!-- Chart JS -->
    <script src="<?= $asset('plugins/morris/raphael-min.js'); ?>"></script>
    <script src="<?= $asset('plugins/morris/morris.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/morris/chart-data.js'); ?>"></script>
<?php } ?>

<?php if($page === 'chart-peity.php' || $page === 'deals-dashboard.php' || $page === 'leads-dashboard.php' || $page === 'dashboard.php' || $page === 'companies.php' || $page === 'subscription.php' || $page === 'tickets-grid.php' || $page === 'tickets.php' || $page === 'task-report.php') {?>
    <!-- Chart JS -->
    <script src="<?= $asset('plugins/peity/jquery.peity.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/peity/chart-data.js'); ?>"></script>
<?php } ?>

<?php if($page === 'chart-flot.php') {?>
    <!-- Chart JS -->
    <script src="<?= $asset('plugins/flot/jquery.flot.js'); ?>"></script>
    <script src="<?= $asset('plugins/flot/jquery.flot.fillbetween.js'); ?>"></script>
    <script src="<?= $asset('plugins/flot/jquery.flot.pie.js'); ?>"></script>
    <script src="<?= $asset('plugins/flot/chart-data.js'); ?>"></script>
<?php } ?>

<?php if($page === 'ui-rating.php') {?>
    <!-- Rater JS -->
    <script src="<?= $asset('plugins/rater-js/index.js'); ?>"></script>

    <!-- Internal Ratings JS -->
    <script src="<?= $asset('js/ratings.js'); ?>"></script>
<?php } ?>

<?php if($page === 'ui-toasts.php') {?>
    <!-- Chart JS -->
    <script src="<?= $asset('plugins/toastr/toastr.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/toastr/toastr.js'); ?>"></script>
<?php } ?>

<?php if($page === 'ui-counter.php') {?>
    <!-- Stickynote JS -->
    <script src="<?= $asset('plugins/countup/jquery.counterup.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/countup/jquery.waypoints.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/countup/jquery.missofis-countdown.js'); ?>"></script>

	<script src="<?= $asset('js/counter.js'); ?>"></script>
<?php } ?>

<?php if($page === 'ui-lightbox.php') {?>
    <!-- Alertify JS -->
    <script src="<?= $asset('plugins/lightbox/glightbox.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/lightbox/lightbox.js'); ?>"></script>
<?php } ?>

<?php if($page === 'ui-swiperjs.php') {?>
    <!-- Swiper JS -->
    <script src="<?= $asset('plugins/swiper/swiper-bundle.min.js'); ?>"></script>
	<script src="<?= $asset('plugins/@simonwep/pickr/pickr.min.js'); ?>"></script>

    <!-- Internal Swiper JS -->
    <script src="<?= $asset('js/swiper.js'); ?>"></script>
<?php } ?>

<?php if($page === 'form-wizard.php') {?>
    <!-- Wizard JS -->
    <script src="<?= $asset('plugins/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
    <script src="<?= $asset('plugins/twitter-bootstrap-wizard/prettify.js'); ?>"></script>
    <script src="<?= $asset('plugins/twitter-bootstrap-wizard/form-wizard.js'); ?>"></script>
<?php } ?>

<?php if($page === 'form-mask.php') {?>
    <!-- Mask JS -->
    <script src="<?= $asset('js/jquery.maskedinput.min.js'); ?>"></script>
    <script src="<?= $asset('js/mask.js'); ?>"></script>
<?php } ?>

<?php if($page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'two-step-verification-3.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification.php' && $page !== 'email-verification-3.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification.php' && $page !== 'reset-password-3.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password.php' && $page !== 'forgot-password-3.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password.php' && $page !== 'register-3.php' && $page !== 'register-2.php' && $page !== 'register.php' && $page !== 'login-3.php' && $page !== 'login-2.php' && $page !== 'login.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'lock-screen.php') {?>
    <!-- Sticky Sidebar JS -->
    <script src="<?= $asset('plugins/theia-sticky-sidebar/ResizeSensor.js'); ?>"></script>
    <script src="<?= $asset('plugins/theia-sticky-sidebar/theia-sticky-sidebar.js'); ?>"></script>
<?php } ?>

<?php if($page === 'reset-password.php' || $page === 'reset-password-2.php' || $page === 'reset-password-3.php') {?>
    <!-- Validation-->
    <script src="<?= $asset('js/validation.js'); ?>"></script>
<?php } ?>

<?php if($page === 'email-verification.php' || $page === 'email-verification-2.php' || $page === 'email-verification-3.php' || $page === 'two-step-verification.php' || $page === 'two-step-verification-2.php' || $page === 'two-step-verification-3.php') {?>
    <script src="<?= $asset('js/otp.js'); ?>"></script>
<?php } ?>

<?php if($page === 'form-fileupload.php') {?>
    <!-- Fileupload JS -->
    <script src="<?= $asset('plugins/fileupload/fileupload.min.js'); ?>"></script>

    <script src="<?= $asset('js/file-upload.js'); ?>"></script>
<?php } ?>

<?php if($page === 'employee-salary.php') {?>
    <script src="<?= $asset('js/employee-salary.js'); ?>"></script>
<?php } ?>

<?php if($page === 'employee-salary.php' || $page === 'project-details.php' || $page === 'gallery.php' || $page === 'plugins.php' || $page === 'search-result.php' || $page === 'social-feed.php') {?>
    <!-- Fancybox JS -->
    <script src="<?= $asset('plugins/fancybox/jquery.fancybox.min.js'); ?>"></script>
<?php } ?>

<?php if($page === 'form-pickers.php') {?>
    <script src="<?= $asset('plugins/flatpickr/flatpickr.js'); ?>"></script>
    <script src="<?= $asset('plugins/bootstrap-datepicker/bootstrap-datepicker.js'); ?>"></script>
    <script src="<?= $asset('plugins/jquery-timepicker/jquery-timepicker.js'); ?>"></script>
    <script src="<?= $asset('plugins/pickr/pickr.js'); ?>"></script>

    <!-- Page JS -->
    <script src="<?= $asset('js/forms-pickers.js'); ?>"></script>
<?php } ?>

<?php if($page === 'coming-soon.php') {?>
    <script src="<?= $asset('js/coming-soon.js'); ?>"></script>
<?php } ?>

<?php if($page === 'email-reply.php'|| $page === 'email.php' || $page === 'social-feed.php') {?>
    <script src="<?= $asset('js/email.js'); ?>"></script>
<?php } ?>

<?php if($page === 'candidates-kanban.php' || $page === 'kanban-view.php' || $page === 'delas-grid.php' || $page === 'leads-grid.php' || $page === 'task-board.php') {?>
    <script src="<?= $asset('js/kanban.js'); ?>"></script>
<?php } ?>

<?php if($page === 'add-invoices.php' || $page === 'edit-invoices.php') {?>
    <script src="<?= $asset('js/invoice.js'); ?>"></script>
<?php } ?>

<?php if($page === 'project-details.php') {?>
    <script src="<?= $asset('js/projects.js'); ?>"></script>
<?php } ?>

<?php if($page === 'leave-settings.php') {?>
	<!-- Multiselect JS -->
	<script src="<?= $asset('js/multiselect.min.js'); ?>"></script>
<?php } ?>

<?php if($page === 'ui-popovers.php' || $page === 'ui-tooltips.php') {?>
	<script src="<?= $asset('js/popover.js'); ?>"></script>
<?php } ?>

<?php if($page === 'company-details.php' || $page === 'contact-details.php' || $page === 'delas-details.php' || $page === 'leads-details.php') {?>
    <script src="<?= $asset('js/add-comments.js'); ?>"></script>
<?php } ?>

<?php if($page === 'file-manager.php' || $page === 'project-details.php' || $page === 'task-details.php') {?>
    <script src="<?= $asset('js/file-manager.js'); ?>"></script>
<?php } ?>

<?php if($page === 'client-details.php' || $page === 'employee-dashboard.php' || $page === 'index.php' || $page === 'layout-box.php' || $page === 'layout-dark.php' || $page === 'layout-detached.php' || $page === 'layout-horizontal-box.php' || $page === 'layout-horizontal-fullwidth.php' || $page === 'layout-horizontal-overlay.php' || $page === 'layout-horizontal-single.php' || $page === 'layout-horizontal-sidemenu.php' || $page === 'layout-horizontal.php' || $page === 'layout-modern.php' || $page === 'layout-hovered.php' || $page === 'layout-vertical-transparent.php' || $page === 'layout-without-header.php' || $page === 'layout-rtl.php' || $page === 'layout-stacked.php' || $page === 'project-details.php' || $page === 'task-details.php' || $page === 'tasks.php' || $page === 'todo-list.php' || $page === 'todo.php') {?>
    <!-- Todo JS -->
    <script src="<?= $asset('js/todo.js'); ?>"></script>
<?php } ?>

<?php if($page !== 'under-maintenance.php' && $page !== 'under-construction.php' && $page !== 'coming-soon.php' && $page !== 'error-404.php' && $page !== 'error-500.php' && $page !== 'two-step-verification-3.php' && $page !== 'two-step-verification-2.php' && $page !== 'two-step-verification.php' && $page !== 'email-verification-3.php' && $page !== 'email-verification-2.php' && $page !== 'email-verification.php' && $page !== 'reset-password-3.php' && $page !== 'reset-password-2.php' && $page !== 'reset-password.php' && $page !== 'forgot-password-3.php' && $page !== 'forgot-password-2.php' && $page !== 'forgot-password.php' && $page !== 'register-3.php' && $page !== 'register-2.php' && $page !== 'register.php' && $page !== 'login-3.php' && $page !== 'login-2.php' && $page !== 'login.php' && $page !== 'success.php' && $page !== 'success-2.php' && $page !== 'success-3.php' && $page !== 'lock-screen.php') {?>
    <!-- Theme Color JS -->
    <script src="<?= $asset('js/theme-colorpicker.js'); ?>"></script>
<?php } ?>

    <!-- Custom JS -->
    <script src="<?= $asset('js/script.js'); ?>"></script>
