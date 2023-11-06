<?php

// make a page where the courses will be displayed
function wingai_add_settings_page()
{
    add_menu_page(
        // Page title
        'WingAI Courses Settings',
        // Menu title
        'WingAI Courses',
        // Capability
        'manage_options',
        // Menu slug
        'wingai-courses-settings',
        // Function to render the settings page
        'wingai_render_settings_page',
        // Icon URL
        'dashicons-admin-generic',
        100 // Position
    );
}

add_action('admin_menu', 'wingai_add_settings_page');
function wingai_render_settings_page()
{
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Render the settings page
    // Display a list of all courses in the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'wingai_course';
    $courses = $wpdb->get_results("SELECT * FROM $table_name");

    ?>
    <div class="wrap">
        <h1>
            <?php echo esc_html(get_admin_page_title()); ?>
        </h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" id="id" class="manage-column column-id column-primary sortable desc" style="width: 100px;">
                        <a href="#">
                            <span>ID</span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" id="title" class="manage-column column-title sortable desc">
                        <a href="#">
                            <span>Title</span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" id="description" class="manage-column column-description sortable desc">
                        <a href="#">
                            <span>Description</span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" id="stages" class="manage-column column-description sortable desc">
                        <a href="#">
                            <span>Stages Count</span>
                            <span class="sorting-indicator"></span>
                        </a>
                    </th>
                    <th scope="col" id="actions" class="manage-column column-actions">
                        <span>Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php foreach ($courses as $course): ?>
                    <?php
                    $json_content = json_decode($course->content, true);
                    if ($json_content !== null) {
                        // Display the main properties
                        echo '<tr>';
                        echo '<td>' . $course->id . '</td>';
                        echo '<td>' . esc_html($json_content['title']) . '</td>';
                        echo '<td>' . esc_html($json_content['description']) . '</td>';
                        $stages_count = isset($json_content['stages']) && is_array($json_content['stages']) ? count($json_content['stages']) : 0;
                        echo '<td>' . esc_html($stages_count) . '</td>';
                        echo '<td><a href="admin.php?page=wingai-edit-course&course_id=' . $course->id . '">View --></a></td>';
                        echo '</tr>';
                    }
                    ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

