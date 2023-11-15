<?php

class WingAI_Uninstall
{

    public static function uninstall()
    {
        global $wpdb;
        $courses_table = $wpdb->prefix . 'WingAI_Courses';
        $stages_table = $wpdb->prefix . 'WingAI_Stages';
        $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';
        $wpdb->query("DROP TABLE IF EXISTS $courses_table");
        $wpdb->query("DROP TABLE IF EXISTS $stages_table");
        $wpdb->query("DROP TABLE IF EXISTS $blocks_table");
    }
}
