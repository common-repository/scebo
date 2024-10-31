<?php
global $wpdb;
global $post;

//Adding the registration notification mail
if (get_option('scb_registration_notification_email') == false) {
    update_option('scb_registration_notification_email',
            
            'Hello {userfirstname},

            You have been registered in the {sitename} support system
            This email will be used to notify you of replies or status updates to your support tickets.

            ---------------

            Username -> {accountusername}
            Password -> {accountpassword}

            ---------------

            You can use the link below to login:

            {siteloginlink}


            Kind Regards,
            {sitename}');
}

//Adding the ticket confirmation notification mail
if (get_option('scb_ticket_confirmation_notification_email') == false) {
    update_option('scb_ticket_confirmation_notification_email',

            'Hello {authorfirstname},

            Your ticket (#{ticketid}) has been successfully created and is now being routed to an appropriate group or staff member on our team.

            During the course of support you may respond to, or update this ticket at the following URL:

            -------------------------------
            {ticketurl}
            -------------------------------

            Kind Regards,
            {sitename}');
}

//Adding the ticket update notification mail
if (get_option('scb_ticket_update_notification_email') == false) {
    update_option('scb_ticket_update_notification_email',

            'Hello {authorfirstname},

            The {sitename} support team has replied to your ticket (#{ticketid}).

            During the course of support you may respond to, or update this ticket at the following URL:

            -------------------------------
            {ticketurl}
            -------------------------------

            Kind Regards,
            {sitename}');
}

?>
