<?php
// Adding Tickets custom post type
function scb_tickets_pt_add() {

    $labels = array(
        'name' => 'Tickets',
        'singular_name' => 'Ticket',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Ticket',
        'edit_item' => 'Edit Ticket',
        'new_item' => 'New Ticket',
        'all_items' => 'All Tickets',
        'view_item' => 'View Ticket',
        'search_items' => 'Search Tickets',
        'not_found' => 'No tickets found',
        'not_found_in_trash' => 'No tickets found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Tickets'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 100,
        'menu_icon' => plugin_dir_url(__FILE__) . 'style/img/ticket-icon.png',
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments')
    );

    register_post_type('tickets', $args);

    flush_rewrite_rules();
}

add_action('init', 'scb_tickets_pt_add');
?>
