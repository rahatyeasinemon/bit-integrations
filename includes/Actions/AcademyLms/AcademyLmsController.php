<?php

namespace BitCode\FI\Actions\AcademyLms;

use BitCode\FI\Log\LogHandler;

/**
 * Provide functionality for Academy Lms integration
 */
class AcademyLmsController
{
    private $_integrationID;

    public function __construct($integrationID)
    {
        $this->_integrationID = $integrationID;
    }

    /**
     * Process ajax request for generate_token
     *
     * @return JSON zoho crm api response and status
     */
    public static function Authorization()
    {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (is_plugin_active('academy/academy.php')) {
            wp_send_json_success(true, 200);
        }

        wp_send_json_error(__('Academy Lms must be activated!', 'bit-integrations'));
    }

    public static function getAllLesson()
    {
        if (!class_exists('Academy')) {
            wp_send_json_error(__('Academy Lms is not installed or activated', 'bit-integrations'));
        }

        $lessons = [];

        $lessonList = \Academy\Traits\Lessons::get_lessons();

        foreach ($lessonList as $key => $val) {
            $lessons[] = [
                'lessonId' => $val->ID,
                'lessonTitle' => $val->lesson_title,
            ];
        }
        wp_send_json_success($lessons, 200);
    }

    public static function getAllCourse($queryParams)
    {
        $action = $queryParams->type;
        if (!class_exists('Academy')) {
            wp_send_json_error(__('Academy Lms is not installed or activated', 'bit-integrations'));
        }


        $courseList = get_posts([
            'post_type' => 'academy_courses',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);

        if ($action !== 'complete-course' && $action !== 'reset-course' && $action !== 'complete-lesson') {
            $courses[] = [
                'courseId' => "all-course",
                'courseTitle' => "All Course",
            ];
        }

        foreach ($courseList as $key => $val) {
            $courses[] = [
                'courseId' => $val->ID,
                'courseTitle' => $val->post_title,
            ];
        }
        wp_send_json_success($courses, 200);
    }

    public static function enrollCourse($selectedCourse, $selectedAllCourse, $type)
    {
        $course_ids = [];

        if (count($selectedAllCourse)) {
            foreach ($selectedAllCourse as $course) {
                if ($course->courseId !== 'all-course') {
                    $course_ids[] = $course->courseId;
                }
            }
        } else {
            $course_ids = $selectedCourse;
        }

        $user_id = get_current_user_id();
        if (!count($course_ids)) {
            return;
        }

        if ($type === "enroll") {
            foreach ($course_ids as $course_id) {
                add_filter('is_course_purchasable', '__return_false', 10);
                \Academy\Helper::do_enroll($course_id, $user_id);
                remove_filter('is_course_purchasable', '__return_false', 10);
            }
            return "course enrolled";
        } else {
            foreach ($course_ids as $course_id) {
                \Academy\Helper::cancel_course_enroll($course_id, $user_id);
            }
            return "course unenrolled";
        }
    }

    public static function completeLesson($selectedCourse, $selectedLesson)
    {
        $user_id = get_current_user_id();
        $topic_id = $selectedLesson[0];
        $course_id = $selectedCourse[0];
        $topic_type = "lesson";

        do_action('academy/frontend/before_mark_topic_complete', $topic_type, $course_id, $topic_id, $user_id);

        $option_name = 'academy_course_' . $course_id . '_completed_topics';
        $saved_topics_lists = (array) json_decode(get_user_meta($user_id, $option_name, true), true);

        if (isset($saved_topics_lists[$topic_type][$topic_id])) {
            unset($saved_topics_lists[$topic_type][$topic_id]);
        } else {
            $saved_topics_lists[$topic_type][$topic_id] = \Academy\Helper::get_time();
        }
        $saved_topics_lists = wp_json_encode($saved_topics_lists);
        update_user_meta($user_id, $option_name, $saved_topics_lists);
        do_action('academy/frontend/after_mark_topic_complete', $topic_type, $course_id, $topic_id, $user_id);
        return "Lesson Completed";
    }

    public static function completeCourse($selectedCourse)
    {
        $user_id = get_current_user_id();
        $course_id = $selectedCourse[0];

        global $wpdb;
        do_action('academy/admin/course_complete_before', $course_id);
        $date = gmdate('Y-m-d H:i:s', \Academy\Helper::get_time());

        // hash is unique.
        do {
            $hash    = substr(md5(wp_generate_password(32) . $date . $course_id . $user_id), 0, 16);
            $hasHash = (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(comment_ID) from {$wpdb->comments} 
				WHERE comment_agent = 'academy' AND comment_type = 'course_completed' AND comment_content = %s ",
                    $hash
                )
            );
        } while ($hasHash > 0);

        $data = array(
            'comment_post_ID'  => $course_id,
            'comment_author'   => $user_id,
            'comment_date'     => $date,
            'comment_date_gmt' => get_gmt_from_date($date),
            'comment_content'  => $hash,
            'comment_approved' => 'approved',
            'comment_agent'    => 'academy',
            'comment_type'     => 'course_completed',
            'user_id'          => $user_id,
        );
        $is_complete = $wpdb->insert($wpdb->comments, $data);

        do_action('academy/admin/course_complete_after', $course_id, $user_id);

        if ($is_complete) {
            return 'Course Completed.';
        }
        return;
    }

