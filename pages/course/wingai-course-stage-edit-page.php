<?php
// Add a custom URL endpoint for editing a course
function wingai_add_edit_course_stage_endpoint()
{
    //add pages if the user is an admin but do not add it to the menu
    add_submenu_page(
        null,
        'Edit Course',
        'Edit Course',
        'manage_options',
        'wingai-edit-course-stage',
        'wingai_edit_course_stage_page'
    );
}

add_action('admin_menu', 'wingai_add_edit_course_stage_endpoint');

function wingai_edit_course_stage_page()
{
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'wingai_course';

    if (isset($_GET['course_id']) && isset($_GET['stage_id'])) {
        $course_id = (int) ($_GET['course_id'] ?? -1);
        $stage_id = (int) ($_GET['stage_id'] ?? -1);

        // Get the course from the database
        $course = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $course_id");

        if (!$course) {
            display_error("Course with ID $course_id not found.");
            return;
        }

        // Include necessary files
        include WingAI_PLUGIN_DIR . 'types/course-data-types.php';
        include WingAI_PLUGIN_DIR . 'utils/type-validator-util.php';

        // Try to decode the JSON content
        $course_content = json_decode($course->content, true);

        if ($course_content === null || json_last_error() !== JSON_ERROR_NONE) {
            display_error('Invalid JSON in the course content.');
            return;
        }

        // Validate the structure using the validateData function
        if (!validateData($course_content, new Course())) {
            display_error('Invalid course content.');
            return;
        }

        // Get the course content
        $course_title = $course_content['title'];
        $course_stages = $course_content['stages'];

        // Check if the provided stage ID is valid
        if ($stage_id < 0 || $stage_id >= count($course_stages)) {
            display_error('Stage ID not found.');
            return;
        }

        // Get the selected stage
        $stage = $course_stages[$stage_id];

        ?>
        <h1>Edit Course Stage</h1>
        <h2>Course:
            <?php echo $course_title; ?> -
            <?php echo $stage['title']; ?>
        </h2>

        <form method="post">
            <?php
            $blocks = $stage['description'];

            // Display the blocks using wp_editor()
            foreach ($blocks as $block) {
                $block_type = $block['blockType'];
                if ($block_type == 'text') {
                    text_content_block($block);
                } else if ($block_type == 'button') {
                    button_content_block($block);
                } else if ($block_type == 'ai') {
                    ai_content_block($block);
                } else {
                    display_error("Invalid block type: $block_type");
                }
            }
            ?>
            <button type="submit" class="button button-primary">Save</button>
        </form>
        <?php
    } else {
        display_error('Course ID or Stage ID not found.');
    }
}
