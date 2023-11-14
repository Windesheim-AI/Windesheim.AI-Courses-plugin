<?php
if (!defined('ABSPATH')) {
    exit;
}

function SaveBlock()
{
    $id = isset($_POST['id']) ? $_POST['id'] : -1;
    if ($id == -1) {
        wp_die('Invalid block ID!');
    }
    $block = isset($_POST['block']) ? $_POST['block'] : -1;
    if ($block == -1) {
        wp_die('Invalid block!');
    }
    global $wpdb;
    $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';

    $wpdb->update($blocks_table, array('content' => json_encode($block)), array('id' => $id), array('%s'), array('%d'));
}

function AddBlock()
{
    $stage_id = isset($_POST['stage_id']) ? $_POST['stage_id'] : -1;
    if ($stage_id == -1) {
        wp_die('Invalid stage ID!');
    }
    $block = isset($_POST['block']) ? $_POST['block'] : -1;
    if ($block == -1) {
        wp_die('Invalid block!');
    }

    $blockType = isset($block['blockType']) ? $block['blockType'] : -1;
    if ($blockType == -1) {
        wp_die('Invalid block type!');
    }



    global $wpdb;
    $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';
    $wpdb->insert($blocks_table, array('stage_id' => $stage_id, 'block_type' => $blockType, 'content' => json_encode($block)), array('%d', '%s', '%s'));

}