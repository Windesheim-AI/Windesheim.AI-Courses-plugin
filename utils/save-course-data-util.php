<?php

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