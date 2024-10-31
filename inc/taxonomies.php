<?php

// Adding Tickets custom taxonomies
function scb_tickets_tx_add() {

    $labels = array(
        'name' => 'Tickets Categories',
        'singular_name' => 'Tickets Category',
        'search_items' => 'Search Tickets Categories',
        'all_items' => 'All Tickets Categories',
        'parent_item' => 'Parent Ticket Category',
        'parent_item_colon' => 'Parent Ticket Category:',
        'edit_item' => 'Edit Ticket Category',
        'update_item' => 'Update Ticket Category',
        'add_new_item' => 'Add New Ticket Category',
        'new_item_name' => 'New Ticket Category Name',
        'menu_name' => 'Tickets Categories',
    );


    register_taxonomy('ticket-category', array('tickets'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'ticket-category'),
    ));


    $labels = array(
        'name' => 'Tickets Priorities',
        'singular_name' => 'Tickets Priority',
        'search_items' => 'Search Tickets Priority',
        'all_items' => 'All Tickets Priorities',
        'parent_item' => 'Parent Tickets Priorities',
        'parent_item_colon' => 'Parent Tickets Priority:',
        'edit_item' => 'Edit Tickets Priority',
        'update_item' => 'Update Tickets Priority',
        'add_new_item' => 'Add New Tickets Priority',
        'new_item_name' => 'New Tickets Priority Name',
        'menu_name' => 'Tickets Priorities',
    );


    register_taxonomy('ticket-priority', array('tickets'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'ticket-priority'),
    ));

    //$labels = array(
    //    'name' => 'Tickets Channels',
    //    'singular_name' => 'Tickets Channel',
    //    'search_items' => 'Search Tickets Channels',
    //    'all_items' => 'All Tickets Channels',
    //    'parent_item' => 'Parent Tickets Channel',
    //    'parent_item_colon' => 'Parent Tickets Channel:',
    //    'edit_item' => 'Edit Tickets Channel',
    //    'update_item' => 'Update Tickets Channel',
    //    'add_new_item' => 'Add New Tickets Channel',
    //    'new_item_name' => 'New Tickets Channel Name',
    //    'menu_name' => 'Tickets Channels',
    //);


    //register_taxonomy('ticket-channels', array('tickets'), array(
    //    'hierarchical' => true,
    //    'labels' => $labels,
    //    'show_ui' => true,
    //    'query_var' => true,
    //    'rewrite' => array('slug' => 'ticket-channels'),
    //));

    $labels = array(
        'name' => 'Tickets Statuses',
        'singular_name' => 'Tickets Status',
        'search_items' => 'Search Tickets Statuses',
        'all_items' => 'All Tickets Statuses',
        'parent_item' => 'Parent Tickets Status',
        'parent_item_colon' => 'Parent Tickets Status:',
        'edit_item' => 'Edit Tickets Status',
        'update_item' => 'Update Tickets Status',
        'add_new_item' => 'Add New Tickets Status',
        'new_item_name' => 'New Tickets Status Name'
    );


    register_taxonomy('ticket-status', array('tickets'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'ticket-status'),
    ));


    if (get_option('scb_plugin_sample_taxonomy_insert') == false) {

    global $wpdb;
    global $post;

    wp_insert_term('Sample Category 1', 'ticket-category');
    wp_insert_term('Sample Category 2', 'ticket-category');

    wp_insert_term('New', 'ticket-status');
    wp_insert_term('Open', 'ticket-status');
    wp_insert_term('Pending', 'ticket-status');
    wp_insert_term('Resolved', 'ticket-status');    

    wp_insert_term('1 - Low', 'ticket-priority');
    wp_insert_term('2', 'ticket-priority');
    wp_insert_term('3', 'ticket-priority');
    wp_insert_term('4', 'ticket-priority');
    wp_insert_term('5 - High', 'ticket-priority');

    $scb_sample_ticket_1 = array(
      'post_title'    => 'This is an open ticket sample',
      'post_content'  => 'Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Sed posuere consectetur est at lobortis. Sed posuere consectetur est at lobortis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
      'post_status'   => 'publish',
      'post_author'   => 1,
      'post_type'     => 'tickets'
    );


    $scb_sample_ticket_2 = array(
      'post_title'    => 'This is a new ticket sample',
      'post_content'  => 'Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Sed posuere consectetur est at lobortis. Sed posuere consectetur est at lobortis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
      'post_status'   => 'publish',
      'post_author'   => 1,
      'post_type'     => 'tickets'
    );


    $new_sample_ticket_id_1 = wp_insert_post( $scb_sample_ticket_1 );
    $new_sample_ticket_id_2 = wp_insert_post( $scb_sample_ticket_2 );



    wp_set_object_terms($new_sample_ticket_id_1, 'Sample Category 1', 'ticket-category');
    wp_set_object_terms($new_sample_ticket_id_1, 'Open', 'ticket-status');
    wp_set_object_terms($new_sample_ticket_id_1, '4', 'ticket-priority');

    wp_set_object_terms($new_sample_ticket_id_2, 'Sample Category 2', 'ticket-category');
    wp_set_object_terms($new_sample_ticket_id_2, 'New', 'ticket-status');
    wp_set_object_terms($new_sample_ticket_id_2, '1 - Low', 'ticket-priority');

    update_option('scb_plugin_sample_taxonomy_insert', 'installed');

    }


}

add_action('init', 'scb_tickets_tx_add');

function scb_add_ticket_status_meta_field($term) {
    @$t_id = $term->term_id;
    $term_meta = get_option("taxonomy_$t_id");
    $scb_status_color = esc_attr($term_meta['status_color']) ? esc_attr($term_meta['status_color']) : '';
    ?>


    <div class="form-field">
        <tr class="form-field">
            <th scope="row" valign="top"><label for="term_meta[status_color]">Status Color</label></th>
            <td>
                <input type="text" name="term_meta[status_color]" id="term_meta[status_color]" value="<?php echo esc_attr($term_meta['status_color']) ? esc_attr($term_meta['status_color']) : ''; ?>">
                <p class="description">Enter the color of the status, example: #2b2b2b</p>
            </td>
        </tr>
    </div>
    <?php
}

function scb_edit_ticket_status_meta_field($term) {
    @$t_id = $term->term_id;
    $term_meta = get_option("taxonomy_$t_id");
    $scb_status_color = esc_attr($term_meta['status_color']) ? esc_attr($term_meta['status_color']) : '';
    ?>

    <tr class="form-field">
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[status_color]">Status Color</label></th>
        <td>
            <input type="text" name="term_meta[status_color]" id="term_meta[status_color]" value="<?php echo esc_attr($term_meta['status_color']) ? esc_attr($term_meta['status_color']) : ''; ?>">
            <p class="description">Enter the color of the status, example: #2b2b2b</p>
        </td>
    </tr>
    </tr>

    <?php
}

add_action('ticket-status_edit_form_fields', 'scb_edit_ticket_status_meta_field', 10, 2);
add_action('ticket-status_add_form_fields', 'scb_add_ticket_status_meta_field', 10, 2);

function save_taxonomy_custom_meta($term_id) {
    if (isset($_POST['term_meta'])) {
        $t_id = $term_id;
        $term_meta = get_option("taxonomy_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
        foreach ($cat_keys as $key) {
            if (isset($_POST['term_meta'][$key])) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }

        update_option("taxonomy_$t_id", $term_meta);
    }
}

add_action('edited_ticket-status', 'save_taxonomy_custom_meta', 10, 2);
add_action('create_ticket-status', 'save_taxonomy_custom_meta', 10, 2);
?>