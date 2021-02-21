<?php 
  /**
     *  class-xs-wpah-setup.php
     */
    // Exit if directly access
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    function xs_wpah_settings_page_cb(){
        ?>
        <h3>Woocommerce Products Auto Hide</h3>
        <form action="options.php" method="post">
            <?php 
                // Settings Error
                settings_errors();
                // getting settings by name get_option(settings_option_name)
                $xs_wpah_setting_array = get_option("xs_wpah_options",true);
                // settings fields
                settings_fields("xs_wpah_option_group");
                // do_settings_sections
                do_settings_sections("xs_wpah_option_group");
                

            ?>

            <div>
                <p>
                    <strong><code>Hide Products:</code></strong>
                    If you select this option then all out of stock products in woocommerce will be hidden.
                </p>
            </div>

            <div>
                <p>
                    <strong><code>Show notify button:</code></strong>
                    If you select this option then a button on out of stock products will be shown. As user clicks 
                    user have to provide contact details. When product is in inventory then notification about product will
                    be sent to user.
                </p>
            </div>

            <div>
                <strong>Select an option for wocommerce out of stock products </strong>
            </div>

            <div>
                <br>
                <input type="radio" name="xs_wpah_options[xs_wpah_user_selection]" 
                <?php echo ( isset( $xs_wpah_setting_array["xs_wpah_user_selection"] ) && $xs_wpah_setting_array["xs_wpah_user_selection"]  == "hide_products" ) ? "checked='checked'"  : '' ?>
                value="hide_products" title="Hide all out of stock products"  checked='checked'> Hide Products
                <br><br>
                <input type="radio" name="xs_wpah_options[xs_wpah_user_selection]" 
                <?php echo ( isset( $xs_wpah_setting_array["xs_wpah_user_selection"] ) &&  $xs_wpah_setting_array["xs_wpah_user_selection"]  == "show_button" ) ? "checked='checked'"  : '' ?>
                value="show_button" title="Show notify button on out of stock products"  > Show Notify Button
                <br>
                
            </div>
           
            <!-- Submit Button -->
            <?php submit_button(); ?>
        </form>
        <?php
    }

  