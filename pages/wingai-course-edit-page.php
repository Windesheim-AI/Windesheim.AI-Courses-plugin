<?php
// Add a custom URL endpoint for editing a course
function wingai_add_edit_course_endpoint()
{
    //add pages if the user is an admin but do not add it to the menu
    add_submenu_page(
        null,
        'Edit Course',
        'Edit Course',
        'manage_options',
        'wingai-edit-course',
        'wingai_edit_course_page'
    );
}

add_action('admin_menu', 'wingai_add_edit_course_endpoint');
function wingai_edit_course_page()
{
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'wingai_course';

    if (isset($_POST['save_course'])) {
        $course_id = intval($_POST['course_id']);
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_textarea_field($_POST['description']);

        $wpdb->update(
            $table_name,
            ['content' => json_encode(['title' => $title, 'description' => $description])],
            ['id' => $course_id],
            ['%s'],
            ['%d']
        );
    }

    if (isset($_GET['course_id'])) {
        $course_id = intval($_GET['course_id']);
        $course = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $course_id));

        if ($course) {
            $json_content = json_decode($course->content, true);
            ?>
            <div class="wrap">
                <h1>Edit Course</h1>
                <form method="post" action="">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title" value="<?php echo esc_attr($json_content['title']); ?>">
                    <br>
                    <label for="description">Description:</label>
                    <textarea name="description"
                        id="description"><?php echo esc_textarea($json_content['description']); ?></textarea>
                    <br>
                    <input type="submit" name="save_course" value="Save Course">
                </form>
            </div>
            <?php
        } else {
            echo 'Course not found.';
        }
    }
}


