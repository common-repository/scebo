<?php
$meta_boxes = array(
        array(
                'id' => 'scb_ticket_details',
                'title' => 'Ticket Details',
                'pages' => array('tickets'),
                'context' => 'side',
                'priority' => 'high',
                'fields' => array(
                        array(
                                'name' => 'Ticket ID',
                                'desc' => '',
                                'id' => 'ticket-id',
                                'type' => 'ticket_id',
                                'std' => ''
                        ),
                        array(
                                'name' => 'Customer',
                                'desc' => '',
                                'id' => 'ticket-author',
                                'type' => 'ticket_author',
                                'std' => ''
                        ),
                        array(
                                'name' => 'Created',
                                'desc' => '',
                                'id' => 'ticket-date',
                                'type' => 'ticket-date',
                                'std' => ''
                        ),
                        array(
                                'name' => 'Updated',
                                'desc' => '',
                                'id' => 'ticket-update',
                                'type' => 'ticket-update',
                                'std' => ''
                        ),
                        array(
                                'name' => 'Priority',
                                'desc' => '',
                                'id' => 'ticket-priority',
                                'type' => 'select_priority',
                                'std' => ''
                        ),
                        array(
                                'name' => 'Status',
                                'desc' => '',
                                'id' => 'ticket-status',
                                'type' => 'select_status',
                                'std' => ''
                        ),
                        array(
                                'name' => 'Category',
                                'desc' => '',
                                'id' => 'ticket-category',
                                'type' => 'select_category',
                                'std' => ''
                        ),
                )
        )
);


foreach ($meta_boxes as $meta_box) {
    $my_box = new scebo_meta_box($meta_box);
}

class scebo_meta_box {

    protected $_meta_box;

    function __construct($meta_box) {
        $this->_meta_box = $meta_box;
        add_action('admin_menu', array(&$this, 'add'));

        add_action('save_post', array(&$this, 'save'));
    }

    function add() {
        foreach ($this->_meta_box['pages'] as $page) {
            add_meta_box($this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']);
        }
    }

    function show() {
        global $post;
        global $wpdb;

        echo '<input type="hidden" name="scebo_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

        echo '<table>';

        foreach ($this->_meta_box['fields'] as $field) {

            $meta = get_post_meta($post->ID, $field['id'], true);

            echo '<tr>',
            '<th class="'.$this->_meta_box['id'].'"><label for="', $field['id'], '">', $field['name'], '</label></th>',
            '<td>';
            switch ($field['type']) {

                case 'ticket_id':
                    ?>

                    <?php
                    $scb_ticket_id = get_the_ID($post->ID);
                    ?>

                    <?php
                    echo '#' . $scb_ticket_id . '<br/>';


                    break;

                case 'ticket-date':
                    ?>

                    <?php
                    $scb_ticket_time = get_the_time('U', $post->ID);
                    $scb_current_time = current_time('timestamp');
                    $scb_ticket_human_time = human_time_diff($scb_ticket_time, $scb_current_time);
                    ?>

                    <?php
                    echo '' . $scb_ticket_human_time . ' ago - '. get_the_time('F j, Y') .'';


                    break;

                case 'ticket-update':
                    ?>


                    <?php

                    $comments = get_comments('post_id=' . $post->ID . '&number=1');
                    foreach ($comments as $comment) :

                    $scb_comment_update_time = get_comment_date('U', $comment->comment_ID);
                    $scb_current_update_time = current_time('timestamp');
                    $scb_comment_update_human_time = human_time_diff($scb_comment_update_time, $scb_current_update_time);

                    ?>

                    <?php

                        echo '' . $scb_comment_update_human_time . ' ago';
                    endforeach;

                    break;

                case 'ticket_author':
                    ?>

                    <?php
                    $scb_ticket_author_id = $post->post_author;
                    $scb_ticket_author_first_name = get_the_author_meta('first_name', $scb_ticket_author_id);
                    $scb_ticket_author_last_name = get_the_author_meta('last_name', $scb_ticket_author_id);
                    ?>

                    <?php
                    echo '' . $scb_ticket_author_first_name . ' ' . $scb_ticket_author_last_name . ' ';

                    break;


                case 'select_agent':
                    
                    echo '<select name="assigned_agent" id="assigned_agent" class="postform">';

                    $scbagents = scb_get_support_agents('support_agent') + scb_get_support_agents('administrator');

                    foreach ($scbagents as $scbagent) {

                    ?>

                    <option value="<?php echo $scbagent->ID; ?>" <?php if ( $scbagent->ID == $meta ) { echo 'selected="selected"'; }; ?>><?php echo $scbagent->display_name; ?></option>

                    <?php

                    };

                    echo '</select>';
                    

                    break;

                case 'select_priority':

                    wp_dropdown_categories(array('name' => '' . $field['id'] . '', 'taxonomy' => 'ticket-priority', 'hide_empty' => 0, 'selected' => '' . $meta ? $meta : $field['std'] . ''));

                    break;

                case 'select_category':

                    wp_dropdown_categories(array('name' => '' . $field['id'] . '', 'taxonomy' => 'ticket-category', 'hide_empty' => 0, 'selected' => '' . $meta ? $meta : $field['std'] . ''));

                    break;


                case 'select_status':

                    wp_dropdown_categories(array('name' => '' . $field['id'] . '', 'taxonomy' => 'ticket-status', 'hide_empty' => 0, 'selected' => '' . $meta ? $meta : $field['std'] . ''));

                    break;

                case 'prewritten_responses':
                    ?>

                    <?php query_posts('post_type=prewritten-responses'); ?>

<select name="response" id="response" onchange="insert_predefined_response();">

                        <?php if (have_posts()) : ?> <option value="">None</option> <?php while (have_posts()) : the_post(); ?>

    <option value="<?php the_content(); ?>"><?php the_title(); ?></option>

                            <?php endwhile;
                        endif; ?>

</select>

                    <?php
                    break;
            }
            echo '<td>',
            '</tr>';
        }

        echo '</table>';
    }

    function save($post_id) {

        if (!wp_verify_nonce(@$_POST['scebo_meta_box_nonce'], basename(__FILE__))) {
            return $post_id;
        }


        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }


        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }



        foreach ($this->_meta_box['fields'] as $field) {
            $old = get_post_meta($post_id, $field['id'], true);
            @$new = $_POST[$field['id']];


            switch ($field['type']) {


                case 'select_agent':

                    if ($new && $new != $old) {
                        update_post_meta($post_id, $field['id'], $new);
                    } elseif ('' == $new && $old) {
                        delete_post_meta($post_id, $field['id'], $old);
                    }

                    break;


                case 'select_priority':

                    if ($new && $new != $old) {
                        update_post_meta($post_id, $field['id'], $new);
                        wp_set_post_terms($post_id, $new, $field['id']);
                    } elseif ('' == $new && $old) {
                        delete_post_meta($post_id, $field['id'], $old);
                    }

                    break;


                case 'select_status':

                    if ($new && $new != $old) {
                        update_post_meta($post_id, $field['id'], $new);
                        wp_set_post_terms($post_id, $new, $field['id']);
                    } elseif ('' == $new && $old) {
                        delete_post_meta($post_id, $field['id'], $old);
                    }

                    break;


                case 'select_category':

                    if ($new && $new != $old) {
                        update_post_meta($post_id, $field['id'], $new);
                        wp_set_post_terms($post_id, $new, $field['id']);
                    } elseif ('' == $new && $old) {
                        delete_post_meta($post_id, $field['id'], $old);
                    }

                    break;
            }
        }
    }
}
?>