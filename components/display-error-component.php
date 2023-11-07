<?php
function display_error($msg)
{
    ?>
    <div class="notice notice-error is-dismissible">
        <p>
            <?php echo $msg ?>
        </p>
    </div>
    <?php
}
