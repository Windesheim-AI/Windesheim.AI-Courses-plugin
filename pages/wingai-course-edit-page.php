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
    $course_not_found = false;

    if (isset($_GET['course_id'])) {
        // Get the course from the database
        $course_id = (int) ($_GET['course_id'] ?? -1);
        $course = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $course_id");

        if (!$course) {
            // Course not found
            display_error("Course with ID $course_id not found.");
            return;
        }

        // Include the JSON structure template
        include WingAI_PLUGIN_DIR . 'types/course-data-types.php';

        // Include the validation functions
        include WingAI_PLUGIN_DIR . 'utils/type-validator-util.php';

        // Try to decode the JSON content
        $course_content = json_decode($course->content, true);

        if ($course_content === null && json_last_error() !== JSON_ERROR_NONE) {
            // JSON decoding failed, and there was an error
            echo 'Invalid JSON data in the course content.';
            return;
        }

        // Validate the structure using the validateStructure function
        if (!validateData($course_content, new Course())) {
            echo 'Invalid JSON structure in the course content.';
            return;
        }

        echo '<h1>Edit Course</h1>';

        // Display the form an editor for the course
        ?>

        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="wingai_update_course">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

            <div class="form-group">
                <label for="course_title">Title</label>
                <br />
                <input type="text" class="form-control" id="course_title" name="course_title" style="width: 80%;"
                    value="<?php echo $course_content['title']; ?>">
            </div>

            <div class="form-group">
                <label for="course_description">Description</label>
                <br />
                <textarea class="form-control" id="course_description" name="course_description" style="width: 80%;"
                    rows="3"><?php echo $course_content['description']; ?></textarea>
            </div>

            <div class="form-group">
                <label for="course_stages">Stages</label>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Stage content blocks</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < count($course_content['stages']); $i++):
                            $stage = $course_content['stages'][$i];
                            ?>
                            <tr>
                                <td>
                                    <?php echo $stage['title']; ?>
                                </td>
                                <td>
                                    <?php echo count($stage['description']); ?>
                                </td>
                                <td>
                                    <a href="admin.php?page=wingai-edit-course-block&course_id=<?php echo "$course_id&block=$i" ?>">View
                                        --></a>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>

        <?php

    }

}



