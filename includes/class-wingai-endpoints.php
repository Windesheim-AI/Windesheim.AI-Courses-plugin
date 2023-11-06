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
        ]);
        register_rest_route('wingai/v1', '/courses/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_course'],
        ]);
        register_rest_route('wingai/v1', '/courses', [
            'methods' => 'POST',
            'callback' => [$this, 'create_course'],
        ]);
        register_rest_route('wingai/v1', '/courses/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_course'],
        ]);
        register_rest_route('wingai/v1', '/courses/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$this, 'delete_course'],
        ]);
    }

    public function get_courses()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wingai_course';
        $courses = $wpdb->get_results("SELECT * FROM $table_name");
        return $courses;
    }

    public function get_course($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wingai_course';
        $id = $request['id'];
        $course = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");
        return $course;
    }

    public function create_course($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wingai_course';
        $data = [
            'content' => $request['content'],
        ];
        $wpdb->insert($table_name, $data);
        $id = $wpdb->insert_id;
        $course = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");
        return $course;
    }

    public function update_course($request)
    {
        $wachtwoord = ($request['wachtwoord'] ?? null);
        if ($wachtwoord !== "Hollo123!@#") return;
        global $wpdb;
        $table_name = $wpdb->prefix . 'wingai_course';
        $id = $request['id'];
        $data = [
            'content' => $request['content'],
        ];
        $wpdb->update($table_name, $data, ['id' => $id]);
        $course = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");
        return $course;
    }

    public function delete_course($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wingai_course';
        $id = $request['id'];
        $wpdb->delete($table_name, ['id' => $id]);
        return new WP_REST_Response(null, 204);
    }

}
