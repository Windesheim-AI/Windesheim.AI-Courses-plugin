<?php
if (!defined('ABSPATH')) {
    exit;
}

function Authorize()
{
    // if current user is loggedin and admin else die
    $user = is_user_logged_in();
    $adm = is_admin();

    if (!$user || !$adm) {
        wp_die('Unauthorized');
    }
}

function SaveBlock()
{
    Authorize();
    $id = isset($_POST['id']) ? $_POST['id'] : -1;
    if ($id == -1) {
        wp_die('Invalid block ID!');
    }
    $block = isset($_POST['block']) ? $_POST['block'] : -1;
    if ($block == -1) {
        wp_die('Invalid block!');
    }
    global $wpdb;
    $blocks_table = $wpdb->prefix . 'WinAI_Stage_Blocks';

    $wpdb->update($blocks_table, array('content' => json_encode($block)), array('id' => $id), array('%s'), array('%d'));
}

function AddBlock()
{
    Authorize();
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
    $blocks_table = $wpdb->prefix . 'WinAI_Stage_Blocks';
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

function winai_edit_stage_title()
{
    Authorize();
    $stage_id = isset($_POST['stage_id']) ? $_POST['stage_id'] : -1;
    if ($stage_id == -1) {
        wp_die('Invalid stage ID!');
    }
    $title = isset($_POST['title']) ? $_POST['title'] : -1;
    if ($title == -1) {
        wp_die('Invalid title!');
    }

    global $wpdb;
    $stages_table = $wpdb->prefix . 'WinAI_Course_Stages';
    $wpdb->update($stages_table, array('title' => $title), array('id' => $stage_id), array('%s'), array('%d'));
}

function winai_delete_block()
{
    Authorize();
    $block_id = isset($_POST['block_id']) ? $_POST['block_id'] : -1;
    if ($block_id == -1) {
        wp_die('Invalid block ID!');
    }

    global $wpdb;
    $blocks_table = $wpdb->prefix . 'WinAI_Stage_Blocks';
    $wpdb->delete($blocks_table, array('id' => $block_id), array('%d'));

    winai_reorder_weight_blocks($_POST['stage_id']);
}

function winai_reorder_weight_blocks($stage_id)
{
    Authorize();
    //make the blocks weight in order
    global $wpdb;
    $blocks_table = $wpdb->prefix . 'WinAI_Stage_Blocks';
    $blocks = $wpdb->get_results("SELECT id FROM $blocks_table WHERE stage_id = $stage_id ORDER BY weight ASC");
    $i = 0;
    foreach ($blocks as $block) {
        $wpdb->update($blocks_table, array('weight' => $i++), array('id' => $block->id), array('%d'), array('%d'));
    }
}

function winai_set_block_weights()
{
    Authorize();
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
    $blocks_table = $wpdb->prefix . 'WinAI_Stage_Blocks';
    $i = 0;
    foreach ($ids as $id) {
        $wpdb->update($blocks_table, array('weight' => $i++), array('id' => $id), array('%d'), array('%d'));
    }
}

function winai_update_course()
{
    Authorize();
    $course_id = isset($_POST['course_id']) ? $_POST['course_id'] : -1;
    if ($course_id == -1) {
        wp_die('Invalid course ID!');
    }
    $course_title = isset($_POST['course_title']) ? $_POST['course_title'] : -1;
    if ($course_title == -1) {
        wp_die('Invalid course title!');
    }
    $course_description = isset($_POST['course_description']) ? $_POST['course_description'] : -1;
    if ($course_description == -1) {
        wp_die('Invalid course description!');
    }
  $course_imageLink = isset($_POST['course_imageLink']) ? $_POST['course_imageLink'] : NULL;

    $stages_order_ids = isset($_POST['stages_order_ids']) ? $_POST['stages_order_ids'] : -1;

    global $wpdb;
    $courses_table = $wpdb->prefix . 'WinAI_Courses';
    $wpdb->update($courses_table, array('title' => $course_title, 'description' => $course_description, 'imageLink' => $course_imageLink), array('id' => $course_id), array('%s', '%s', '%s'), array('%d'));

    // Do not update stages if we don't have stages.
  if ($stages_order_ids == -1) {
    return;
  }

    $stages_table = $wpdb->prefix . 'WinAI_Course_Stages';
    $i = 0;
    foreach ($stages_order_ids as $stage_id) {
        $wpdb->update($stages_table, array('weight' => $i++), array('id' => $stage_id), array('%d'), array('%d'));
    }
}

function winai_update_course_list()
{
    Authorize();
    $course_ids = isset($_POST['course_ids']) ? $_POST['course_ids'] : -1;
    if ($course_ids == -1) {
        wp_die('Invalid course IDs!');
    }

    global $wpdb;
    $courses_table = $wpdb->prefix . 'WinAI_Courses';
    $i = 0;
    foreach ($course_ids as $course_id) {
        $wpdb->update($courses_table, array('weight' => $i++), array('id' => $course_id), array('%d'), array('%d'));
    }
}

function winai_delete_course()
{
    Authorize();
    $course_id = isset($_POST['course_id']) ? $_POST['course_id'] : -1;
    if ($course_id == -1) {
        wp_die('Invalid course ID!');
    }
    winai_delete_course_stages($course_id);
    global $wpdb;
    $courses_table = $wpdb->prefix . 'WinAI_Courses';
    $wpdb->delete($courses_table, array('id' => $course_id), array('%d'));

    winai_reorder_weight_courses();
}

function winai_delete_course_stages($course_id)
{
    Authorize();
    global $wpdb;
    $stages_table = $wpdb->prefix . 'WinAI_Course_Stages';
    $stage_ids = $wpdb->get_col("SELECT id FROM $stages_table WHERE course_id = $course_id");
    winai_delete_stage_blocks($stage_ids);
    foreach ($stage_ids as $stage_id) {
        $wpdb->delete($stages_table, array('id' => $stage_id), array('%d'));
    }
}

function winai_delete_stage_blocks($stage_ids)
{
    Authorize();
    global $wpdb;
    $blocks_table = $wpdb->prefix . 'WinAI_Stage_Blocks';
    foreach ($stage_ids as $stage_id) {
        $wpdb->delete($blocks_table, array('stage_id' => $stage_id), array('%d'));
    }

}

function winai_reorder_weight_courses()
{
    Authorize();
    //make the courses weight in order
    global $wpdb;
    $courses_table = $wpdb->prefix . 'WinAI_Courses';
    $courses = $wpdb->get_results("SELECT id FROM $courses_table ORDER BY weight ASC");
    $i = 0;
    foreach ($courses as $course) {
        $wpdb->update($courses_table, array('weight' => $i++), array('id' => $course->id), array('%d'), array('%d'));
    }
}

function winai_delete_stage()
{
    Authorize();
    $stage_id = isset($_POST['stage_id']) ? $_POST['stage_id'] : -1;
    if ($stage_id == -1) {
        wp_die('Invalid stage ID!');
    }
    global $wpdb;
    winai_delete_stage_blocks([$stage_id]);
    $stages_table = $wpdb->prefix . 'WinAI_Course_Stages';
    $wpdb->delete($stages_table, array('id' => $stage_id), array('%d'));

    winai_reorder_weight_stages($_POST['course_id']);
}

function winai_reorder_weight_stages($course_id)
{
    Authorize();
    //make the stages weight in order
    global $wpdb;
    $stages_table = $wpdb->prefix . 'WinAI_Course_Stages';
    $stages = $wpdb->get_results("SELECT id FROM $stages_table WHERE course_id = $course_id ORDER BY weight ASC");
    $i = 0;
    foreach ($stages as $stage) {
        $wpdb->update($stages_table, array('weight' => $i++), array('id' => $stage->id), array('%d'), array('%d'));
    }
}

function winai_add_stage()
{
    Authorize();
    $course_id = isset($_POST['course_id']) ? $_POST['course_id'] : -1;
    if ($course_id == -1) {
        wp_die('Invalid course ID!');
    }
    $stage_title = isset($_POST['stage_title']) ? $_POST['stage_title'] : -1;
    if ($stage_title == -1) {
        wp_die('Invalid stage title!');
    }

    global $wpdb;
    $stages_table = $wpdb->prefix . 'WinAI_Course_Stages';
    //get the max weight
    $max_weight = $wpdb->get_var("SELECT MAX(weight) FROM $stages_table WHERE course_id = $course_id");
    $max_weight = $max_weight == null ? 0 : $max_weight + 1;
    $stage_data = [
        'course_id' => $course_id,
        'title' => $stage_title,
        'weight' => $max_weight,
    ];
    $wpdb->insert($stages_table, $stage_data);
}

function winai_add_course()
{
    Authorize();
    $course_title = isset($_POST['course_title']) ? $_POST['course_title'] : -1;
    if ($course_title == -1) {
        wp_die('Invalid course title!');
    }
    $course_description = isset($_POST['course_description']) ? $_POST['course_description'] : -1;
    if ($course_description == -1) {
        wp_die('Invalid course description!');
    }
  $course_imageLink = isset($_POST['course_imageLink']) ? $_POST['course_imageLink'] : NULL;

    global $wpdb;
    $courses_table = $wpdb->prefix . 'WinAI_Courses';
    //get the max weight
    $max_weight = $wpdb->get_var("SELECT MAX(weight) FROM $courses_table");
    $max_weight = $max_weight == null ? 0 : $max_weight + 1;
    $course_data = [
        'title' => $course_title,
        'description' => $course_description,
        'imageLink' => $course_imageLink,
        'weight' => $max_weight,
    ];
    $wpdb->insert($courses_table, $course_data);

    echo "Added prompt!";
    echo json_encode($course_data);
    echo json_encode($wpdb->get_results("SELECT * FROM $courses_table"));
    exit(201);
}