<?php
/** 
 * plugin name: Give - Cancel Subscription
 * description: Ask reason before cancelling the Give Recurring Donation.
 * Author: Vishal Tanwar
 * Version: 0.0.1
 * text domain: cancel-subscription-for-give   
*/

// denied Direct Access
if( !defined('ABSPATH') ) exit('Don\'t try to rob');

// Basic Plugin Constants

if ( ! defined( 'GIVE_RECURRING_CANCELLATION_PLUGIN_FILE' ) ) {
	define( 'GIVE_RECURRING_CANCELLATION_PLUGIN_FILE', __FILE__ );
}
if( ! defined( 'GIVE_RECURRING_CANCELLATION_DIR' ) ){
    define('GIVE_RECURRING_CANCELLATION_DIR',plugin_dir_path( GIVE_RECURRING_CANCELLATION_PLUGIN_FILE ) );
}
if( ! defined( 'GIVE_RECURRING_CANCELLATION_URL' ) ){
    define('GIVE_RECURRING_CANCELLATION_URL',plugin_dir_url( GIVE_RECURRING_CANCELLATION_PLUGIN_FILE ) );
}

if( !defined( 'GIVE_RECURRING_CANCELLATION_INCLUDE_DIR') ){
    define('GIVE_RECURRING_CANCELLATION_INCLUDE_DIR', GIVE_RECURRING_CANCELLATION_DIR .'/includes' );
}

// Check If give-recurring is active
add_action( 'admin_notices', 'give_validate_recurring_activation' );

// Callback for give recurring validation
function give_validate_recurring_activation(){
    if( ! class_exists ('Give_Recurring') ){
        echo "<div class='error'><p>". sprintf('<strong>Activation Error:</strong> You must have <a href="%1$s"> Give - Recurring Donations</a> installed or active.', 'https://givewp.com/addons/recurring-donations/') . "</p></div>";
    }
}

// Override Cancel Subscription Template 
function csfg_override_shortcode_subscription_template( $paths ){
    $paths[49] = GIVE_RECURRING_CANCELLATION_DIR . '/templates';

    return $paths;
}
add_filter( 'give_template_paths', 'csfg_override_shortcode_subscription_template' );

// Enqueue Style and Subscriptions for the Confirmation Modal 

function csfg_scripts(){
    // Resgister Style
    wp_register_style('give-cancel-subscription', GIVE_RECURRING_CANCELLATION_URL . '/assets/css/cancel-subscription-confirmation.css', array(), time() );
    // Load Style on Page
    wp_enqueue_style('give-cancel-subscription');
    // Register Script
    wp_register_script('give-cancel-subscription', GIVE_RECURRING_CANCELLATION_URL . '/assets/js/cancel-subscription-confirmation.js', array(), time(), true );
    // Load JS on page
    wp_enqueue_script('give-cancel-subscription');
}

add_action( 'wp_enqueue_scripts', 'csfg_scripts', 15 );

// Include Files

include_once GIVE_RECURRING_CANCELLATION_INCLUDE_DIR . '/process-cancel-subscription.php';
