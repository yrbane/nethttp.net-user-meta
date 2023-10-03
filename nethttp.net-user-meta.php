<?php

/**
 * Plugin Name: nethttp.net-user-meta
 * Plugin URI: https://github.com/yrbane/nethttp.net-user-meta
 * Description: User Meta is a Wordpress plugin whitch is a simple way to store some additional data to users'profile. 
 * Version: 0.0.1
 * Author: Barney <yrbane@nethttp.net>
 * Author URI: https://github.com/yrbane
 * Requires PHP: 7.4
 * Text Domain: default
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:       /languages
 */

namespace nethttp;

use User_meta_type;

include 'user_meta_type.php';

class User_Meta
{
    // Plugin version
    private const PLUGIN_VERSION = '0.0.1';

    // Plugin name
    private const PLUGIN_NAME = 'nethttp.net-user-meta';

    /**
     * The absolute path to the directory containing this plugin file.
     */
    private const PLUGIN_PATH = __DIR__ . '/';

    /**
     * The name of the option that stores the selected net-user-meta.
     */
    private const OPTION = 'nethttp.net-user-meta';

    /**
     * Constructor function that sets up actions to be taken when the plugin is loaded.
     */
    public function __construct()
    {
        // Check if the net-user-meta option is not set and add it if it's not
        if (!get_option(self::OPTION)) {
            add_option(self::OPTION, []);
        }

        // Add a menu link in the WordPress admin panel
        add_action('admin_menu', [$this, 'admin_menu']);

        //Adding style and script
        add_action('admin_enqueue_scripts', [$this, 'styles_and_scripts']);

        // Add hooks to display and save user meta fields
        add_action('show_user_profile', [$this, 'form_field']);
        add_action('edit_user_profile', [$this, 'form_field']);
        add_action('personal_options_update', [$this, 'form_field_update']);
        add_action('edit_user_profile_update', [$this, 'form_field_update']);
    }

    /**
     * Function to display the form field for user meta.
     * @param \WP_User $user The user whose profile is being edited.
     */
    public function form_field($user)
    {
        // Get the list of user meta fields from the option
        $metas = get_option(self::OPTION);

        // Display each user meta field
        foreach ($metas as &$meta) {
            User_meta_type::{$meta['type']}($user, $meta);
        }
    }

    /**
     * Function to save the form field data for user meta.
     * @param object $user The user whose profile is being edited.
     */
    public function form_field_update($user_id)
    {
        // check that the current user have the capability to edit the $user_id
        if (!current_user_can('edit_user', $user_id)) {
            $this->error('You cannot edit user  ' . $user_id . '!');
            return false;
        }

        // Get the list of user meta fields from the option
        $metas = get_option(self::OPTION);

        // Loop through each user meta field and update the user meta value
        foreach ($metas as &$meta) {
            if (isset($_POST[$meta['slug']])) {
                // create/update user meta for the $user_id
                if (update_user_meta(
                    $user_id,
                    $meta['slug'],
                    $_POST[$meta['slug']]
                )) {
                    $this->notice('Meta ' . $meta['slug'] . ' is saved!');
                } else {
                    $this->error('Meta ' . $meta['slug'] . ' is not saved!');
                }
            }
        }
    }

    /**
     * Function to add styles and scripts to the WordPress admin panel.
     * @param string $hook The current admin page.
     */
    public function styles_and_scripts($hook)
    {
        // Check if we are on the plugin settings page
        if ($hook === 'toplevel_page_nethttp.net-user-meta') {
            // Add CSS style
            wp_enqueue_style(self::PLUGIN_NAME . '-css', plugin_dir_url(__FILE__) . '/style.css', [], WP_DEBUG ? time() : self::PLUGIN_VERSION);

            // Add JavaScript
            wp_enqueue_script(self::PLUGIN_NAME . '-js', plugin_dir_url(__FILE__) . '/script.js', ['jquery'], WP_DEBUG ? time() : self::PLUGIN_VERSION, true);
        }
    }

    /**
     * Adds a menu link and page to WordPress admin panel.
     */
    public function admin_menu()
    {
        add_menu_page(
            'nethttp.net user meta', // Title of the page
            'User Meta', // Text to show on the menu link
            'administrator', // Capability requirement to see the link
            'nethttp.net-user-meta', // The 'slug' - file to display when clicking the link,
            [$this, 'settings'], // Callback function to generate page content
            'dashicons-admin-generic' // Icon to display next to the menu item
        );
    }

    /**
     * Display admin form setting to define metas to add
     */
    public function settings()
    {
        if (!empty($_POST)) {
            $this->save($_POST);
        }

        $metas = get_option(self::OPTION);
        $user_meta_types = get_class_methods('User_meta_type');
?>
        <div class="wrap">
            <h1 class="wp-heading-inline">User Metas</h1>
            <button class="page-title-action" id="add-user-meta">Ajouter un groupe</button>
            <form method="post" action="<?php echo admin_url('admin.php?page=nethttp.net-user-meta&action=settings'); ?>">
                <table class="wp-list-table widefat fixed striped table-view-list" id="user-metas"></table>
                <?php submit_button(__('Save Settings', 'textdomain')); ?>
            </form>
        </div>
        <script>
            var userMetas = <?php echo json_encode($metas) ?>;
            var userMetasType = <?php echo json_encode($user_meta_types) ?>;
        </script>
<?php
    }

    /**
     * Save settings
     */
    private function save($data)
    {

        $dataToSave = [];
        foreach ($data['name'] as $k => $value) {
            if (empty($value)) {
                continue;
            }
            if (!isset($data['type'][$k]) || !isset($data['description'][$k])) {
                continue;
            }
            $data['name'][$k] = stripslashes($data['name'][$k]);
            $dataToSave[] = [
                'slug' => sanitize_title($data['name'][$k], '_'),
                'name' => $data['name'][$k],
                'type' => $data['type'][$k],
                'description' => stripslashes($data['description'][$k]),
            ];
        }

        if (update_option(self::OPTION, $dataToSave)) {
            $this->notice('User metas settings saved !');
        } else {
            $this->error('User metas settings not saved or unchanged !');
        }
    }

    /**
     * Outputs a notice message to the user.
     * @param string $msg The message to display.
     * @param string $type The type of notice to display, defaults to 'success'.
     * @return void
     */
    private function notice($msg, $type = 'success')
    {
        echo '<div class="notice notice-' . $type . ' is-dismissible"><p>' . $msg . '</p></div>';
    }

    /**
     * Outputs an error message to the user.
     * @param string $msg The error message to display.
     * @return void
     */
    private function error($msg)
    {
        return $this->notice($msg, 'error');
    }
}

new User_Meta();
