<?php

/** * Plugin Name: BuddyForms Anonymous Author * Plugin URI: http://buddyforms.com/downloads/buddyforms-anonymous-author/ * Description: This BuddyForms Extension allows you to select a default Author and give your users the option to publish Anonymous under the default Author. * Version: 1.1 * Author: ThemeKraft * Author URI: https://themekraft.com/buddyforms/ * License: GPLv2 or later * Network: false * ***************************************************************************** * * This script is free software; you can redistribute it and/or modify * it under the terms of the GNU General Public License as published by * the Free Software Foundation; either version 2 of the License, or * (at your option) any later version. * * This program is distributed in the hope that it will be useful, * but WITHOUT ANY WARRANTY; without even the implied warranty of * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the * GNU General Public License for more details. * * You should have received a copy of the GNU General Public License * along with this program; if not, write to the Free Software * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA * *****************************************************************************/
//
// Require all needed files
//
if ( !defined( 'BUDDYFORMS_ANONYMOUS_INSTALL_PATH' ) ) {
    /**	 * Define the install path	 */
    define( 'BUDDYFORMS_ANONYMOUS_INSTALL_PATH', dirname( __FILE__ ) . '/' );
}
if ( !defined( 'BUDDYFORMS_ANONYMOUS_INCLUDES_PATH' ) ) {
    /**	 * Define the include path	 */
    define( 'BUDDYFORMS_ANONYMOUS_INCLUDES_PATH', BUDDYFORMS_ANONYMOUS_INSTALL_PATH . 'includes/' );
}
require BUDDYFORMS_ANONYMOUS_INCLUDES_PATH . '/form-elements.php';
require BUDDYFORMS_ANONYMOUS_INCLUDES_PATH . '/functions.php';
//
// Check the plugin dependencies
//
add_action(
    'init',
    function () {
    // Only Check for requirements in the admin
    if ( !is_admin() ) {
        return;
    }
    // Require TGM
    require dirname( __FILE__ ) . '/includes/resources/tgm/class-tgm-plugin-activation.php';
    // Hook required plugins function to the tgmpa_register action
    add_action( 'tgmpa_register', function () {
        // Create the required plugins array
        
        if ( !defined( 'BUDDYFORMS_PRO_VERSION' ) ) {
            $plugins['buddyforms'] = array(
                'name'     => 'BuddyForms',
                'slug'     => 'buddyforms',
                'required' => true,
            );
            $config = array(
                'id'           => 'buddyforms-tgmpa',
                'parent_slug'  => 'plugins.php',
                'capability'   => 'manage_options',
                'has_notices'  => true,
                'dismissable'  => false,
                'is_automatic' => true,
            );
            // Call the tgmpa function to register the required plugins
            tgmpa( $plugins, $config );
        }
    
    } );
},
    1,
    1
);
//
// Create a helper function for easy SDK access.
//
function buddyforms_aa_fs()
{
    global  $buddyforms_aa_fs ;
    
    if ( !isset( $buddyforms_aa_fs ) ) {
        // Include Freemius SDK.
        
        if ( file_exists( dirname( dirname( __FILE__ ) ) . '/buddyforms/includes/resources/freemius/start.php' ) ) {
            // Try to load SDK from parent plugin folder.
            require_once dirname( dirname( __FILE__ ) ) . '/buddyforms/includes/resources/freemius/start.php';
        } else {
            if ( file_exists( dirname( dirname( __FILE__ ) ) . '/buddyforms-premium/includes/resources/freemius/start.php' ) ) {
                // Try to load SDK from premium parent plugin folder.
                require_once dirname( dirname( __FILE__ ) ) . '/buddyforms-premium/includes/resources/freemius/start.php';
            }
        }
        
        $buddyforms_aa_fs = fs_dynamic_init( array(
            'id'             => '1024',
            'slug'           => 'buddyforms-anonymous-author',
            'type'           => 'plugin',
            'public_key'     => 'pk_6b2da28e8be45961bcc58e88c894d',
            'is_premium'     => false,
            'has_paid_plans' => false,
            'parent'         => array(
            'id'         => '391',
            'slug'       => 'buddyforms',
            'public_key' => 'pk_dea3d8c1c831caf06cfea10c7114c',
            'name'       => 'BuddyForms',
        ),
            'menu'           => array(
            'slug'       => 'buddyforms',
            'first-path' => 'edit.php?post_type=buddyforms&page=buddyforms_welcome_screen',
            'support'    => false,
        ),
            'is_live'        => true,
        ) );
    }
    
    return $buddyforms_aa_fs;
}

function buddyforms_aa_fs_is_parent_active_and_loaded()
{
    // Check if the parent's init SDK method exists.
    return function_exists( 'buddyforms_core_fs' );
}

function buddyforms_aa_fs_is_parent_active()
{
    $active_plugins_basenames = get_option( 'active_plugins' );
    foreach ( $active_plugins_basenames as $plugin_basename ) {
        if ( 0 === strpos( $plugin_basename, 'buddyforms/' ) || 0 === strpos( $plugin_basename, 'buddyforms-premium/' ) ) {
            return true;
        }
    }
    return false;
}

function buddyforms_aa_fs_init()
{
    
    if ( buddyforms_aa_fs_is_parent_active_and_loaded() ) {
        // Init Freemius.
        buddyforms_aa_fs();
        // Parent is active, add your init code here.
    } else {
        // Parent is inactive, add your error handling here.
    }

}


if ( buddyforms_aa_fs_is_parent_active_and_loaded() ) {
    // If parent already included, init add-on.
    buddyforms_aa_fs_init();
} else {
    
    if ( buddyforms_aa_fs_is_parent_active() ) {
        // Init add-on only after the parent is loaded.
        add_action( 'buddyforms_core_fs_loaded', 'buddyforms_aa_fs_init' );
    } else {
        // Even though the parent is not activated, execute add-on for activation / uninstall hooks.
        buddyforms_aa_fs_init();
    }

}
