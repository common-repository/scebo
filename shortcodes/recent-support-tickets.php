<?php
function scb_recent_support_tickets_shortcode() {
    global $wpdb;
    global $post;
    global $wp_query;
    
    if (is_user_logged_in()) {

        global $current_user;
        get_currentuserinfo();

        ?>

        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array( 'post_type'   => 'tickets', 'paged' => $paged);
        query_posts($args); ?>

        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                <?php

                $terms = wp_get_post_terms($post->ID, 'ticket-status');
                foreach ($terms as $term) {
                    $scb_ticket_status = $term->name;
                    $t_id = $term->term_id;
                }             

                $term_meta = get_option("taxonomy_$t_id");
                $scb_status_color = esc_attr($term_meta['status_color']) ? esc_attr($term_meta['status_color']) : '';

                ?>

<div class="ticket-single">
    <div class="tickets-title"><h1><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h1></div>
    <div class="tickets-meta"><div class="tickets-meta-date"><?php the_time('F j, Y') ; ?></div><div class="tickets-meta-id">Ticket ID: #<?php echo get_the_ID(); ?></div><div class="tickets-meta-status" style="background:<?php echo $scb_status_color; ?> !important;" ><?php echo $scb_ticket_status; ?></div></div>
</div>

            <?php endwhile; endif;  ?>

            <div class="pagination">
                <?php
              

                $big = 999999999; // need an unlikely integer

                echo paginate_links(array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '?paged=%#%',
                    'current' => max(1, get_query_var('paged')),
                    'total' => $wp_query->max_num_pages,
                    'prev_text' => 'Previous',
                    'next_text' => 'Next',
                ));
                ?>
            </div><!--/pagination-->

            <?php wp_reset_query();  ?>

        <?php

        $scb_ticket_status_id = get_term_by('name', 'New', 'ticket-status');
        $scb_ticket_status_id_print = $scb_ticket_status_id->term_id;

      

    }

  

} ?>