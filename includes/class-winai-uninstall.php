<?php

class WinAI_Uninstall
{

    public static function uninstall()
    {
        global $wpdb;
        $courses_table = $wpdb->prefix . 'WinAI_Courses';
        $stages_table = $wpdb->prefix . 'WinAI_course_Stages';
        $blocks_table = $wpdb->prefix . 'WinAI_Stage_Blocks';

        $wpdb->query("DROP TABLE IF EXISTS $blocks_table");
        $wpdb->query("DROP TABLE IF EXISTS $stages_table");
        $wpdb->query("DROP TABLE IF EXISTS $courses_table");
    }
}
