<?php
/**
 * Plugin Name
 *
 * @package           PluginPackage
 * @author            Your Name
 * @copyright         2019 Your Name or Company Name
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Plugin Name
 * Plugin URI:        https://example.com/plugin-name
 * Description:       Description of the plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Your Name
 * Author URI:        https://example.com
 * Text Domain:       plugin-slug
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/my-plugin/
 */



// Prevent direct access to the file
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}



function ms_activate_plugin() {
    add_action('admin_notices', 'ms_activation_notice');
}

// Hook for plugin activation
register_activation_hook(__FILE__, 'ms_activate_plugin');




function ms_deactivate_plugin() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'users'; // Adjust if you're working with a different table

    // Array of custom columns to check and potentially drop
    $columns = ["phone", "address_line1", "address_line2", "city", "state", "zip", "country"];

    foreach ($columns as $column) {
        // Check if the column exists
        $column_exists = $wpdb->get_results($wpdb->prepare(
            "SHOW COLUMNS FROM `$table_name` LIKE %s", 
            $column
        ));

        // If the column exists, drop it
        if (!empty($column_exists)) {
            $wpdb->query("ALTER TABLE `$table_name` DROP COLUMN `$column`");
        }
    }
}


register_deactivation_hook(__FILE__, 'ms_deactivate_plugin');



function ms_activation_notice(){
    ?>
    <div class="notice notice-success is-dismissible">
        <p>Thanks for activating the Membership System plugin!</p>
    </div>
    <?php
}

add_action('admin_notices', 'ms_activation_notice');



// Function to create the admin page
function ms_add_admin_page() {
    add_menu_page(
        'Membership System', // Page title
        'Membership System', // Menu title
        'manage_options', // Capability
        'membership-system', // Menu slug
        'ms_admin_page', // Function to display the page content
        'dashicons-groups'
    );
}

// Hook to add the admin page
add_action('admin_menu', 'ms_add_admin_page');

