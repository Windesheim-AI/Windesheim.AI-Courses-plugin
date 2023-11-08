<?php
function ai_content_block($block)
{
    $content = $block['content'];
    $propmt = $content['prompt'];
    $provider = $content['provider'];

    // make the prompt and provider editable 
    $editor_id = uniqid('wingai_block_');
    wp_editor(
        $propmt,
        $editor_id,
        array(
            'textarea_name' => $editor_id,
            'textarea_rows' => 4,
            'media_buttons' => false,
            'tinymce' => array(
                'toolbar1' => 'bold,italic,underline,|,bullist,numlist,|,link,unlink,|,undo,redo',
                'toolbar2' => '',
                'toolbar3' => '',
            ),
        )
    );
    $editor_id = uniqid('wingai_block_');
    //the provider is a short field
    ?>
    <div class="ai-block-edit">
        <input type="text" name="provider" value="<?php echo $provider; ?>" placeholder="Provider">
    </div>
    <?php
}