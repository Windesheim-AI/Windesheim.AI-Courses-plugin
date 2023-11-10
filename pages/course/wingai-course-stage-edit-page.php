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

    if (isset($_GET['course_id']) && isset($_GET['stage_id'])) {
        $course_id = (int) ($_GET['course_id'] ?? -1);
        $stage_id = (int) ($_GET['stage_id'] ?? -1);

        // Get the course from the database
        $course = get_course_json($course_id);
        $course = json_decode(json_encode($course), false);

        if (!$course) {
            display_error("Course with ID $course_id not found.");
            return;
        }

        // Include necessary files
        include WingAI_PLUGIN_DIR . 'types/course-data-types.php';
        include WingAI_PLUGIN_DIR . 'utils/type-validator-util.php';

        // Validate the structure using the validateData function
        if (!validateData($course, new Course())) {
            display_error('Invalid course content.');
            return;
        }

        // Get the course content
        $course_title = $course->title;
        $course_stages = $course->stages;

        // Check if the provided stage ID is valid and if the course has a stage with that ID
        $stage_found = false;
        $stage;
        foreach ($course_stages as $stages) {
            if ($stages->id == $stage_id) {
                $stage = $stages;
                $stage_found = true;
                break;
            }
        }

        if (!$stage_found || !$stage) {
            display_error("Stage with ID $stage_id not found.");
            return;
        }

        ?>
        <h1>Edit Course Stage</h1>
        <h2>Course:
            <?php echo $course_title; ?> -
            <?php echo $stage->title; ?>
        </h2>

        <form method="post">
            <?php
            $blocks = $stage->blocks;

            // Display the blocks using wp_editor()
            foreach ($blocks as $block) {
                $block_type = $block->blockType;
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
