<?php
function text_content_block($block, $stage_id, $new = false)
{
    if (!$new) {
        $id = (int) ($block->id ?? -1);
        if ($id == -1) {
            wp_die('Invalid block ID!');
        }
    } else
        $id = uniqid();

    $content = isset($block->content) ? $block->content : '';
    $text = isset($content->text) ? esc_html($content->text) : '';
    ?>
    <div class="row">
        <div class="col-md-6">
            <form id="text-block-edit-form-<?php echo $id; ?>">
                <div class="text-block-edit">
                    <input type="hidden" name="<?php echo 'id-' . $id; ?>" id="id-<?php echo $id; ?>"
                        value="<?php echo $id; ?>">
                    <div class="form-group">
                        <label for="text-<?php echo $id; ?>" class="control-label">Text</label>
                        <textarea name="text" id="text-<?php echo $id; ?>" placeholder="Text"
                            class="w-100 min-h-200"><?php echo $text; ?></textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" class="button button-primary"
                            id="save-button-<?php echo $id; ?>">Save</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $('#save-button-<?php echo $id; ?>').click(function (e) {
                e.preventDefault();

                var block = {
                    'text': $('#text-<?php echo $id; ?>').val()
                };

                var action = <?php echo $new ? "'add_wingai_block'" : "'save_wingai_block'"; ?>;

                var data = {
                    'id': $('input[name="<?php echo 'id-' . $id; ?>"]').val(),
                    'action': action,
                    'block': block,
                    'block_type': 'text',
                    'stage_id': <?php echo $stage_id; ?>
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

add_action('wp_ajax_save_wingai_block', 'SaveBlock');
add_action('wp_ajax_add_wingai_block', 'AddBlock');
