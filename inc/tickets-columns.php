<?php

// Adding columns to the tickets table
add_filter('manage_edit-tickets_columns', 'scb_add_new_tickets_columns');

function scb_add_new_tickets_columns($tickets_columns) {
    $new_columns['cb'] = '<input type="checkbox" />';

    $new_columns['scb_id'] = 'ID';
    //$new_columns['scb_channel'] = '';
    $new_columns['scb_title'] = 'Subject';
    $new_columns['scb_updated'] = 'Updated';
    $new_columns['scb_author'] = 'Customer';
    $new_columns['scb_responses'] = 'Responses';
    $new_columns['scb_ticket-priority'] = 'Priority';
    $new_columns['scb_ticket-status'] = 'Status';
    $new_columns['scb_ticket-category'] = 'Category';

    return $new_columns;
}

add_action('manage_tickets_posts_custom_column', 'scb_manage_tickets_columns', 10, 2);

function scb_manage_tickets_columns($column_name, $id) {
    global $wpdb;
    switch ($column_name) {
        case 'scb_channel':
            $terms = wp_get_post_terms($id, 'ticket-channels');
            foreach ($terms as $term) {
                print '<div class="channel-' . $term->slug . '" ></div>';
            }
            break;
            
        case 'scb_id':
            echo '#' . $id;
            break;

        case 'scb_title':
            echo '<div class="table-ticket-title"><a href="' . home_url() . '/wp-admin/post.php?post=' . $id . '&action=edit">' . get_the_title() . '</a></div>';
            break;

        case 'scb_ticket-priority':
            $terms = wp_get_post_terms($id, 'ticket-priority');
            foreach ($terms as $term) {
                print $term->name;
            }
            break;

        case 'scb_ticket-status':
            $terms = wp_get_post_terms($id, 'ticket-status');
            foreach ($terms as $term) {
                $t_id = $term->term_id;

                $term_meta = get_option("taxonomy_$t_id");
                $scb_status_color = esc_attr($term_meta['status_color']) ? esc_attr($term_meta['status_color']) : '';

                print '<div class="status" style="background: ' . $scb_status_color . ' !important;" >' . $term->name . '</a></div>';
            }

            break;

        case 'scb_ticket-category':

            $terms = wp_get_post_terms($id, 'ticket-category');
            foreach ($terms as $term) {
                print '<a href="' . home_url() . '/wp-admin/edit.php?ticket-category=' . $term->slug . '&post_type=tickets">' . $term->name . '</a></div>';
            }
            break;

        case 'scb_author':
            echo get_the_author();
            break;

        case 'scb_updated':

            $comments = get_comments('post_id=' . $id . '&number=1');
            foreach ($comments as $comment) :
                ?>
                <?php

                $scb_comment_time = get_comment_date('U', $comment->comment_ID);
                $scb_current_time = current_time('timestamp');
                $scb_comment_human_time = human_time_diff($scb_comment_time, $scb_current_time);

                echo '' . $scb_comment_human_time . ' ago';

            endforeach;
            break;


        case 'scb_responses':
            echo comments_number('No responses', 'One response', '% responses');
            break;

        default:

            break;
    }
}

?>