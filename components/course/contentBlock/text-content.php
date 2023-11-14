<?php
function text_content_block($block)
{
    $id = (int) ($block->id ?? -1);
    if ($id == -1) {
        wp_die('Invalid block ID!');
    }

    $content = $block->content;
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
                        <textarea name="text" id="text-<?php echo $id; ?>"
                            placeholder="Text" class="w-100 min-h-200" ><?php echo $text; ?></textarea>
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
