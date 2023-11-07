<?php
// Add a custom URL endpoint for editing a course
function wingai_add_edit_course_block_endpoint()
{
    //add pages if the user is an admin but do not add it to the menu
    add_submenu_page(
        null,
        'Edit Course',
        'Edit Course',
        'manage_options',
        'wingai-edit-course-block',
        'wingai_edit_course_block_page'
    );
}

add_action('admin_menu', 'wingai_add_edit_course_block_endpoint');


function wingai_edit_course_block_page()
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

        // Get the course content
        $course_content = json_decode($course->content, true);

        // Get the course title
        $course_title = $course->title;

        // Get the course description
        $course_description = $course->description;

        // Get the course stages
        $course_stages = $course_content['stages'];

        // Get the course blocks
        $course_blocks = $course_content['blocks'];

        // Get the course block types
        $course_block_types = $course_content['blockTypes'];

    }
}