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

    $blockType = isset($_POST['block_type']) ? $_POST['block_type'] : -1;
    if ($blockType == -1) {
        wp_die('Invalid block type!');
    }

    global $wpdb;
    $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';
    //get the max weight
    $max_weight = $wpdb->get_var("SELECT MAX(weight) FROM $blocks_table WHERE stage_id = $stage_id");
    $max_weight = $max_weight == null ? 0 : $max_weight + 1;
    $block_data = [
        'stage_id' => $stage_id,
        'block_type' => $blockType,
        'content' => json_encode($block),
        'weight' => $max_weight,
    ];
    $wpdb->insert($blocks_table, $block_data);

}

function wingai_edit_stage_title()
{
    $stage_id = isset($_POST['stage_id']) ? $_POST['stage_id'] : -1;
    if ($stage_id == -1) {
        wp_die('Invalid stage ID!');
    }
    $title = isset($_POST['title']) ? $_POST['title'] : -1;
    if ($title == -1) {
        wp_die('Invalid title!');
    }

    global $wpdb;
    $stages_table = $wpdb->prefix . 'WingAI_Course_Stages';
    $wpdb->update($stages_table, array('title' => $title), array('id' => $stage_id), array('%s'), array('%d'));
}

function wingai_delete_block()
{
    $block_id = isset($_POST['block_id']) ? $_POST['block_id'] : -1;
    if ($block_id == -1) {
        wp_die('Invalid block ID!');
    }

    global $wpdb;
    $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';
    $wpdb->delete($blocks_table, array('id' => $block_id), array('%d'));

    wingai_reorder_weight_blocks($_POST['stage_id']);
}

function wingai_reorder_weight_blocks($stage_id)
{
    //make the blocks weight in order
    global $wpdb;
    $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';
    $blocks = $wpdb->get_results("SELECT id FROM $blocks_table WHERE stage_id = $stage_id ORDER BY weight ASC");
    $i = 0;
    foreach ($blocks as $block) {
        $wpdb->update($blocks_table, array('weight' => $i++), array('id' => $block->id), array('%d'), array('%d'));
    }
}

function wingai_set_block_weights()
{
    //from the post get the ids array and the stage id and order the blocks by the ids array
    $ids = isset($_POST['ids']) ? $_POST['ids'] : -1;
    if ($ids == -1) {
        wp_die('Invalid ids!');
    }
    $stage_id = isset($_POST['stage_id']) ? $_POST['stage_id'] : -1;
    if ($stage_id == -1) {
        wp_die('Invalid stage ID!');
    }

    global $wpdb;
    $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';
    $i = 0;
    foreach ($ids as $id) {
        $wpdb->update($blocks_table, array('weight' => $i++), array('id' => $id), array('%d'), array('%d'));
    }
}

function wingai_update_course()
{
    $course_id = isset($_POST['course_id']) ? $_POST['course_id'] : -1;
    if ($course_id == -1) {
        wp_die('Invalid course ID!');
    }
    $course_title = isset($_POST['course_title']) ? $_POST['course_title'] : -1;
    if ($course_title == -1) {
        wp_die('Invalid course!');
    }
    $course_description = isset($_POST['course_description']) ? $_POST['course_description'] : -1;
    if ($course_description == -1) {
        wp_die('Invalid course!');
    }
    $stages_order_ids = isset($_POST['stages_order_ids']) ? $_POST['stages_order_ids'] : -1;
    if ($stages_order_ids == -1) {
        wp_die('Invalid course!');
    }

    global $wpdb;
    $courses_table = $wpdb->prefix . 'WingAI_Courses';
    $wpdb->update($courses_table, array('title' => $course_title, 'description' => $course_description), array('id' => $course_id), array('%s', '%s'), array('%d'));

    $stages_table = $wpdb->prefix . 'WingAI_Course_Stages';
    $i = 0;
    foreach ($stages_order_ids as $stage_id) {
        $wpdb->update($stages_table, array('weight' => $i++), array('id' => $stage_id), array('%d'), array('%d'));
    }
}

function wingai_update_course_list()
{
    $course_ids = isset($_POST['course_ids']) ? $_POST['course_ids'] : -1;
    if ($course_ids == -1) {
        wp_die('Invalid course IDs!');
    }

    global $wpdb;
    $courses_table = $wpdb->prefix . 'WingAI_Courses';
    $i = 0;
    foreach ($course_ids as $course_id) {
        $wpdb->update($courses_table, array('weight' => $i++), array('id' => $course_id), array('%d'), array('%d'));
    }
}