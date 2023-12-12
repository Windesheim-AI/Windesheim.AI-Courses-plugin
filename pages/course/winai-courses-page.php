<?php

// make a page where the courses will be displayed
function winai_add_settings_page()
{
    add_menu_page(
        // Page title
        'Courses Settings',
        // Menu title
        'Courses',
        // Capability
        'manage_options',
        // Menu slug
        'winai-courses-settings',
        // Function to render the settings page
        'winai_render_settings_page',
        // Icon URL use ./images/your-icon.png
        'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjxzdmcgaWQ9ImxvZ29zYW5kdHlwZXNfY29tIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNTAgMTUwIj4NCiAgICA8cGF0aCBmaWxsPSIjRkZjYjA1Ig0KICAgICAgICBkPSJNNDAuNDcsMTMzLjU4bDE5LjcyLTExLjg1LDUuNjItMzkuOTQsMTUuODQsNTEuOCwyNy4zMS0xNi40MUwxNDEuMDcsNS4xNWMtMTEuNzQsMi42NC0yMy4yNSw2LjIzLTM0LjQxLDEwLjcybC0xNC45Nyw3MC40LTEwLjI5LTU4LjM2Yy0xMC45Myw2LjA4LTIxLjM0LDEzLjA2LTMxLjExLDIwLjg3bC02LjcxLDQ3Ljc0LTExLjkyLTMxLjExYy03Ljc0LDcuNzMtMTQuOTIsMTYuMDEtMjEuNDcsMjQuNzdsLS4wNCwuMDksMzAuMyw0My4zWiIgLz4NCjwvc3ZnPg==',
        50 // Position
    );
}

add_action('admin_menu', 'winai_add_settings_page');
function winai_render_settings_page()
{
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Render the settings page
    // Display a list of all courses in the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'WinAI_Courses';

    // Get all course ids from the database
    $course_ids = $wpdb->get_col("SELECT id FROM $table_name ORDER BY weight ASC");
    $courses = [];
    foreach ($course_ids as $course_id) {
        $courses[] = get_course_json($course_id);
    }

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
        <button class="button button-primary thickbox" href="#TB_inline?width=600&height=550&inlineId=add-course-modal">
            Add Course
        </button>
        <table class="wp-list-table widefat fixed striped sortable">
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
                    <th scope="col" id="imageLink" class="manage-column column-imageLink sortable desc">
                        <a href="#">
                            <span>Image link</span>
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
                    <tr id="<?php echo $course['id'] ?>">
                        <td class="id column-id has-row-actions column-primary" data-colname="ID">
                            <?php echo $course['id'] ?>
                        </td>
                        <td class="title column-title has-row-actions column-primary" data-colname="Title">
                            <?php echo $course['title'] ?>
                        </td>
                        <td class="description column-description has-row-actions column-primary" data-colname="Description">
                            <?php echo $course['description'] ?>
                        </td>
                        <td class="imageLink column-imageLink has-row-actions column-primary" data-colname="Image link">
                          <?php echo $course['imageLink'] ?>
                        </td>
                        <td class="stages column-stages has-row-actions column-primary" data-colname="Stages">
                            <?php echo count($course['stages']) ?>
                        </td>
                        <td class="actions column-actions has-row-actions column-primary" data-colname="Actions">
                            <button class="button button-primary winai_edit_btn"
                                href="admin.php?page=winai-edit-course&course_id=<?php echo $course['id'] ?>">Edit</button>
                            <button class="button button-danger winai_delete"
                                courseid="<?php echo $course['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form class="wing_ai_save_course_list">
            <div class="form-group">
                <button type="submit" class="button button-primary winai_save_course_list">Save</button>
            </div>
        </form>
        <div id="add-course-modal" style="display:none;">
            <form action="post">
                <div class="form-group">
                    <label for="course_title">Title</label>
                    <br />
                    <input type="text" class="form-control" id="course_title" name="course_title" style="width: 80%;">
                </div>

                <div class="form-group">
                    <label for="course_description">Description</label>
                    <br />
                    <textarea class="form-control" id="course_description" name="course_description" style="width: 80%;"
                        rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="course_imageLink">Image link</label>
                    <br />
                    <input type="text" class="form-control" id="course_imageLink" name="course_imageLink" style="width: 80%;">
                </div>

                <button type="submit" class="button button-primary btn-add-course">Add Course</button>
            </form>
        </div>
        <script>
            jQuery(document).ready(function ($) {
                $(".sortable>tbody").sortable();
                $(".sortable>tbody").disableSelection();

                $('.winai_save_course_list').click(function (e) {
                    e.preventDefault();
                    var ids = [];
                    $(".sortable>tbody>tr").each(function () {
                        ids.push($(this).attr('id'));
                    });
                    var data = {
                        'action': 'winai_update_course_list',
                        'course_ids': ids,
                    };
                    $(this).html('<span class="spinner is-active"></span>');
                    $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');

                    $.post(ajaxurl, data, function (response) {
                        $('.winai_save_course').html('Save');
                        $('.winai_save_course').prop('disabled', false).html('Save');
                        location.reload();
                    });
                })
                $('.winai_edit_btn').click(function (e) {
                    // go to the href of the button
                    window.location.href = $(this).attr('href');
                });
                $('.winai_delete').click(function (e) {
                    e.preventDefault();

                    if (confirm("Are you sure you want to delete this course?")) {
                        var course_id = $(this).attr('courseid');
                        var data = {
                            'action': 'winai_delete_course',
                            'course_id': course_id,
                        };

                        $(this).html('<span class="spinner is-active"></span>');
                        $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');

                        $.post(ajaxurl, data, function (response) {
                            $('.winai_save_course').prop('disabled', false).html('Delete');
                            location.reload();
                        });
                    }
                });

                $('.thickbox').click(function (e) {
                    e.preventDefault();
                    var href = $(this).attr('href');
                    tb_show('Add Course', href);
                })

                $('.btn-add-course').click(function (e) {
                    e.preventDefault();
                    var data = {
                        'action': 'winai_add_course',
                        'course_title': $('#course_title').val(),
                        'course_description': $('#course_description').val(),
                        'course_imageLink': $('#course_imageLink').val(),
                    };

                    $(this).html('<span class="spinner is-active"></span>');
                    $(this).prop('disabled', true).html('<span class="spinner is-active"></span>');

                    $.post(ajaxurl, data, function (response) {
                        $('.winai_save_course').prop('disabled', false).html('Delete');
                        location.reload();
                    });
                });
            });

        </script>
        <?php
        //if deleted corrupted courses
        if (!isset($_POST['delete_corrupted_courses']) && count($corrupted_courses) > 0) {
            ?>
            <h2>Corrupted Courses</h2>
            <p>
                <?php echo count($corrupted_courses) ?> corrupt cources have been found
            </p>
            <!-- propmpt to delete them -->
            <form action="admin.php?page=winai-courses-settings" method="post">
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

add_action('wp_ajax_winai_update_course_list', 'winai_update_course_list');
add_action('wp_ajax_winai_delete_course', 'winai_delete_course');
add_action('wp_ajax_winai_add_course', 'winai_add_course');
