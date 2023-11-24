<?php

class WingAI_Endpoints
{

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_endpoints']);
    }

    public function register_endpoints()
    {
        register_rest_route('wingai/v1', '/courses', [
            'methods' => 'GET',
            'callback' => [$this, 'get_courses'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
        ]);
        register_rest_route('wingai/v1', '/courses/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_course'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
        ]);
        register_rest_route('wingai/v1', '/courses', [
            'methods' => 'POST',
            'callback' => [$this, 'create_course'],
            'permission_callback' => function () {
                return is_user_logged_in() && current_user_can('edit_posts');
            },
        ]);
        register_rest_route('wingai/v1', '/courses/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_course'],
            'permission_callback' => function () {
                return is_user_logged_in() && current_user_can('edit_posts');
            },
        ]);
        register_rest_route('wingai/v1', '/courses/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$this, 'delete_course'],
            'permission_callback' => function () {
                return is_user_logged_in() && current_user_can('edit_posts');
            },
        ]);
    }

    public function get_courses()
    {
        //get all course ids from the database
        global $wpdb;
        $courses_table = $wpdb->prefix . 'WingAI_Courses';
        $course_ids = $wpdb->get_col("SELECT id FROM $courses_table");
        $courses = [];
        foreach ($course_ids as $course_id) {
            $courses[] = get_course_json($course_id);
        }
        return $courses;
    }


    public function get_course($request)
    {
        return get_course_json((int) ($request['id'] ?? -1));
    }

    public function create_course($request)
    {
        if (!isset($request['content'])) {
            return new WP_REST_Response('Invalid data given!', 400);
        }

        global $wpdb;

        $content = json_decode($request['content']);

        $course_weight = $wpdb->get_var("SELECT MAX(weight) FROM {$wpdb->prefix}WingAI_Courses");
        $course_weight = $course_weight == null ? 0 : $course_weight + 1;

        // Create WingAI_Courses
        $courses_table = $wpdb->prefix . 'WingAI_Courses';
        $course_data = [
            'title' => $content->title,
            'description' => $content->description,
            'weight' => $course_weight,
        ];
        $wpdb->insert($courses_table, $course_data);
        $course_id = (int) $wpdb->insert_id;
        $stagei = 0;
        foreach ($content->stages as $stage) {
            // Create WingAI_Course_Stages
            $stages_table = $wpdb->prefix . 'WingAI_Course_Stages';
            $stage_data = [
                'course_id' => $course_id,
                'title' => $stage->title,
                'weight' => $stagei++,
            ];
            $wpdb->insert($stages_table, $stage_data);
            $stage_id = (int) $wpdb->insert_id;
            $i = 0;
            foreach ($stage->blocks as $block) {
                // Create WingAI_Stage_Blocks
                $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';
                $block_data = [
                    'stage_id' => $stage_id,
                    'block_type' => $block->blockType,
                    'content' => json_encode($block->content),
                    'weight' => $i++,
                ];
                $wpdb->insert($blocks_table, $block_data);
            }
        }

        return get_course_json($course_id);
    }

    public function update_course($request)
    {
        return;
        if (!isset($request['content'])) {
            return new WP_REST_Response('Invalid data given!', 400);
        }

        global $wpdb;

        $id = (int) ($request['id'] ?? -1);
        $content = json_decode($request['content']);

        // Update WingAI_Courses
        $courses_table = $wpdb->prefix . 'WingAI_Courses';
        $course_data = [
            'title' => $content->title,
            'description' => $content->description,
        ];
        $wpdb->update($courses_table, $course_data, ['id' => $id]);

        // Delete WingAI_Course_Stages
        $stages_table = $wpdb->prefix . 'WingAI_Course_Stages';
        $wpdb->delete($stages_table, ['course_id' => $id]);

        // Delete WingAI_Stage_Blocks
        $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';
        $wpdb->delete($blocks_table, ['stage_id' => $id]);

        foreach ($content->stages as $stage) {
            // Create WingAI_Course_Stages
            $stage_data = [
                'course_id' => $id,
                'title' => $stage->title,
            ];
            $wpdb->insert($stages_table, $stage_data);
            $stage_id = (int) $wpdb->insert_id;

            foreach ($stage->blocks as $block) {
                // Create WingAI_Stage_Blocks
                $block_data = [
                    'stage_id' => $stage_id,
                    'block_type' => $block->blockType,
                    'content' => json_encode($block->content),
                ];
                $wpdb->insert($blocks_table, $block_data);
            }
        }

        return get_course_json($id);
    }

    public function delete_course($request)
    {
        return;
        global $wpdb;

        $id = (int) ($request['id'] ?? -1);
        $blocks_table = $wpdb->prefix . 'WingAI_Stage_Blocks';
        $wpdb->delete($blocks_table, ['stage_id' => $id]);
        $stages_table = $wpdb->prefix . 'WingAI_Course_Stages';
        $wpdb->delete($stages_table, ['course_id' => $id]);
        $courses_table = $wpdb->prefix . 'WingAI_Courses';
        $wpdb->delete($courses_table, ['id' => $id]);

        return new WP_REST_Response('Course deleted successfully!', 200);
    }
}
