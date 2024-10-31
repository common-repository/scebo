<?php
if (isset($_POST['scb_registration_notification_email']) && isset($_POST['scb_ticket_update_notification_email']) && isset($_POST['scb_ticket_confirmation_notification_email'])) {
    update_option('scb_registration_notification_email', $_POST['scb_registration_notification_email']);
    update_option('scb_ticket_update_notification_email', $_POST['scb_ticket_update_notification_email']);
    update_option('scb_ticket_confirmation_notification_email', $_POST['scb_ticket_confirmation_notification_email']);
}
?>

<div class="wrap">
    <h2>Notifications Settings</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Registration Notification</th>
                <td colspan='2'>

                    <?php $scb_registration_notification = get_option('scb_registration_notification_email'); ?>

                    <textarea name="scb_registration_notification_email" style='width:700px;height:400px'><?php echo $scb_registration_notification; ?></textarea>
                    <p class="description"><strong>Available tags:</strong> <strong>{sitename}</strong> - Your site name, <strong>{userfirstname}</strong> - Registered user first name,<br/><strong>{userlastname}</strong> - Registered user last name, <strong>{accountusername}</strong> - Registered user account username,<br/>
                        <strong>{accountpassword}</strong>  - Registered user password username, <strong>{siteloginlink}</strong>  - Your site login URL <br /></p>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Ticket Confirmation Notification</th>
                <td colspan='2'>

                    <?php $scb_ticket_confirmation_notification = get_option('scb_ticket_confirmation_notification_email'); ?>

                    <textarea name="scb_ticket_confirmation_notification_email" style='width:700px; height:300px'><?php echo $scb_ticket_confirmation_notification; ?></textarea>
                    <p class="description"><strong>Available tags:</strong> <strong>{sitename}</strong> - Your site name, <strong>{authorfirstname}</strong> - Ticket author first name, <strong>{authorlastname}</strong> - Ticket author last name,<br/>
                        <strong>{siteloginlink}</strong>  - Your site login URL, <strong>{ticketurl}</strong>  - Ticket URL, <strong>{ticketid}</strong>  - Ticket ID</p>
                </td>
            </tr>


            <tr valign="top">
                <th scope="row">Ticket Update Notification</th>
                <td colspan='2'>

                    <?php $scb_ticket_update_notification = get_option('scb_ticket_update_notification_email'); ?>

                    <textarea name="scb_ticket_update_notification_email" style='width:700px; height:300px'><?php echo $scb_ticket_update_notification; ?></textarea>
                    <p class="description"><strong>Available tags:</strong> <strong>{sitename}</strong> - Your site name, <strong>{authorfirstname}</strong> - Ticket author first name, <strong>{authorlastname}</strong> - Ticket author last name,<br/>
                        <strong>{siteloginlink}</strong>  - Your site login URL, <strong>{ticketurl}</strong>  - Ticket URL, <strong>{ticketid}</strong>  - Ticket ID</p>
                </td>
            </tr>


        </table>

        <p class="submit"><input type="submit" value="Save Changes" class="button-primary" id="submit" name="submit"></p>

    </form>

</div><!--wrap-->