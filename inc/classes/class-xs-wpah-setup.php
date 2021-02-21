<?php
    /**
     *  class-xs-wpah-setup.php
     */
    // Exit if directly access
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    /**
    * Define Class Xs_Wpah_Setup_Class
    * Hooks callback functions are avilable in this class
    * All hook are available in inc/classes/class-xs-wpah-main.php in xs_wpah_hooks() function
    */
    class Xs_Wpah_Setup_Class {
        // cb for wp_enqueue_scripts
        public function xs_wpah_inc_files(){
            wp_enqueue_style( 'xs-wpah-main' , plugins_url( '/woocommerce-product-auto-hide/assets/css/style.css'),array(),false ) ;
            wp_enqueue_script('xs-wpah-main', plugins_url( '/woocommerce-product-auto-hide/assets/js/xs-wpah-main.js'),array('jquery'), '', false);
            $translation_array = array(
                'ajax_url' => admin_url( 'admin-ajax.php' )
            );
            wp_localize_script( 'xs-wpah-main', 'xs_wpah_ajax_object', $translation_array );
        }
        // callback function for; admin_menu ; 
        public function xs_wpah_add_register_menu(){
            // registering menu
            add_menu_page(
                "WC Product Auto Hide",  // page  title
                "WC Product Auto Hide",   // menu title
                "manage_options", // capability
                "xs_wpah_settings_page", // plugin admin page slug
                "xs_wpah_settings_page_cb", // function (inc/functions/xs-wpah-functions.php) to be executed on activating plugin"
                "dashicons-products", // menu icon 
                40  // position of plugin in dashboard
            );
        }
        // callback function for ; 
        // Settings API 
        public function xs_wpah_register_settings(){
            register_setting( 'xs_wpah_option_group', 'xs_wpah_options');
        }
        // Callback function for ; init hook
        public function xs_wpah_custom_post_type(){
            $labels = array(
                'name'               => _x( 'WC Products', 'WC Products' ),
                'singular_name'      => _x( 'WC Product', 'WC Product' ),
                'add_new'            => _x( 'Add New', 'xs_wpah' ),
                'add_new_item'       => __( 'Add New Product' ),
                'edit_item'          => __( 'Edit Product' ),
                'new_item'           => __( 'New Product' ),
                'all_items'          => __( 'All Products' ),
                'view_item'          => __( 'View Product' ),
                'search_items'       => __( 'Search Products' ),
                'not_found'          => __( 'No products found' ),
                'not_found_in_trash' => __( 'No products found in the Trash' ), 
                'menu_name'          => 'WC Products'
              );
            $args = array(
                'labels'        => $labels,
                'description'   => 'Holds our products and product specific data',
                'public'        => true,
                'menu_position' => 25,
                'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
                'has_archive'   => true,
              );
            register_post_type( 'wc_products', $args );
        }

        // callback function of ; pre_get_posts; Hide all products (out of stock)
        public function xs_wpah_hide_all_products( $query ){
            if ( $outofstock_term = get_term_by( 'name', 'outofstock', 'product_visibility' ) ) {
                $tax_query = (array) $query->get('tax_query');
                $tax_query[] = array(
                    'taxonomy' => 'product_visibility',
                    'field' => 'term_taxonomy_id',
                    'terms' => array( $outofstock_term->term_taxonomy_id ),
                    'operator' => 'NOT IN'        
                );
                $query->set( 'tax_query', $tax_query );
            }
        
        }
        // callback function for ; woocommerce_after_shop_loop_item
        public function xs_wpah_notify_shop_page( $args ){
            global $product;
            global $post;
            if( $product && !$product->is_in_stock() ){
                
                return '
                    
                   <div class="xs-wpah-parent-popup">
                       <div class="popup popup-class" id="pdt-'.$product->get_id().'">
                       
                            <div class="overlay"></div>
                            <div class="content">
                                <div class="close-btn" id="pdt-'.$product->get_id().'" >&times;</div>
                                <h1>'.$product->get_title().'</h1>
                                <table>
                                        <tr >
                                            <td> 
                                            <input type="hidden" id="xs-wpah-product-id" value="'.$product->get_id().'" /> 
                                            
                                            
                                            </td>
                                            <div class="alert">
                                                <span class="closebtn" id="xs-wpah-closable-btn">
                                                    X
                                                </span>
                                                <span class="xs-wpah-message"></span>
                                            </div>
                                        
                                        </tr>
                                        <tr>
                                            <td class="xs-wpah-row"> Name  : </td>
                                            <td class="xs-wpah-row"> <input id="xs-wpah-user-name-'.$product->get_id().'"   type="text" size="40" placeholder="Please enter your Name" >  
                                        </tr>
                                        <tr>
                                            <td class="xs-wpah-row"> Email   : </td>
                                            <td class="xs-wpah-row"> <input type="email" id="xs-email-'.$product->get_id().'"   size="40" placeholder="Please enter your email" >  
                                        </tr>
                                        <tr>
                                        <td> </td>
                                        <td class="xs-wpah-row">
                                                <button data-id="'.$product->get_id().'"  class="xs-wpah-btn2" >Submit</button>
                                        </td>
                                        </tr> 
                            
                                </table>
                         </div>
                     </div>
                 </div> 
                 <button class="xs-wpah-btn click-class" data-id="pdt-'.$product->get_id().'" >Notify When Available</button>
                  
                
                ';
            }
            return $args;
        }
        // // callback function for ; woocommerce_get_availability
        public function xs_wpah_notify_single_page( $availability, $product ){
            // Change Out of Stock Text
            global $post;
            if (!$product->is_in_stock()) {
                echo '
                
                    <div class="xs-wpah-parent-popup">
                        <div class="popup popup-class" id="pdt-'.$product->get_id().'">
                            <div class="overlay"></div>
                            <div class="content">
                                <div class="close-btn" id="pdt-'.$product->get_id().'" >&times;</div>
                                <h1>'.$product->get_title().'</h1>
                                <table>
                                    <tr >
                                        <td> 
                                        <input type="hidden"  id="xs-wpah-product-id" value="'.$product->get_id().'" />   
                                        
                                        </td>
                                        <div class="alert">
                                            <span class="closebtn" id="xs-wpah-closable-btn">
                                                X
                                            </span>
                                            <span class="xs-wpah-message"></span>
                                        </div>
                                    </tr>
                                    <tr>
                                        <td class="xs-wpah-row"> Name  : </td>
                                        <td class="xs-wpah-row"> <input type="text" id="xs-wpah-user-name-'.$product->get_id().'"  size="40" placeholder="Please enter your name" />  
                                    </tr>
                                    <tr>
                                        <td class="xs-wpah-row"> Email   : </td>
                                        <td class="xs-wpah-row"><input type="email"  id="xs-email-'.$product->get_id().'"  size="40" placeholder="Please enter your email" />  
                                    </tr>
                                    <tr>
                                    <td> </td>
                                    <td class="xs-wpah-row">
                                            <button data-id="'.$product->get_id().'" class="xs-wpah-btn2" >Submit</button>
                                    </td>
                                    </tr> 
                                
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <button class="xs-wpah-btn click-class" data-id="pdt-'.$product->get_id().'" >Notify When Available</button>
                   ';
            }
            return $availability;
        }
        // adding callback function for templates_include
        public function xs_wpah_templates_include( $template ){
            $template = XS_WPAH_ABSPATH .'/template/page-xs-wpah-contact-form.php'; 
            return $template;  
        }  
        // callback function for save_post; just for sending email if requeted products or in stock or not
        public function xs_wpah_save_posts( $product_id ){
            
            global $wpdb;
            global $product;
            // getting admin email
            $admin_email = get_option('admin_email');
            $product_xs = wc_get_product( $product_id );
            $xs_product_name = $product_xs->get_title();
            // getting product permalink
            $xs_wpah_product_url = get_permalink( $product_id );
            $xs_email_message = "You requested for a product ".$xs_product_name." is now available.<br>
             Click here to <a href='$xs_wpah_product_url' href='_blank' >Buy </a>";
            
            // getting post type 
            if( get_post_type( $product_id ) == "product"  ){
                // checking if product is out of stock
                $email_check_status = true;
                $table_name = $wpdb->prefix."wc_product_meta_lookup";
                $product_status = $wpdb->get_var("SELECT stock_status FROM $table_name   WHERE product_id='$product_id' ");
                if( $product_status == "outofstock"){
                   // gettin IDs of all custom post type wc_products
                    $post_ids = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE  post_type='wc_products';",'ARRAY_A');
                    // if return result is array and is not empty
                    if ( is_array( $post_ids ) && !empty($post_ids) ) {
                        // checking return IDs in post meta table for current product ID
                        foreach( $post_ids as $post ){
                            if( get_post_meta( $post['ID'] ,'xs_wpah_product_id',true ) == $product_id ){
                                // getting email of the pending request for out of stock product
                                $post_email = $wpdb->get_results("SELECT post_title FROM $wpdb->posts WHERE ID=".$post['ID']."  AND post_type='wc_products';",'ARRAY_A');
                                foreach( $post_email as $post_emails) {
                                    // false means email was not sent before
                                    if( get_post_meta($post['ID'],'xs_wpah_email_sent_status',true) == "false" ){
                                        if( empty( $admin_email ) ){
                                              echo "<script>alert('Please set admin email first!')</script>";
                                        }
                                        else {
                                            // wp_mail( $to, $subject, $message, $headers, $attachments )
                                            if( mail( $post_emails['post_title'], "Product Available ", $xs_email_message) ) {
                                                // updating meta data of email
                                                update_post_meta( $post['ID'],'xs_wpah_email_sent_status','true' );
                                                // true means the email sent for this product
                                            }
                                            else {
                                                $email_check_status =false;
                                                exit;
                                            }
                                        }
                                    }
                                }
                                // confirming all emails sent or not
                                if($email_check_status == true) {
                                    echo "<script>alert('All emails sent successfully!')</script>";
                                }
                                else {
                                    echo "<script>alert('Emails could not be sent!')</script>";
                                }
                            }

                        }
                    }
                }
                
                
            }
            
        }
        // getting popup form data from ajax hooks; wp_ajax_ and wp_ajax_nopriv
        public function xs_wpah_popup_form(){
            // getting data from jquery method. after popup sbmit button clicked
            $xs_product_id = esc_html($_POST['xs_product_id']);
            $xs_username   = esc_html($_POST['xs_username']);
            $xs_email      = esc_html($_POST['xs_email']);
            // if same emil and product has been requested again then 
            // deny to accept request
            global $wpdb;
            $postid = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_title = '" . $xs_email . "' AND post_type='wc_products';",'ARRAY_A');
            if( is_array($postid) && !empty($postid) ){
                foreach($postid as $post){
                    // checking if corresponding post id and product id are present in wp post meta table
                    // if ids match and email sent status is fale , then it means no need to regiter new query
                    if( ( get_post_meta($post['ID'],'xs_wpah_product_id',true) == $xs_product_id ) && ( get_post_meta($post['ID'],'xs_wpah_email_sent_status',true) == "false" )   ){
                        echo "product_exists";
                        die();
                    }
                    // else if post id and product match then see if email sent status true or false
                    // if true it means email was sent before and now product is out of stock agian and same user requesting for same product
                    elseif( ( get_post_meta($post['ID'],'xs_wpah_product_id',true) == $xs_product_id ) && ( get_post_meta($post['ID'],'xs_wpah_email_sent_status',true) == "true" ) ){
                        // updating email status to false so that when product is again in stock , email can be sent again
                        update_post_meta($post['ID'],'xs_wpah_email_sent_status',"false" );
                    }
                }
            }
            
            // preparing to insert data in dabase
            $xs_wpah_custom_post_data = array(
                'post_title'   => esc_html($xs_email),
                'post_content' => '',
                'post_status'  => 'publish',
                'post_author'  => get_current_user_id(),
                'post_type'    => 'wc_products',
                'meta_input'   => array(
                    'xs_wpah_product_id'         => esc_html($xs_product_id),
                    'xs_wpah_user_name'          => esc_html($xs_username),
                    'xs_wpah_email'              => esc_html($xs_email),
                    'xs_wpah_email_sent_status'  => "false"
                ),
            );
            
            
            // inserting data in database
            if ( wp_insert_post($xs_wpah_custom_post_data) ) {
                echo "success"; // return true in onSuccess jquery method
            }
            else {
                echo "failed";  // return failed in onSuccess jquery method
            }  
            die();
        }
        
        
        
    }