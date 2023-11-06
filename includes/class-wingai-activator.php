<?php

class WingAI_Activator
{

	public static function activate()
	{
		global $wpdb;
		flush_rewrite_rules();
		$table_name = $wpdb->prefix . 'wingai_course';
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			content text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
