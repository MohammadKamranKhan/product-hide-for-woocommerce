<?php
    /**
     *  class-xs-wpah-main.php
     */
    // Exit if directly access
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    /**
    * Define Class Xs_Main_Class
    */
    class Xs_Main_Class {
        // constructor call
        public function __construct(){
            $this->xs_wpah_constants(); // calling method for defining constants
            $this->xs_wpah_include_files(); // calling method for including files
            $this->xs_wpah_hooks();     // hooks initilization
        }
        // Method for Constants
        public function xs_wpah_constants(){
            define( 'XS_WPAH_ABSPATH' , dirname( XS_WPAH_PATH ) );
            define( 'XS_WPAH_BASENAME' , plugin_basename( XS_WPAH_PATH ) );
        }
        // Method for Including all files of pluing
        public function xs_wpah_include_files(){
            // including file path; inc/classes/class-xs-wpah-setup.php
            include_once XS_WPAH_ABSPATH . '/inc/classes/class-xs-wpah-setup.php';
            // including templates file for admin settings page; 
            // path; inc/functions/xs-wpah-functions.php
            include_once XS_WPAH_ABSPATH . '/inc/functions/xs-wpah-functions.php';
        }
        /**
         *  Method for Hooks
         *  All callback functions for hooks are available in inc/classes/class-xs-wpah-setup.php
         */
        public function xs_wpah_hooks(){
            // object declaration of class Xs_Wpah_Setup_Class
            // Xs_Wpah_Setup_Class is avilable in inc/classes/class-xs-wpah-setup.php 
            $obj_xs_wpah_setup = new Xs_Wpah_Setup_Class;
            // CSS and JS include files
            add_action( 'wp_enqueue_scripts', array(  $obj_xs_wpah_setup ,'xs_wpah_inc_files' ) );
            // save_post hook for cheking if the requested products or out of stock or not
            // if in stock then send email
            add_action( 'save_post',  array(  $obj_xs_wpah_setup ,'xs_wpah_save_posts' ) );
            // creating custom post type 
            add_action( 'init', array(  $obj_xs_wpah_setup, 'xs_wpah_custom_post_type' ) );
            // adding main menu page hooks
            add_action( "admin_menu", array( $obj_xs_wpah_setup ,"xs_wpah_add_register_menu") );
            // Admin Init settings API
            add_action( 'admin_init', array(  $obj_xs_wpah_setup, 'xs_wpah_register_settings') ); 
            // adding template contact form
            add_filter( 'template_include', array(  $obj_xs_wpah_setup, 'xs_wpah_templates_include' ) );
            // ajax call for popup form submission
            add_action( 'wp_ajax_xs_wpah_popup_form', array( $obj_xs_wpah_setup,'xs_wpah_popup_form'));
            add_action( 'wp_ajax_nopriv_xs_wpah_popup_form', array( $obj_xs_wpah_setup,'xs_wpah_popup_form'));
            // getting user selected options in settings page
            $xp_wpah_selection = get_option("xs_wpah_options","hide_products");
            if( $xp_wpah_selection["xs_wpah_user_selection"]  == "hide_products"){
                // if hide products selected then hide products
                add_action( 'pre_get_posts', array(  $obj_xs_wpah_setup, 'xs_wpah_hide_all_products' ) );
            }
            elseif(  $xp_wpah_selection["xs_wpah_user_selection"]  == "show_button"){
                // add contact button on shop page.
                add_action('woocommerce_loop_add_to_cart_link', array(  $obj_xs_wpah_setup,'xs_wpah_notify_shop_page'), 50,2 );
                // for single product page
                add_filter('woocommerce_get_availability', array(  $obj_xs_wpah_setup , 'xs_wpah_notify_single_page' ) , 1, 2);
            }
        }

        
    }