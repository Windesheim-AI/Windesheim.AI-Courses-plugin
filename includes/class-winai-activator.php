<?php

class WinAI_Activator
{
	public static function activate()
	{
		global $wpdb;
		flush_rewrite_rules();
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// it the old table exists, delete it
		$old_table = $wpdb->prefix . 'winai_course';
		$wpdb->query("DROP TABLE IF EXISTS $old_table");

		// Create generative_ai_tutorial table
		$generative_ai_tutorial_table = $wpdb->prefix . 'WinAI_Courses';
		$charset_collate = $wpdb->get_charset_collate();
		$generative_ai_tutorial_sql = "CREATE TABLE IF NOT EXISTS $generative_ai_tutorial_table (
            id INT NOT NULL AUTO_INCREMENT,
            title VARCHAR(255),
			description TEXT,
			imageLink TEXT,
			weight INT,
            PRIMARY KEY  (id)
        ) $charset_collate;";
		dbDelta($generative_ai_tutorial_sql);

		// Create tutorial_stages table
		$tutorial_stages_table = $wpdb->prefix . 'WinAI_Course_Stages';
		$tutorial_stages_sql = "CREATE TABLE IF NOT EXISTS $tutorial_stages_table (
            id INT NOT NULL AUTO_INCREMENT,
            course_id INT,
            title VARCHAR(255),
			weight INT,
            PRIMARY KEY  (id),
            FOREIGN KEY (course_id) REFERENCES $generative_ai_tutorial_table(id)
        ) $charset_collate;";
		dbDelta($tutorial_stages_sql);

		// Create stage_blocks table
		$stage_blocks_table = $wpdb->prefix . 'WinAI_Stage_Blocks';
		$stage_blocks_sql = "CREATE TABLE IF NOT EXISTS $stage_blocks_table (
			id INT NOT NULL AUTO_INCREMENT,
			stage_id INT,
			block_type VARCHAR(255),
			content TEXT,
			weight INT,
			PRIMARY KEY  (id),
			FOREIGN KEY (stage_id) REFERENCES $tutorial_stages_table(id)
		) $charset_collate;";
		dbDelta($stage_blocks_sql);
	}
}
