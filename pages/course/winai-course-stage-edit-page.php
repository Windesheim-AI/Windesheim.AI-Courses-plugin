<?php
// Add a custom URL endpoint for editing a course
function winai_add_edit_course_stage_endpoint()
{
    //add pages if the user is an admin but do not add it to the menu
    add_submenu_page(
        null,
        'Edit Course',
        'Edit Course',
        'manage_options',
        'winai-edit-course-stage',
        'winai_edit_course_stage_page'
    );
}

add_action('admin_menu', 'winai_add_edit_course_stage_endpoint');

function winai_edit_course_stage_page()
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
        include WinAI_PLUGIN_DIR . 'types/course-data-types.php';
        include WinAI_PLUGIN_DIR . 'utils/type-validator-util.php';

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
        <div class="wrap">
            <h1>Edit Course Stage</h1>
            <h2>Course:
                <?php echo $course_title; ?> -
                <?php echo $stage->title; ?>
            </h2>
            <div class="col-md-4">
                <!-- make the abillity to edit the title -->
                <form id="title-edit" class="form-horizontal">
                    <div class="form-group">
                        <input type="hidden" name="id" id="id" value="<?php echo $stage_id; ?>">
                    </div>
                    <div class="form-group">
                        <label for="title" class="control-label">
                            <Title></Title>
                        </label>
                        <textarea name="text" id="title" class="form-control w-100"
                            placeholder="Title"><?php echo $stage->title; ?></textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" class="button button-primary" id="save-button-title">Save</button>
                    </div>
                </form>
                <button class="button button-primary thickbox"
                    href="#TB_inline?width=600&height=550&inlineId=add-block-modal">Add
                    Content Block</button>
                <br />
            </div>
            <table class="wp-list-table widefat fixed striped table-view-list pages sortable">
                <thead>
                    <tr>
                        <th scope="col" id="id" class="manage-column column-id column-primary sortable desc"
                            style="width: 100px;">
                            <a href="#">
                                <span>ID</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" id="blockType" class="manage-column column-blockType sortable desc">
                            <a href="#">
                                <span>Block Type</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" id="content" class="manage-column column-content sortable desc">
                            <a href="#">
                                <span>Action</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody id="the-list">
                    <?php
                    $blocks = $stage->blocks;


                    foreach ($blocks as $block) {
                        $block_type = $block->blockType;
                        ?>
                        <tr id="<?php echo $block->id; ?> ">
                            <td>
                                <?php echo $block->id; ?>
                            </td>
                            <td>
                                <?php echo $block_type; ?>
                            </td>
                            <td>
                                <button class="quick-edit button button-primary" data-block-type="<?php echo $block_type; ?>"
                                    data-block-id="<?php echo $block->id; ?>">Edit</button>
                                <button class="delete-btn-winai button button-danger" data-block-type="<?php echo $block_type; ?>"
                                    data-block-id="<?php echo $block->id; ?>">Delete</button>

                            </td>
                        </tr>

                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php


            foreach ($blocks as $block) {
                ?>
                <div class="block-edit" id="block-edit-<?php echo $block->id; ?>" style="display:none;">
                    <p>Bewerk #
                        <?php echo $block->id; ?>:
                    </p>
                    <?php
                    $block_type = $block->blockType;
                    if ($block_type == 'text') {
                        text_content_block($block, $stage_id);
                    } else if ($block_type == 'button') {
                        button_content_block($block, $stage_id);
                    } else if ($block_type == 'ai') {
                        ai_content_block($block, $stage_id);
                    } else {
                        display_error("Invalid block type: $block_type");
                    }
                    ?>
                    <!-- close button -->
                    <button class="quick-edit-hide button" data-block-type="<?php echo $block_type; ?>"
                        data-block-id="<?php echo $block->id; ?>">Close</button>
                </div>
                <?php
            }
            ?>
            <div id="add-block-modal" style="display:none;">
                <!-- make a n input where you can select to add an block opeing (AI, tect, or button) -->
                <form method="post">
                    <input type="hidden" name="action" value="winai_add_block" />
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>" />
                    <input type="hidden" name="stage_id" value="<?php echo $stage_id; ?>" />
                    <select name="block_type">
                        <option value="" selected>Select</option>
                        <option value="text">Text</option>
                        <option value="button">Button</option>
                        <option value="ai">AI</option>
                    </select>
                    <div class="add-button add-option">
                        <?php button_content_block(null, $stage_id, true); ?>
                    </div>
                    <div class="add-text add-option">
                        <?php text_content_block(null, $stage_id, true); ?>
                    </div>
                    <div class="add-ai add-option">
                        <?php ai_content_block(null, $stage_id, true); ?>
                    </div>
                </form>

            </div>

        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                //make the table sortable

                $(".sortable>tbody").sortable({
                    update: function (event, ui) {
                        var ids = [];
                        $(".sortable>tbody>tr").each(function () {
                            ids.push($(this).attr('id'));
                        });
                        //post the ids to the server
                        var data = {
                            'action': 'winai_set_block_weights',
                            'ids': ids,
                            'stage_id': <?php echo $stage_id; ?>
                        };
                        $.post(ajaxurl, data, function (response) {
                            //do nothing
                        });
                    }
                });
                $(".sortable>tbody").disableSelection();


                $('.block-edit').hide();
                $('.quick-edit').click(function (e) {
                    e.preventDefault();
                    var block_id = $(this).data('block-id');
                    var block_type = $(this).data('block-type');
                    $('.block-edit').hide();
                    $('#block-edit-' + block_id).show();
                });
                $('.quick-edit-hide').click(function (e) {
                    e.preventDefault();
                    var block_id = $(this).data('block-id');
                    var block_type = $(this).data('block-type');
                    $('.block-edit').hide();
                });

                $('.thickbox').click(function (e) {
                    e.preventDefault();
                    var href = $(this).attr('href');
                    tb_show('Add Content Block', href);
                });

                $('.add-option').hide();
                //if the block type is changed, hide all the options and show the correct one
                $('select[name="block_type"]').change(function () {
                    $('.add-option').hide();
                    var block_type = $(this).val();
                    $('.add-' + block_type).show();
                });

                $("#save-button-title").click(function (e) {
                    e.preventDefault();
                    var title = $('#title').val();
                    var data = {
                        'action': 'winai_edit_stage_title',
                        'stage_id': <?php echo $stage_id; ?>,
                        'title': title
                    };
                    $(this).html('<span class="spinner is-active"></span>');
                    $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');

                    $.post(ajaxurl, data, function (response) {
                        $('#save-button-title').html('Save');
                        $('#save-button-title').prop('disabled', false).html('Save');
                        location.reload();
                    });
                });

                $('.delete-btn-winai').click(function (e) {
                    e.preventDefault();
                    var block_id = $(this).data('block-id');
                    var block_type = $(this).data('block-type');
                    if (confirm("Are you sure you want to delete this block?")) {
                        var data = {
                            'action': 'winai_delete_block',
                            'block_id': block_id,
                            'block_type': block_type
                        };
                        $(this).html('<span class="spinner is-active"></span>');
                        $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');

                        $.post(ajaxurl, data, function (response) {
                            $('.delete-btn-winai').html('Delete');
                            $('.delete-btn-winai').prop('disabled', false).html('Delete');
                            location.reload();
                        });
                    }
                })
            });
        </script>
        <?php
    } else {
        display_error('Course ID or Stage ID not found.');
    }
}


add_action('wp_ajax_winai_edit_stage_title', 'winai_edit_stage_title');
add_action('wp_ajax_winai_delete_block', 'winai_delete_block');
add_action('wp_ajax_winai_set_block_weights', 'winai_set_block_weights');