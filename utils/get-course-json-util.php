<?php
//if directly accessed return error
if (!defined('ABSPATH')) {
    exit;
}

function get_course_json($id)
{
    global $wpdb;
    $courses_table = $wpdb->prefix . 'WingAI_Courses';
    $stages_table = $wpdb->prefix . 'WingAI_Course_Stages';
    $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';

    $result_course = $wpdb->get_row("SELECT * FROM $courses_table WHERE id = $id");
    if (!$result_course) {
        return null;
    }
    $result_stages = $wpdb->get_results("SELECT * FROM $stages_table WHERE course_id = $id");

    $result = [
        'id' => $result_course->id,
        'title' => $result_course->title,
        'description' => $result_course->description,
        'stages' => [],
    ];
    foreach ($result_stages as $stage) {
        $blocks = $wpdb->get_results("SELECT * FROM $blocks_table WHERE stage_id = $stage->id");
        $result['stages'][] = [
            'id' => $stage->id,
            'title' => $stage->title,
            'blocks' => [],
        ];
        foreach ($blocks as $block) {
            $result['stages'][count($result['stages']) - 1]['blocks'][] = [
                'id' => $block->id,
                'blockType' => $block->block_type,
                'content' => json_decode($block->content),
            ];
        }
    }
    return $result;
}