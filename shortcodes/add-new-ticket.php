<?php
function scb_add_new_ticket_shortcode($att, $content = null) {
    ob_start();
    global $wpdb;

    if (is_user_logged_in()) {

        global $current_user;
        get_currentuserinfo();

        if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action'])) {

            if ($_POST['title'] != '' && $_POST['description'] != '') {

                $scb_title = $_POST['title'];
                $scb_description = $_POST['description'];
                $ticket_category_ID = array($_POST['cat']);
                $ticket_category_name = get_term($ticket_category_ID[0], 'ticket-category');

                $post = array(
                        'post_title' => $scb_title,
                        'post_content' => $scb_description,
                        'post_status' => 'publish',
                        'post_type' => 'tickets',
                        'post_author' => $current_user->ID
                );

                $new_ticket_id = wp_insert_post($post);

                wp_set_object_terms($new_ticket_id, $ticket_category_name->name, 'ticket-category');
                wp_set_object_terms($new_ticket_id, 'New', 'ticket-status');
                //wp_set_object_terms($new_ticket_id, 'Support System', 'ticket-channels');

                do_action('wp_insert_post', 'wp_insert_post');

                $scb_ticket_link = get_post_permalink($new_ticket_id);
                $scb_ticket_id = $new_ticket_id;
                $scb_ticket_author_email = $current_user->user_email;
                $scb_ticket_author_first_name = $current_user->user_firstname;
                $scb_ticket_author_last_name = $current_user->user_lastname;
                $scb_site_name = get_bloginfo('name');
                $scb_site_email = get_bloginfo('admin_email');
                $scb_site_login_link = '' . home_url() . '/wp-login.php';

                $headers = 'From: ' . $scb_site_name . ' <' . $scb_site_email . '>' . "\r\n";

                $subject = 'Ticket (#' . $scb_ticket_id . ') - Confirmation';

                $scb_ticket_confirmation_notification = get_option('scb_ticket_confirmation_notification_email');

                $scb_replace_what = array("{sitename}", "{authorfirstname}", "{authorlastname}", "{siteloginlink}", "{ticketurl}", "{ticketid}");
                $scb_replace_with = array($scb_site_name, $scb_ticket_author_first_name, $scb_ticket_author_last_name, $scb_site_login_link, $scb_ticket_link, $scb_ticket_id);

                $scb_ticket_confirmation_notification = str_replace($scb_replace_what, $scb_replace_with, $scb_ticket_confirmation_notification);

                add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));

                @wp_mail($scb_ticket_author_email, $subject, nl2br($scb_ticket_confirmation_notification), $headers);

                $message = 'Thank you for the submission.';
            } else {
                $message = 'All fields are required';
            }
        }
        ?>


<form id="submit_ticket" name="submit_ticket" method="post" action="" enctype="multipart/form-data">
    <h3>Ticket title</h3>
    <input type="text" name="title" />

    <h3>Ticket Category</h3>

    <div><?php wp_dropdown_categories('tab_index=10&taxonomy=ticket-category&hide_empty=0'); ?></div>

    <h3>Describe the problem</h3>

    <textarea name="description"></textarea>

    <div class="submission_mesage"><?php echo @$message; ?></div>

    <input name="submit" type="submit" id="submit" value="Submit Ticket">

    <input type="hidden" name="page" id="page" value="<?php echo $post->ID; ?>"/>

    <input type="hidden" name="action" value="post" />

            <?php wp_nonce_field('new-post'); ?>
