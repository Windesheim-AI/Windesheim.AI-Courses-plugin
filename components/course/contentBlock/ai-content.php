<?php
function ai_content_block($block)
{
    $id = (int) ($block->id ?? -1);
    if ($id == -1) {
        wp_die('Invalid block ID!');
    }

    $content = $block->content;
    $prompt = isset($content->prompt) ? esc_html($content->prompt) : '';
    $provider = isset($content->provider) ? esc_html($content->provider) : '';
    ?>
    <form id="ai-block-edit-form-<?php echo $id; ?>">
        <div class="ai-block-edit">
            <input type="hidden" name="<?php echo 'id-' . $id; ?>" id="id-<?php echo $id; ?>" value="<?php echo $id; ?>">
            <textarea name="text" id="prompt-<?php echo $id; ?>" placeholder="prompt"><?php echo $prompt; ?></textarea>
            <input type="text" name="<?php echo 'provider-' . $id; ?>" id="provider-<?php echo $id; ?>" value="<?php echo $provider; ?>" placeholder="Provider">
            <button type="button" class="button button-primary" id="save-button-<?php echo $id; ?>">Save</button>
        </div>
    </form>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $('#save-button-<?php echo $id; ?>').click(function (e) {
                e.preventDefault();

                var block = {
                    'prompt': $('#prompt-<?php echo $id; ?>').val(),
                    'provider': $('#provider-<?php echo $id; ?>').val()
                };

                var data = {
                    'id': $('input[name="<?php echo 'id-' . $id; ?>"]').val(),
                    'action': 'save_wingai_block',
                    'block': block
                };
                $(this).html('<span class="spinner is-active"></span>');
                $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');

                $.post(ajaxurl, data, function (response) {
                    $('#save-button-<?php echo $id; ?>').html('Save');
                    $('#save-button-<?php echo $id; ?>').prop('disabled', false).html('Save');
                });
            });
        });
    </script>
    <?php
}

add_action('wp_ajax_save_wingai_block', 'SaveBlock');