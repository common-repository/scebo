<?php

/*
  Plugin Name: Scebo - Customer Support
  Plugin URI: http://www.themeskingdom.com
  Description: Customer Support Plugin
  Version: 1.5b1
  Author: Themes Kingdom
  Author URI: http://www.themeskingdom.com
  License: GPL2
*/

ob_start();

$file = dirname(__FILE__) . '/index.php';
$plugin_path = plugin_dir_path($file);

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Adding custom post types
require( $plugin_path . 'inc/post-types.php');

// Adding columns to the tickets table
require( $plugin_path . 'inc/taxonomies.php');

// Adding custom meta boxes
require( $plugin_path . 'inc/meta-boxes.php');

// Adding columns to the tickets table
require( $plugin_path . 'inc/tickets-columns.php');

// Adding shortcodes pages
require($plugin_path . 'shortcodes/add-new-ticket.php');
require($plugin_path . 'shortcodes/recent-support-tickets.php');

//Plugin installation
register_activation_hook(__FILE__, 'scb_plugin_installation');

function scb_plugin_installation() {
    include("inc/install.php");
}

//Get current URL
function scb_get_current_url() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

// Error Log
if(!function_exists('_log')){
  function _log( $message ) {
    if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}

// Adding custom admin css
function scb_add_admin_style() {
    wp_enqueue_style('scb_admin_css', plugin_dir_url(__FILE__) . 'inc/style/admin-style.css');
    wp_print_styles('scb_admin_css');
}

// Adding custom admin css
function scb_add_admin_customized_style() {
    global $post_type;
    global $pagenow;

    if ($pagenow == 'edit.php') {
        if (isset($_GET['post_type'])) {
            if (isset($_GET['post_type']) && (($_GET['post_type'] == 'tickets') || ($post_type == 'tickets') ) || (($_GET['post_type'] == 'prewritten-responses') || ($post_type == 'prewritten-responses') )) {//prewritten-responses
                wp_enqueue_style('scb_admin_customized_css', plugin_dir_url(__FILE__) . 'inc/style/admin-customized-style.css');
                wp_print_styles('scb_admin_customized_css');
            }
        }
    }
}

// Adding custom admin css
function scb_add_admin_style_single_ticket() {
    global $post_type;
    global $pagenow;

    if ($pagenow == 'post.php') {
    wp_enqueue_style('scb_admin_css_single_ticket', plugin_dir_url(__FILE__) . 'inc/style/admin-single-ticket-style.css');
    wp_print_styles('scb_admin_css_single_ticket');
    }
}

add_action('admin_head', 'scb_add_admin_customized_style', 30);
add_action('admin_head', 'scb_add_admin_style');
add_action('admin_head', 'scb_add_admin_style_single_ticket');

// Adding custom theme css
function scb_theme_admin_style() {
    wp_enqueue_style('scb_theme_css', plugin_dir_url(__FILE__) . 'inc/style/style.css');
    wp_print_styles('scb_theme_css');
}

add_action('wp_head', 'scb_theme_admin_style');

add_shortcode('add-new-ticket', 'scb_add_new_ticket_shortcode');
add_shortcode('recent-support-tickets', 'scb_recent_support_tickets_shortcode');

// Adding WordPress administration menu
function scb_create_menu() {

    add_menu_page('Scebo Options', 'Scebo Options', 'manage_options', 'scb_admin_welcome', 'scb_admin_welcome', plugin_dir_url(__FILE__) . '/inc/style/img/settings-icon.png');
    add_submenu_page('scb_admin_welcome', 'Notifications', 'Notifications', 'manage_options', 'scb_admin_notifications', 'scb_admin_notifications');
    //add_submenu_page( 'scb_admin_welcome', 'Scebo Help', 'Scebo Help', 'manage_options', 'scb_admin_help_page', 'scb_admin_help_page');//Will add later
}

add_action('admin_menu', 'scb_create_menu');

// Adding WordPress administration pages
function scb_admin_welcome() {
    include("admin-pages/admin-options.php");
}

function scb_admin_help_page() {
    include("admin-pages/admin-help.php");
}

function scb_admin_notifications() {
    include("admin-pages/admin-notifications.php");
}

// Adding tickets details to the theme single page
add_filter('the_content', 'scb_tickets_content');

function scb_tickets_content($content) {
    global $post;
    // Get ticket status
    $terms = wp_get_post_terms($post->ID, 'ticket-status');
    foreach ($terms as $term) {
        $scb_ticket_status = $term->name;
    }

    // Get assigned agent info
    $user_info = get_userdata(get_post_meta($post->ID, 'assigned_agent', true));

    // Check if Scebo Agents Extension is active
    $plugins = get_option('active_plugins');
    $required_plugin = 'scebo-agents-extension/index.php';
    $debug_queries_on = FALSE;
    $plugin = plugin_basename(__FILE__);

    if (in_array($required_plugin, $plugins)) {
        @$scb_assigned_user = '  /  <span>Assigned Agent:</span> ' . $user_info->display_name . '';
    } else {
        $scb_assigned_user = '';
    }

    $terms = wp_get_post_terms($post->ID, 'ticket-status');
    foreach ($terms as $term) {
        $scb_ticket_status = $term->name;
        $t_id = $term->term_id;
    }

    $term_meta = @get_option("taxonomy_$t_id");
    $scb_status_color = esc_attr($term_meta['status_color']) ? esc_attr($term_meta['status_color']) : '';


    // Ticket meta adding to the theme single page
    if (is_singular('tickets')) {
        $content = '<div class="tickets-meta"><div class="tickets-meta-date">'. get_the_time('F j, Y') .'</div><div class="tickets-meta-id">Ticket ID: #'. get_the_ID() .'</div><div class="tickets-meta-status" style="background:'. $scb_status_color .' !important;" >'. $scb_ticket_status. '</div></div>' . $content . '';
    }

    return $content;
}

// Preventing users from accessing wp-admin
add_action('init', 'scb_user_access');

function scb_user_access() {
    if (is_admin() && !current_user_can('edit_published_posts')) {
        wp_redirect(home_url());
        exit;
    }
}

// Preventing users from accessing other tickets
add_action('template_redirect', 'scb_user_tickets_access');

function scb_user_tickets_access() {

    global $post;

    $scb_current_user = get_current_user_id();
    $post_author_ID = get_the_author_meta('ID', $post->post_author);
    if (is_singular('tickets') && $scb_current_user != $post_author_ID) {
        wp_redirect(wp_redirect(get_bloginfo('wpurl') . '/wp-login.php?redirect_to=' . scb_get_current_url()));
        die();
    }
}


// Notify post author about ticket update
function scb_ticket_author_notification($commentID) {

    global $wpdb;

    $comment = get_comment($commentID);
    $post = get_post($comment->comment_post_ID);
    $user = get_userdata($post->post_author);
    $scb_ticket_link = get_post_permalink($post->ID);
    $scb_ticket_id = $post->ID;

    $scb_ticket_author_id = $user->ID;
    $commentator_id = get_comment($commentID)->user_id;

    if ($scb_ticket_author_id == $commentator_id) {
        $open_status = get_term( get_option('scb_open_status'), 'ticket-status' );
        wp_set_object_terms($post->ID, $open_status->name, 'ticket-status', false);
        update_post_meta($post->ID, 'ticket-status', $open_status->term_id);
    }else{
        $pending_status = get_term( get_option('scb_pending_status'), 'ticket-status' );
        wp_set_object_terms($post->ID, $pending_status->name, 'ticket-status', false);
        update_post_meta($post->ID, 'ticket-status', $pending_status->term_id);
    }

    if ($scb_ticket_author_id != $commentator_id) {

        $scb_ticket_author_email = $user->user_email;
        $scb_ticket_author_first_name = $user->user_firstname;
        $scb_ticket_author_last_name = $user->user_lastname;
        $scb_site_name = get_bloginfo('name');
        $scb_site_email = get_bloginfo('admin_email');
        $scb_site_login_link = '' . home_url() . '/wp-login.php';

        $headers = 'From: ' . $scb_site_name . ' <' . $scb_site_email . '>' . "\r\n";

        $subject = 'Ticket (#' . $scb_ticket_id . ') - Update';

        $scb_ticket_update_notification = get_option('scb_ticket_update_notification_email');

        $scb_replace_what = array("{sitename}", "{authorfirstname}", "{authorlastname}", "{siteloginlink}", "{ticketurl}", "{ticketid}");
        $scb_replace_with = array($scb_site_name, $scb_ticket_author_first_name, $scb_ticket_author_last_name, $scb_site_login_link, '<a href="' . $scb_ticket_link . '">' . $scb_ticket_link . '</a>', $scb_ticket_id);

        $scb_ticket_update_notification = str_replace($scb_replace_what, $scb_replace_with, $scb_ticket_update_notification);

        add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));

        @wp_mail($scb_ticket_author_email, $subject, nl2br($scb_ticket_update_notification), $headers);
    }
}