// Function to display the admin page content
function ms_admin_page() {
    
    global $wpdb; // Access the global database object
    ?>
    <div class="wrap">
        <h2>Membership System</h2>
        <form method="post" action="">
            <div style="display:flex; flex-direction:flex-row;justify-content:space-between;">
                <div class="flex flex-col">
                    <h2>Extend Users Table Details</h2>
                    <div class="flex flex-row">
                        <input type="submit" name="modify_db" class="button button-primary" value="Modify Database"/>
                        <input type="submit" name="reverse_db" class="button" value="Reverse Changes"/>
                    </div>
                </div>
                <div class="flex flex-col">
                    <h2>Title 2</h2>
                    <div class="flex flex-row">
                        <input type="submit" name="modify_db" class="button button-primary" value="Modify Database"/>
                        <input type="submit" name="reverse_db" class="button" value="Reverse Changes"/>
                    </div>
                </div>
                <div class="flex flex-col">
                    <h2>Title 3</h2>
                    <div class="flex flex-row">
                        <input type="submit" name="modify_db" class="button button-primary" value="Modify Database"/>
                        <input type="submit" name="reverse_db" class="button" value="Reverse Changes"/>
                    </div>
                </div>
            </div>
        </form>
        
        <h3>User List</h3>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <!-- Add other headers here -->
                </tr>
            </thead>
            <tbody>
                <?php
                $users = get_users(); // Retrieve an array of user objects.
                foreach ($users as $user) {
                    echo '<tr>';
                    echo '<td>' . esc_html($user->ID) . '</td>';
                    echo '<td>' . esc_html($user->user_login) . '</td>';
                    echo '<td>' . esc_html($user->user_email) . '</td>';
                    
                    // Retrieve user meta data like phone number
                    $phone = get_user_meta($user->ID, 'phone', true);
                    echo '<td>' . esc_html($phone) . '</td>';
                    
                    // Add other user meta data here
                    
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
        
    <?php
    
    function do_alter_database(){
        global $wpdb;
	    $charset_collate = $wpdb->get_charset_collate();
        $sql = "ALTER TABLE " . "aqa4j_users
                ADD COLUMN phone VARCHAR(20) NULL,
                ADD COLUMN address_line1 VARCHAR(255) NULL,
                ADD COLUMN address_line2 VARCHAR(255) NULL,
                ADD COLUMN city VARCHAR(100) NULL,
                ADD COLUMN state VARCHAR(100) NULL,
                ADD COLUMN zip VARCHAR(20) NULL,
                ADD COLUMN country VARCHAR(100) NULL;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $wpdb->query($sql);
    }

     function ms_reverse_changes(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'users'; // Adjust if you're working with a different table

        // Array of custom columns to check and potentially drop
        $columns = ["phone", "address_line1", "address_line2", "city", "state", "zip", "country"];

        foreach ($columns as $column) {
            // Check if the column exists
            $column_exists = $wpdb->get_results($wpdb->prepare(
                "SHOW COLUMNS FROM `$table_name` LIKE %s", 
                $column
            ));

            // If the column exists, drop it
            if (!empty($column_exists)) {
                $wpdb->query("ALTER TABLE `$table_name` DROP COLUMN `$column`");
            }
        }
    }

   

    // Check if 'Modify Database' button was clicked
    if (isset($_POST['modify_db'])) {
        do_alter_database(); // Assuming this function modifies the database
        echo '<div class="updated"><p>Database modified successfully.</p></div>';
    }
    // Check if 'Reverse Changes' button was clicked
    if (isset($_POST['reverse_db'])) {
        ms_reverse_changes(); // You'll need to define this function to reverse the changes
        echo '<div class="updated"><p>Changes reversed successfully.</p></div>';
    }
}




function ms_custom_user_profile_fields($user) {
    ?>
    <h3>Extra User Information</h3>
    <table class="form-table">
        <tr>
            <th><label for="phone">Phone</label></th>
            <td>
                <input type="text" name="phone" id="phone" value="<?php echo esc_attr(get_the_author_meta('phone', $user->ID)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="addressLine1">Address Line 1</label></th>
            <td>
                <input type="text" name="addressLine1" id="addressLine1" value="<?php echo esc_attr(get_the_author_meta('address_line1', $user->ID)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="addressLine2">addressLine2</label></th>
            <td>
                <input type="text" name="addressLine2" id="addressLine2" value="<?php echo esc_attr(get_the_author_meta('address_line2', $user->ID)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="city">City</label></th>
            <td>
                <input type="text" name="city" id="city" value="<?php echo esc_attr(get_the_author_meta('city', $user->ID)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="state">State</label></th>
            <td>
                <input type="text" name="state" id="state" value="<?php echo esc_attr(get_the_author_meta('state', $user->ID)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="zip">Zip Code</label></th>
            <td>
                <input type="text" name="zip" id="zip" value="<?php echo esc_attr(get_the_author_meta('zip', $user->ID)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="country">Country</label></th>
            <td>
                <input type="text" name="country" id="country" value="<?php echo esc_attr(get_the_author_meta('country', $user->ID)); ?>" class="regular-text" />
            </td>
        </tr>
        <!-- Repeat for other fields -->
    </table>
    <?php
}

// Display custom user profile fields
add_action('show_user_profile', 'ms_custom_user_profile_fields');
add_action('edit_user_profile', 'ms_custom_user_profile_fields');

function ms_save_custom_user_profile_fields($user_id) {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();

    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Save custom fields
    update_user_meta($user_id, 'phone', $_POST['phone']);
    update_user_meta($user_id, 'address_line1', $_POST['address_line1']);
    update_user_meta($user_id, 'address_line2', $_POST['address_line2']);
    update_user_meta($user_id, 'city', $_POST['city']);
    update_user_meta($user_id, 'state', $_POST['state']);
    update_user_meta($user_id, 'zip', $_POST['zip']);
    update_user_meta($user_id, 'country', $_POST['country']);
    // Repeat for other fields

   
    $sql = $wpdb->prepare(
        "UPDATE {$wpdb->users}
        SET phone = %s,
        address_line1 = %s,
        address_line2 = %s,
        city = %s,
        state = %s,
        zip = %s,
        country = %s
        WHERE ID = %d",
        $_POST['phone'],
        $_POST['addressLine1'],
        $_POST['addressLine2'],
        $_POST['city'],
        $_POST['state'],
        $_POST['zip'],
        $_POST['country'],
        $user_id
    );
   
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    $wpdb->query($sql);


}
// Save custom user profile fields
add_action('personal_options_update', 'ms_save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'ms_save_custom_user_profile_fields');


// Hook into the 'wp_enqueue_scripts' action to enqueue your JavaScript file
add_action('wp_enqueue_scripts', 'ms_enqueue_scripts');
function ms_enqueue_scripts() {
    wp_enqueue_script('membership-system-js', plugin_dir_url(__FILE__) . 'js/membership-system.js', array('jquery'), '1.0', true);
}