</form>

        <?php
    } else {

        global $message;
        global $additional_check_status;

        if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action'])) {

            if ($_POST['firstname'] != '' && $_POST['lastname'] != '' && $_POST['username'] != '' && $_POST['password'] != '' && $_POST['repeat_password'] != '' && $_POST['email'] != '') {

                $scb_registration_first_name = $_POST['firstname'];
                $scb_registration_last_name = $_POST['lastname'];
                $scb_registration_username = $_POST['username'];
                $scb_registration_password = $_POST['password'];
                $scb_registration_repeat_password = $_POST['repeat_password'];
                $scb_registration_email = $_POST['email'];

                if ($scb_registration_repeat_password != $scb_registration_password) {
                    $message = 'Repeat Password does not match with your password';
                }

                if (!is_email($scb_registration_email)) {
                    $message = 'Enter a real email ';
                }

                if (email_exists($scb_registration_email)) {
                    $message = 'This email is already registered, please choose another one.';
                }

                if (username_exists($scb_registration_username)) {
                    $message = 'This username is already registered, please choose another one.';
                }

                if (( $scb_registration_repeat_password != $scb_registration_password ) || (!is_email($scb_registration_email) ) || ( email_exists($scb_registration_email) ) || ( username_exists($scb_registration_username) )) {

                } else {

                    $additional_check_status = true;
                    scb_additional_validity_check();

                    if ($additional_check_status == true) {
                        $user_id = wp_create_user($scb_registration_username, $scb_registration_password, $scb_registration_email);
                        update_user_meta($user_id, 'first_name', $scb_registration_first_name);
                        update_user_meta($user_id, 'last_name', $scb_registration_last_name);
                        wp_update_user(array('ID' => $user_id, 'display_name' => $scb_registration_first_name . ' ' . $scb_registration_last_name));
                        $wp_user_object = new WP_User($user_id);
                        $wp_user_object->set_role('subscriber');
                        add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
                        $scb_site_name = get_bloginfo('name');
                        $scb_site_email = get_bloginfo('admin_email');
                        $scb_site_login_link = '' . home_url() . '/wp-login.php';

                        $headers = 'From: ' . $scb_site_name . ' <' . $scb_site_email . '>' . "\r\n";

                        $subject = 'Account Created';

                        $scb_registration_notification = get_option('scb_registration_notification_email');

                        $scb_replace_what = array("{sitename}", "{userfirstname}", "{userlastname}", "{accountusername}", "{accountpassword}", "{siteloginlink}");
                        $scb_replace_with = array($scb_site_name, $scb_registration_first_name, $scb_registration_last_name, $scb_registration_username, $scb_registration_password, $scb_site_login_link);

                        $scb_registration_notification = str_replace($scb_replace_what, $scb_replace_with, $scb_registration_notification);

                        add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
                        @wp_mail($scb_registration_email, $subject, nl2br($scb_registration_notification), $headers);

                        ob_clean();
                        wp_redirect(home_url());
                    }
                }
            } else {
                $message = 'All fields are required';
            }
        }
        ?>

<form id="scb_register_account" name="scb_register_account" method="post" action="" enctype="multipart/form-data">

    <p>Our support is offered to registered customers only. Use the form below to register your account. If you are already registered login <a href="<?php echo wp_login_url( get_permalink() ); ?>">here</a></p>

    <h3>First Name</h3>

    <input type="text" name="firstname" value="<?php echo @$scb_registration_first_name ?>" />

    <h3>Last Name</h3>

    <input type="text" name="lastname" value="<?php echo @$scb_registration_last_name ?>" />

    <h3>Email</h3>

    <input type="text" name="email" value="<?php echo @$scb_registration_email ?>" />

    <h3>Username</h3>

    <input type="text" name="username" value="<?php echo @$scb_registration_username ?>" />

    <h3>Password</h3>

    <input type="password" name="password" />

    <h3>Repeat Password</h3>

    <input type="password" name="repeat_password" />

            <?php envato_additional_fields(); ?>

    <input name="submit" type="submit" id="submit" value="Register Your Account"><br/>

    <div class="submission_mesage"><?php echo @$message; ?></div>

    <input type="hidden" name="action" value="post" />

</form>
        <?php


    }
    $content = ob_get_contents();
    ob_clean();
    return $content;

} ?>