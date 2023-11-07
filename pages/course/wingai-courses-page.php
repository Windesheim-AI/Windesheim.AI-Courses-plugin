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
        // Icon URL use ./images/your-icon.png
        'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjxzdmcgaWQ9ImxvZ29zYW5kdHlwZXNfY29tIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNTAgMTUwIj4NCiAgICA8cGF0aCBmaWxsPSIjRkZjYjA1Ig0KICAgICAgICBkPSJNNDAuNDcsMTMzLjU4bDE5LjcyLTExLjg1LDUuNjItMzkuOTQsMTUuODQsNTEuOCwyNy4zMS0xNi40MUwxNDEuMDcsNS4xNWMtMTEuNzQsMi42NC0yMy4yNSw2LjIzLTM0LjQxLDEwLjcybC0xNC45Nyw3MC40LTEwLjI5LTU4LjM2Yy0xMC45Myw2LjA4LTIxLjM0LDEzLjA2LTMxLjExLDIwLjg3bC02LjcxLDQ3Ljc0LTExLjkyLTMxLjExYy03Ljc0LDcuNzMtMTQuOTIsMTYuMDEtMjEuNDcsMjQuNzdsLS4wNCwuMDksMzAuMyw0My4zWiIgLz4NCjwvc3ZnPg==',
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

    //make a list with corrupted courses 
    $corrupted_courses = [];

    ?>
    <div class="wrap">
        <h1>
            <?php echo esc_html(get_admin_page_title()); ?>
        </h1>
        <?php
        if (isset($_POST['delete_corrupted_courses'])) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p>Corrupted courses have been deleted successfully</p>
            </div>
            <?php
        }
        ?>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" id="id" class="manage-column column-id column-primary sortable desc"
                        style="width: 100px;">
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
                    <th scope="col" id="stages" class="manage-column column-description sortable desc"
                        style="width: 100px;">
                        <a href="#">
                            <span>Stages</span>
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
                    } else {
                        // append the corrupted course to the corrupted courses list
                        array_push($corrupted_courses, $course);
                    }
                    ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        //if deleted corrupted courses
        if (!isset($_POST['delete_corrupted_courses']) && count($corrupted_courses) > 0) {
            ?>
            <h2>Corrupted Courses</h2>
            <p>
                <?php echo count($corrupted_courses) ?> corrupt cources have been found
            </p>
            <!-- propmpt to delete them -->
            <form action="admin.php?page=wingai-courses-settings" method="post">
                <input type="submit" name="delete_corrupted_courses" value="Delete Corrupted Courses">
            </form>
            <?php
        }
        ?>

    </div>
    <?php
    //if the user wants to delete the corrupted courses
    if (isset($_POST['delete_corrupted_courses'])) {
        foreach ($corrupted_courses as $corrupted_course) {
            $wpdb->delete($table_name, ['id' => $corrupted_course->id], ['%d']);
        }
    }
}

