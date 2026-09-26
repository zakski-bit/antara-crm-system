<?php require_once('parts/header/head.php'); ?>

<div class="page-wrapper">

<?php
if (empty($disable_preloader)) {
    require_once('parts/preloader.php');
}
$userFullName = $_SESSION['auth_user']['full_name'] ?? null;
?>

<header class="main-header header-style-eight">
    <div class="header-lower">
        <div class="inner-container"> 
            <!-- Main box -->
            <div class="main-box">
                <!--Mobile Navigation Toggler-->
                <div class="mobile-nav-toggler"><span class="icon lnr-icon-bars"></span></div>

                <div class="logo-box">
                    <div class="logo">
                        <?php $default_logo_url = "images/antaradark1.png";
                            if(isset($dark_logo_url)&&!empty($dark_logo_url)) {
                                $default_logo_url = $dark_logo_url;
                            }
                        ?>
                        <a href="index-9.php"><img src="<?php echo $default_logo_url;?>" alt="ANTARA" class="antara-logo"></a>
                    </div>
                </div>
                
                <!--Nav Box-->
                <div class="nav-outer">
                    <nav class="nav main-menu">
                        <?php
                            $default_menu_file = "parts/header/menu.php";
                            if(isset($single_menu_file)&&!empty($single_menu_file)) {
                                $default_menu_file = $single_menu_file;
                            }
                        ?>
                        <?php require_once($default_menu_file); ?>
                    </nav>
                </div>
                <!-- Main Menu End--> 
                
                <!-- Outer Box -->
                <div class="outer-box"> 
                    <span class="divider"></span>
                    <?php
                        $header_outer_box_layout = $header_outer_box_layout ?? 'call';
                        $header_outer_box_items = [
                            'call' => ['call'],
                            'call-only' => ['call'],
                            'call-login' => ['call', 'login'],
                            'login-call' => ['login', 'call'],
                            'login-only' => ['login'],
                            'none' => [],
                        ];
                        $outer_box_items = $header_outer_box_items[$header_outer_box_layout] ?? ['call'];
                        foreach ($outer_box_items as $outer_box_item) {
                            if ($outer_box_item === 'login') {
                                if ($userFullName) {
                    ?>
                        <a href="page-login.php" class="theme-btn btn-style-one header-login-btn header-login-btn--welcome">
                            <span class="btn-title">Hai, <?php echo htmlspecialchars($userFullName); ?></span>
                        </a>
                    <?php
                                } else {
                    ?>
                        <a href="page-login.php" class="theme-btn btn-style-one header-login-btn">
                            <span class="btn-title">Masuk</span>
                        </a>
                    <?php
                                }
                            } elseif ($outer_box_item === 'call') {
                    ?>
                        <a href="tel:+62213842591" class="info-btn-two">
                            <i class="icon fa fa-phone"></i>
                            <small>Telepon</small>
                            <strong>021-3842591</strong>
                        </a>
                    <?php
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu  -->
    <div class="mobile-menu">
        <div class="menu-backdrop"></div>
        
        <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
        <nav class="menu-box">
            <div class="upper-box">
                <div class="nav-logo">
                    <?php $default_logo_url = "images/antara.png";
                        if(isset($dark_logo_url)&&!empty($dark_logo_url)) {
                            $default_logo_url = $dark_logo_url;
                        }
                    ?>
                    <a href="index-9.php"><img src="<?php echo $default_logo_url;?>" alt="ANTARA" class="antara-logo"></a>
                </div>
                <div class="close-btn"><i class="icon fa fa-times"></i></div>
            </div>
            <ul class="navigation clearfix">
                <!--Keep This Empty / Menu will come through Javascript-->
            </ul>
            <ul class="contact-list-one">
                <li> <i class="icon lnr-icon-phone-handset"></i> <span class="title">Call Now</span>
                    <div class="text"><a href="tel:+62213842591">021-3842591</a></div>
                </li>
                <li> <i class="icon lnr-icon-envelope1"></i> <span class="title">Send Email</span>
                    <div class="text"><a href="mailto:help@company.com">help@company.com</a></div>
                </li>
                <li> <i class="icon lnr-icon-map-marker"></i> <span class="title">Address</span>
                    <div class="text">66 Broklyant, New York India 3269</div>
                </li>
            </ul>
            <ul class="social-links">
                <li><a href="#"><i class="fa fa-x"></i></a></li>
                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                <li><a href="#"><i class="fab fa-pinterest"></i></a></li>
                <li><a href="#"><i class="fab fa-instagram"></i></a></li>
            </ul>
        </nav>
    </div>
    <!-- End Mobile Menu --> 
    
    <!-- Header Search -->
    <div class="search-popup"> <span class="search-back-drop"></span>
        <button class="close-search"><span class="fa fa-times"></span></button>
        <div class="search-inner">
            <form method="post" action="index.php">
                <div class="form-group">
                    <input type="search" name="search-field" value="" placeholder="Search..." required="">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>
    <!-- End Header Search --> 
    
    <!-- Sticky Header  -->
    <div class="sticky-header">
        <div class="auto-container">
            <div class="inner-container"> 
                <!--Mobile Navigation Toggler-->
                <div class="mobile-nav-toggler"><span class="icon lnr-icon-bars"></span></div>

                <!--Logo-->
                <?php $default_logo_url = "images/antara.png";
                    if(isset($dark_logo_url)&&!empty($dark_logo_url)) {
                        $default_logo_url = $dark_logo_url;
                    }
                ?>
                <a href="index-9.php"><img src="<?php echo $default_logo_url;?>" alt="ANTARA" class="antara-logo antara-logo--sticky"></a>
                
                <!--Right Col-->
                <div class="nav-outer"> 
                    <!-- Main Menu -->
                    <nav class="main-menu">
                        <div class="navbar-collapse show collapse clearfix">
                            <ul class="navigation clearfix">
                                <!--Keep This Empty / Menu will come through Javascript-->
                            </ul>
                        </div>
                    </nav>
                    <!-- Main Menu End--> 
                </div>
                <div class="sticky-header__actions">
                    <?php if ($userFullName): ?>
                        <a href="page-login.php" class="theme-btn btn-style-one header-login-btn header-login-btn--welcome header-login-btn--sticky">
                            <span class="btn-title">Hai, <?php echo htmlspecialchars($userFullName); ?></span>
                        </a>
                    <?php else: ?>
                        <a href="page-login.php" class="theme-btn btn-style-one header-login-btn header-login-btn--sticky">
                            <span class="btn-title">Masuk</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End Sticky Menu --> 
</header>
