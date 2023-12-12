<?php
function ai_content_block($block, $stage_id, $new = false)
{
    if (!$new) {
        $id = (int) ($block->id ?? -1);
        if ($id == -1) {
            wp_die('Invalid block ID!');
        }
    } else {
      $id = uniqid('', TRUE);
    }

    $content = isset($block->content) ? $block->content : '';
    $prompt = isset($content->prompt) ? esc_html($content->prompt) : '';
    $provider = isset($content->provider) ? esc_html($content->provider) : '';
    ?>
    <div class="row">

        <div class="col-md-4">
            <form id="ai-block-edit-form-<?php echo $id; ?>" class="form-horizontal">
                <div class="ai-block-edit">
                    <div class="form-group">
                        <input type="hidden" name="<?php echo 'id-' . $id; ?>" id="id-<?php echo $id; ?>"
                            value="<?php echo $id; ?>">
                    </div>
                    <div class="form-group">
                        <label for="prompt-<?php echo $id; ?>" class="control-label">Prompt</label>
                        <textarea name="text" id="prompt-<?php echo $id; ?>" class="form-control w-100"
                            placeholder="prompt"><?php echo $prompt; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="provider-<?php echo $id; ?>" class="control-label">Provider</label>
                        <input type="text" name="<?php echo 'provider-' . $id; ?>" id="provider-<?php echo $id; ?>"
                            class="form-control w-100" value="<?php echo $provider; ?>" placeholder="Provider">
                    </div>
                    <div class="form-group">
                        <button type="button" class="button button-primary"
                            id="save-button-<?php echo $id; ?>">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript" nonce=<?php echo uniqid('', TRUE); ?>>
        jQuery(document).ready(function ($) {
            $('#save-button-<?php echo $id; ?>').click(function (e) {
                e.preventDefault();

                var block = {
                    'prompt': $('#prompt-<?php echo $id; ?>').val(),
                    'provider': $('#provider-<?php echo $id; ?>').val()
                };

                var action = <?php echo $new ? "'add_winai_block'" : "'save_winai_block'"; ?>;

                var data = {
                    'id': $('input[name="<?php echo 'id-' . $id; ?>"]').val(),
                    'stage_id': <?php echo $stage_id; ?>,
                'action': action,
                    'block': block,
                        'block_type': 'ai'
            };
            $(this).html('<span class="spinner is-active"></span>');
            $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');

            $.post(ajaxurl, data, function (response) {
                $('#save-button-<?php echo $id; ?>').html('Save');
                $('#save-button-<?php echo $id; ?>').prop('disabled', false).html('Save');
                if (<?php echo $new ? 'true' : 'false'; ?>) {
                location.reload();
            }
        });
                        });
                    });
    </script>
    <?php
}

add_action('wp_ajax_save_winai_block', 'SaveBlock');
add_action('wp_ajax_add_winai_block', 'AddBlock');