<?php
if (isset($_POST['scb_pending_status'])) {
    update_option('scb_pending_status', $_POST['scb_pending_status']);
    update_option('scb_open_status', $_POST['scb_open_status']);
}
?>

<div class="wrap">
    <h2>Scebo Options</h2>
    <form action="" method="post" enctype="multipart/form-data">

        <table class="form-table">

            
            <tr valign="top"><?php
            ?>
                <th scope="row"><label for="scb_pending_status">Pending Status</label></th>
                <td><fieldset>
                        <select name="scb_pending_status">
                            <?php
                            $terms = get_terms("ticket-status", "hide_empty=0");
                            foreach ($terms as $term) {
                                if(get_option('scb_pending_status') == $term->term_id){
                                    $selected = 'selected=""';
                                }else{
                                    $selected = '';
                                }
                                echo '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
                            }
                            ?>

                        </select>
                    </fieldset></td>
            </tr>

            <tr valign="top"><?php

            ?>
                <th scope="row"><label for="scb_open_status">Open Status</label></th>
                <td><fieldset>
                        <select name="scb_open_status">
                            <?php
                            $terms = get_terms("ticket-status", "hide_empty=0");
                            foreach ($terms as $term) {
                                if(get_option('scb_open_status') == $term->term_id){
                                    $selected = 'selected=""';
                                }else{
                                    $selected = '';
                                }
                                echo '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
                            }
                            ?>
                        </select>
                    </fieldset></td>
            </tr>

        </table>

        <p class="submit"><input type="submit" value="Save Changes" class="button-primary" id="submit" name="submit"></p>

    </form>

</div><!--wrap-->