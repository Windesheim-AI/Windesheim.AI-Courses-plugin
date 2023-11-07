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
                    <br>
                    <input type="text" name="title" id="title" value="<?php echo esc_attr($json_content['title']); ?>">
                    <br>
                    <label for="description">Description:</label>
                    <br>
                    <textarea name="description"
                        id="description"><?php echo esc_textarea($json_content['description']); ?></textarea>
                    <br>
                    <input type="submit" name="save_course" value="Save Course">
                </form>
                <h3>Stages</h3>
                <?php
                //stages are stored in json ['stages']
                echo esc_html($course);
                $stages = json_decode($course->stages, true);
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
                            <th scope="col" id="actions" class="manage-column column-actions">
                                <span>Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="the-list">
                        <?php foreach ($stages as $stage): ?>
                            <?php
                            echo esc_html($stage);
                            $json_content = json_decode($stage->content, true);
                            if ($json_content !== null) {
                                ?>
                                <tr id="course-<?php echo $stage->id; ?>"
                                    class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-uncategorized">
                                    <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                                        <strong>
                                            <a class="row-title"
                                                href="<?php echo admin_url('admin.php?page=wingai-edit-stage&stage_id=' . $stage->id); ?>"
                                                aria-label="“<?php echo esc_attr($json_content['title']); ?>” (Edit)">
                                                <?php echo esc_html($json_content['title']); ?>
                                            </a>
                                        </strong>
                                    </td>
                                    <td class="author column-author" data-colname="Author">
                                        <?php echo esc_html($json_content['description']); ?>
                                    </td>
                                    <td class="author column-author" data-colname="Author">
                                        <a
                                            href="<?php echo admin_url('admin.php?page=wingai-edit-stage&stage_id=' . $stage->id); ?>">Edit</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php
        } else {
            echo 'Course not found.';
        }
    }
}


