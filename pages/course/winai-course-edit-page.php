<?php
// Add a custom URL endpoint for editing a course
function winai_add_edit_course_endpoint()
{
    //add pages if the user is an admin but do not add it to the menu
    add_submenu_page(
        null,
        'Edit Course',
        'Edit Course',
        'manage_options',
        'winai-edit-course',
        'winai_edit_course_page'
    );
}

add_action('admin_menu', 'winai_add_edit_course_endpoint');


function winai_edit_course_page()
{
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['course_id'])) {
        // Get the course from the database
        $course_id = (int) ($_GET['course_id'] ?? -1);
        $course = get_course_json($course_id);
        // convert the course to object
        if (!$course) {
            // Course not found
            display_error("Course with ID $course_id not found.");
            return;
        }
        $course = json_decode(json_encode($course), false);

        // Include the JSON structure template
        include WinAI_PLUGIN_DIR . 'types/course-data-types.php';

        // Include the validation functions
        include WinAI_PLUGIN_DIR . 'utils/type-validator-util.php';

        // Validate the structure using the validateStructure function
        if (!validateData($course, new Course())) {
            display_error('Invalid course content.');
            return;
        }

        echo '<h1>Edit Course</h1>';

        // Display the form an editor for the course
        ?>

        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="winai_update_course">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

            <div class="form-group">
                <label for="course_title">Title</label>
                <br />
                <input type="text" class="form-control" id="course_title" name="course_title" required="required" style="width: 80%;"
                    value="<?php echo $course->title; ?>">
            </div>

            <div class="form-group">
                <label for="course_description">Description</label>
                <br />
                <textarea class="form-control" id="course_description" name="course_description" required="required" style="width: 80%;"
                    rows="3"><?php echo $course->description; ?></textarea>
            </div>

            <div class="form-group">
                <label for="course_imageLink">Image link</label>
                <br />
                <input type="text" class="form-control" id="course_imageLink" name="course_imageLink" style="width: 80%;"
                       value="<?php echo $course->imageLink; ?>">
            </div>

            <div class="form-group">
                <label for="course_stages">Stages</label>
                <br />
                <button type="button" class="button button-primary winai_add_stage thickbox"
                    href="#TB_inline?width=600&height=550&inlineId=add-stage-modal">Add stage</button>
                <table class="wp-list-table widefat fixed striped sortable">
                    <thead>
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Stage content blocks</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < count($course->stages); $i++):
                            $stage = $course->stages[$i];
                            ?>
                            <tr id="<?php echo $stage->id ?>">
                                <td>
                                    <?php echo $stage->title; ?>
                                </td>
                                <td>
                                    <?php echo count($stage->blocks); ?>
                                </td>
                                <td>
                                    <button class="button button-primary winai_edit_btn"
                                        href="admin.php?page=winai-edit-course-stage&course_id=<?php echo "$course_id&stage_id=$stage->id" ?>">Edit</button>
                                    <button class="button button-danger winai_delete"
                                        courseid="<?php echo $stage->id ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <button type="submit" class="button button-primary winai_save_course">Save</button>
        </form>

        <div id="add-stage-modal" style="display:none;">
            <form method="post">
                <input type="hidden" name="action" value="winai_add_stage" />
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>" />
                <input type="text" name="stage_title" placeholder="Stage title" />
                <button type="submit" class="button button-primary btn-add-stage">Add stage</button>
            </form>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                $(".sortable>tbody").sortable();
                $(".sortable>tbody").disableSelection();

                $('.winai_save_course').click(function (e) {
                    e.preventDefault();
                    var ids = [];
                    $(".sortable>tbody>tr").each(function () {
                        ids.push($(this).attr('id'));
                    });
                    var data = {
                        'action': 'winai_update_course',
                        'course_id': <?php echo $course_id; ?>,
                        'course_title': $('#course_title').val(),
                        'course_description': $('#course_description').val(),
                        'course_imageLink': $('#course_imageLink').val(),
                        'stages_order_ids': ids,
                    };
                    $(this).html('<span class="spinner is-active"></span>');
                    $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');

                    $.post(ajaxurl, data, function (response) {
                        $('.winai_save_course').html('Save');
                        $('.winai_save_course').prop('disabled', false).html('Save');
                        console.log('save');
                        // location.reload();
                    });
                })

                $('.winai_edit_btn').click(function (e) {
                    e.preventDefault();
                    window.location.href = $(this).attr('href');
                })

                $('.winai_delete').click(function (e) {
                    e.preventDefault();

                    if (confirm("Are you sure you want to delete this stage?")) {
                        var stage_id = $(this).attr('courseid');
                        var data = {
                            'action': 'winai_delete_stage',
                            'stage_id': stage_id,
                        };

                        $(this).html('<span class="spinner is-active"></span>');
                        $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');

                        $.post(ajaxurl, data, function (response) {
                            $('.winai_save_course').prop('disabled', false).html('Delete');
                            location.reload();
                        });
                    }
                });

                $('.btn-add-stage').click(function (e) {
                    e.preventDefault();
                    var data = {
                        'action': 'winai_add_stage',
                        'course_id': <?php echo $course_id; ?>,
                        'stage_title': $('input[name="stage_title"]').val(),
                    };

                    $(this).html('<span class="spinner is-active"></span>');
                    $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');

                    $.post(ajaxurl, data, function (response) {
                        location.reload();
                    });
                });

                $('.thickbox').click(function (e) {
                    e.preventDefault();
                    var href = $(this).attr('href');
                    tb_show('Add Stage', href);
                });
            });
        </script>

        <?php

    }

}

add_action('wp_ajax_winai_update_course', 'winai_update_course');
add_action('wp_ajax_winai_delete_stage', 'winai_delete_stage');
add_action('wp_ajax_winai_add_stage', 'winai_add_stage');



