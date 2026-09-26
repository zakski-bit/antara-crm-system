<?php
if (!defined('PARTNER_STYLES_LOADED')) {
    define('PARTNER_STYLES_LOADED', true);
    ?>
    <style>
    .partner-logo-wrapper {
        width: 100%;
        height: 120px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 14px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        margin: 0 auto 16px;
        overflow: hidden;
    }
    .partner-logo-wrapper--small {
        width: 64px;
        height: 64px;
        padding: 12px;
        margin: 0;
        border-radius: 12px;
    }
    .partner-logo-wrapper img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    </style>
    <?php
}