    public static function resetCourse($selectedCourse)
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $course_id = $selectedCourse[0];

        $completedLessonIds = $wpdb->get_col("select post_id from {$wpdb->postmeta} where meta_key = '_tutor_course_id_for_lesson' AND meta_value = {$course_id} ");

        if (is_array($completedLessonIds) && count($completedLessonIds)) {
            $lessonMetaIds = [];
            foreach ($completedLessonIds as $lesson_id) {
                $lessonMetaIds[] = '_tutor_completed_lesson_id_' . $lesson_id;
            }
            $ids = implode("','", $lessonMetaIds);

            $wpdb->query("DELETE from {$wpdb->usermeta} WHERE user_id = '{$user_id}' AND meta_key in('{$ids}') ");
        }

        $courseContents = tutils()->get_course_contents_by_id($course_id);
        $cntContents = tutils()->count($courseContents);
        if ($cntContents) {
            foreach ($courseContents as $content) {
                if ('tutor_quiz' === $content->post_type) {
                    $quiz_id = $content->ID;
                    $wpdb->query("DELETE FROM {$wpdb->tutor_quiz_attempts} WHERE quiz_id = {$quiz_id} AND user_id = {$user_id} ; ");
                } elseif ('tutor_assignments' === $content->post_type) {
                    $assignment_id = $content->ID;
                    $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_type = 'tutor_assignment' AND user_id = {$user_id} AND comment_post_ID = {$assignment_id} ");
                }
            }
        }

        $wpdb->query(" DELETE from {$wpdb->comments} WHERE comment_agent = 'TutorLMSPlugin' AND comment_type = 'course_completed' AND comment_post_ID = {$course_id} AND user_id = {$user_id} ;");
        return "Course progress reseted";
    }

    public static function completedCourse($course_id, $user_id)
    {

        global $wpdb;
        do_action('tutor_course_complete_before', $course_id);

        $date = date("Y-m-d H:i:s", tutor_time());

        $hash    = substr(md5(wp_generate_password(32) . $date . $course_id . $user_id), 0, 16);
        $has_unique_hash = $wpdb->get_var("SELECT COUNT(comment_ID) from {$wpdb->comments} WHERE comment_agent = 'TutorLMSPlugin' AND comment_type = 'course_completed' AND comment_content = '{$hash}' ");

        while ((int)$has_unique_hash > 0) {
            $hash    = substr(md5(wp_generate_password(32) . $date . $course_id . $user_id), 0, 16);
            $has_unique_hash = $wpdb->get_var("SELECT COUNT(comment_ID) from {$wpdb->comments} WHERE comment_agent = 'TutorLMSPlugin' AND comment_type = 'course_completed' AND comment_content = '{$hash}' ");
        }

        $data = array(
            'comment_post_ID'  => $course_id,
            'comment_author'   => $user_id,
            'comment_date'     => $date,
            'comment_date_gmt' => get_gmt_from_date($date),
            'comment_content'  => $hash,
            'comment_approved' => 'approved',
            'comment_agent'    => 'TutorLMSPlugin',
            'comment_type'     => 'course_completed',
            'user_id'          => $user_id,
        );

        $wpdb->insert($wpdb->comments, $data);

        do_action('tutor_course_complete_after', $course_id);
        return true;
    }

    public function execute($integrationData, $fieldValues)
    {
        $integId = $integrationData->id;
        $actionName = $integrationData->flow_details->actionName;
        $response = [];
        switch ($actionName) {
            case "enroll-course":
            case "unenroll-course":
            case "complete-course":
            case "reset-course":
                $selectedCourse = $integrationData->flow_details->selectedCourse;
                $selectedAllCourse = [];
                if ($actionName !== 'complete-course' && property_exists($integrationData->flow_details, 'selectedAllCourse')) {
                    $selectedAllCourse = $integrationData->flow_details->selectedAllCourse;
                }
                if ($actionName === 'complete-course') {
                    $response = self::completeCourse($selectedCourse);
                } else if ($actionName === 'reset-course') {
                    $response = self::resetCourse($selectedCourse);
                } else if ($actionName === 'enroll-course') {
                    $response = self::enrollCourse($selectedCourse, $selectedAllCourse, "enroll");
                } else {
                    $response = self::enrollCourse($selectedCourse, $selectedAllCourse, "unenroll");
                }
                break;
            case "complete-lesson":
                $selectedCourse = $integrationData->flow_details->selectedCourse;
                $selectedLesson = $integrationData->flow_details->selectedLesson;
                $response = self::completeLesson($selectedCourse, $selectedLesson);
                break;
        }

        if (!isset($response) && $response != []) {
            LogHandler::save($integId, wp_json_encode(['type' => $actionName, 'type_name' => $actionName]), 'error', wp_json_encode($response));
        } else {
            LogHandler::save($integId, wp_json_encode(['type' => $actionName, 'type_name' => $actionName]), 'success', wp_json_encode($response));
        }
    }
}
