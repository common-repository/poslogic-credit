<?php
/*
Plugin Name: Poslogic Credit
Description: Extends WooCommerce by Adding the Poslogic Credit Gateway.
Version: 1.0.5
Author: Vasily Kolesnik, Finservice
Author URI: https://www.finservice.pro/
*/
$poslogic_credit = new Poslogic_Credit(__FILE__);
$poslogic_credit->register();

class Poslogic_Credit{
    public $plugin_name;

    public function __construct($file){
        $this->plugin_name = plugin_basename($file);
    }

    public function register(){
        $this->register_activation_hooks();

        add_action( 'plugins_loaded', array($this, 'gateway_init'), 0 );
        add_filter( 'woocommerce_payment_gateways', array($this, 'add_payment_gateway') );
        add_filter( 'plugin_action_links_' . $this->plugin_name, array($this, 'settings_links') );
    }

    public function register_activation_hooks(){
        include_once "poslogic-credit-activation.php";
        register_activation_hook(   __FILE__,  array( 'Poslogic_Credit_Activation', 'on_activation') );
        register_deactivation_hook( __FILE__, array( 'Poslogic_Credit_Activation', 'on_deactivation' ) );
    }

    public function gateway_init() {
        load_plugin_textdomain( 'poslogic-credit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
        include_once('poslogic-credit-gateway.php');


    }
    public function settings_links($links){
        $plugin_links = array(
            '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'poslogic-credit' ) . '</a>',
        );

        // Merge our new link with the default ones
        return array_merge( $plugin_links, $links );

    }

    public function add_payment_gateway( $methods ) {
        $methods[] = 'Poslogic_Credit_Gateway';
        return $methods;
    }
}