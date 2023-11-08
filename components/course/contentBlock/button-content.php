<?php
function button_content_block($block)
{
    $content = $block['content'];
    $text = $content['text'];
    $navigateToStageId = $content['navigateToStageId'];
    $colorOptions = $content['colorOptions'];
    ?>
    <div class="button-block-edit">
        <input type="text" name="text" value="<?php echo $text; ?>" placeholder="Text">
        <input type="text" name="navigateToStageId" value="<?php echo $navigateToStageId; ?>"
            placeholder="Navigate To Stage ID">
        <input type="text" name="colorOptions" value="<?php echo $colorOptions; ?>" placeholder="Color Options">
    </div>
    <?php
}