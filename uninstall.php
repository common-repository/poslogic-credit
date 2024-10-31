<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

include_once "poslogic-credit-activation.php";

Poslogic_Credit_Activation::on_uninstall();