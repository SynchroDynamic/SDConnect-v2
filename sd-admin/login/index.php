<?php

session_start();
if (!empty($_SESSION["userId"])) {
    include_once dirname(__DIR__, 2) . '/inc/sd-config1.php';
    echo '<meta http-equiv="refresh" content="0;url=' . \SDC::URL . \SDC::SUBFOLDER . 'sd-admin/">';
} else {
    require_once './view/login-form.php';
}
?>