add_action('comment_post', 'scb_ticket_author_notification');

// Envato additional fields place holder function
function envato_additional_fields() {
    do_action('envato_additional_fields_hook');
}

// Envato check validity place holder function
function scb_additional_validity_check() {
    do_action('scb_additional_validity_check_hook');
}

// Removing tickets meta boxes from ticket edit page
add_action( 'add_meta_boxes', 'scb_remove_tickets_meta_boxes' );

function scb_remove_tickets_meta_boxes() {

    /* Publish meta box. */
    remove_meta_box( 'submitdiv', 'tickets', 'normal' );

    /* Author meta box. */
    remove_meta_box( 'authordiv', 'tickets', 'normal' );

    /* Slug meta box. */
    remove_meta_box( 'slugdiv', 'tickets', 'normal' );

    /* Post tags meta box. */
    remove_meta_box( 'tagsdiv-post_tag', 'tickets', 'side' );

    /* Category meta box. */
    remove_meta_box( 'categorydiv', 'tickets', 'side' );

    /* Excerpt meta box. */
    remove_meta_box( 'postexcerpt', 'tickets', 'normal' );

    /* Post format meta box. */
    remove_meta_box( 'formatdiv', 'tickets', 'normal' );

    /* Trackbacks meta box. */
    remove_meta_box( 'trackbacksdiv', 'tickets', 'normal' );

    /* Custom fields meta box. */
    remove_meta_box( 'postcustom', 'tickets', 'normal' );

    /* Featured image meta box. */
    remove_meta_box( 'postimagediv', 'tickets', 'side' );

    /* Page attributes meta box. */
    remove_meta_box( 'pageparentdiv', 'tickets', 'side' );

    /* Page attributes meta box. */
    remove_meta_box( 'ticket-categorydiv', 'tickets', 'side' );

    /* Page attributes meta box. */
    remove_meta_box( 'ticket-prioritydiv', 'tickets', 'side' );

    /* Page attributes meta box. */
    remove_meta_box( 'ticket-statusdiv', 'tickets', 'side' );

    /* Page attributes meta box. */
    //remove_meta_box( 'commentstatusdiv', 'tickets', 'normal' );

}

//Get users by role
function scb_get_support_agents($role) {
    global $wpdb;
    $wp_user_search = new WP_User_Query( array( 'role' => $role ) );
    $scbagents = $wp_user_search->get_results();
    return $scbagents;
}

?>