<?php
    /**
     * Plugin Name: Woocommerce Product Auto Hide
     * Description: Hide out of stock woocommerce products.
     * Plugin URI : https://www.example.com
     * Version: 1.0.0
     * Author: XfinitySoft
     * Author URI: https://www.example.com
     * Text Domain: xs-wpah
    */
    // Exit if directly access
    if ( !defined( 'ABSPATH' ) ) {
    exit;
    }
    // Define  XS_WPAH_PATH constant
    if ( ! defined( 'XS_WPAH_PATH' ) ) {
        define( 'XS_WPAH_PATH' , __FILE__ );
    }
    // define function for deactivation hook
    function xs_wpah_plugin_deactivate(){
        // deactivating own plugin
        deactivate_plugins( __FILE__ );   
    }
    // define function for register activation hook
    function xs_wpah_check_woocommerce(){
        // check if woocommerce plugin is active or not
        // if woocommerce is not active then deactivate own plugin
        if ( ! class_exists( 'woocommerce' ) ) { 
           register_deactivation_hook( __FILE__ , "xs_wpah_plugin_deactivate" );
           echo "<code>Please activate / install  woocommerce plugin first!</code>";
           exit; 
        }    
    }
    // callback functions for plugin_actions_link_pluginpath; 
    function xs_wpah_settings_link( $links ){
        $settings_link = '<a href="admin.php?page=xs_wpah_settings_page">' . __( 'Settings' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    register_activation_hook( __FILE__ , "xs_wpah_check_woocommerce"  );
    // plugin settings link
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) ,'xs_wpah_settings_link' );
      
    /**
    *  including main class located : inc/classes/xs-wpah-main.php
    */
    if ( ! class_exists( 'Xs_Main_Class' ) ) {
        include_once dirname( __FILE__ ) . '/inc/classes/class-xs-wpah-main.php';
        $object_xs_wpah_class = new Xs_Main_Class;
    }