<?php
function text_content_block($block)
{
    $content = $block['content'];
    $text = (string) ($content['text'] ?? "");
    $editor_id = uniqid('wingai_block_');
    wp_editor(
        $text,
        $editor_id,
        array(
            'textarea_name' => $editor_id,
            'textarea_rows' => 10,
            'media_buttons' => false,
            'tinymce' => array(
                'toolbar1' => 'bold,italic,underline,|,bullist,numlist,|,link,unlink,|,undo,redo',
                'toolbar2' => '',
                'toolbar3' => '',
            ),
        )
    );
}