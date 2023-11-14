<?php
function button_content_block($block)
{
    $id = (int) ($block->id ?? -1);
    if ($id == -1) {
        wp_die('Invalid block ID!');
    }

    $content = $block->content;
    $text = isset($content->text) ? esc_html($content->text) : '';
    $navigateToStageId = isset($content->navigateToStageId) ? esc_html($content->navigateToStageId) : '';
    $colorOptions = isset($content->colorOptions) ? esc_html($content->colorOptions) : '';
    ?>
    <div class="row">
        <div class="col-md-4">
            <form id="button-block-edit-form">
                <div class="button-block-edit">
                    <div class="form-group">
                        <input type="hidden" name="<?php echo 'id-' . $id; ?>" value="<?php echo $id; ?>">
                    </div>
                    <div class="form-group">
                        <label for="text-<?php echo $id; ?>" class="control-label">Text</label>
                        <input type="text" name="<?php echo 'text-' . $id; ?>" value="<?php echo $text; ?>"
                            placeholder="Text" class="w-100">
                    </div>
                    <div class="form-group">
                        <label for="navigateToStageId-<?php echo $id; ?>" class="control-label">Navigate To Stage ID</label>
                        <input type="text" name="<?php echo 'navigateToStageId-' . $id; ?>"
                            value="<?php echo $navigateToStageId; ?>" placeholder="Navigate To Stage ID" class="w-100">
                    </div>
                    <div class="form-group">
                        <label for="colorOptions-<?php echo $id; ?>" class="control-label">ColorOptions</label>
                        <input type="text" name="<?php echo 'colorOptions-' . $id; ?>" value="<?php echo $colorOptions; ?>"
                            placeholder="Color Options" class="w-100">
                    </div>
                    <div class="form-group">
                        <button type="button" class="button button-primary" id="save-button">Save</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $('#save-button').click(function (e) {
                e.preventDefault();

                var block = {
                    'text': $('input[name="<?php echo 'text-' . $id; ?>"]').val(),
                    'navigateToStageId': $('input[name="<?php echo 'navigateToStageId-' . $id; ?>"]').val(),
                    'colorOptions': $('input[name="<?php echo 'colorOptions-' . $id; ?>"]').val()
                };

                var data = {
                    'id': $('input[name="<?php echo 'id-' . $id; ?>"]').val(),
                    'action': 'save_wingai_block',
                    'block': block
                };
                $(this).html('<span class="spinner is-active"></span>');
                $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');


                $.post(ajaxurl, data, function (response) {
                    $('#save-button').html('Save');
                    $('#save-button').prop('disabled', false).html('Save');
                });
            });
        });
    </script>
    <?php
}

add_action('wp_ajax_save_wingai_block', 'SaveBlock');
