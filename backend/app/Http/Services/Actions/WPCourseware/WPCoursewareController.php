<?php

namespace BitApps\BTCBI\Http\Services\Actions\WPCourseware;

use BitApps\BTCBI\Util\Helper;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BTCBI\Deps\BitApps\WPKit\Http\Response;
use WP_Error;

class WPCoursewareController
{
    private $integrationID;
    protected static $actions = [
        "enroll" => [
            "id" => "enroll",
            "title" => "Enroll user in a Course"
        ],
        "unroll" => [
            "id" => "unroll",
            "title" => "Unroll user in a Course"
        ],
    ];

    public function __construct($integrationID)
    {
        $this->integrationID = $integrationID;
    }

    public static function wpCoursewareAuthorize()
    {
        if (!is_plugin_active('wp-courseware/wp-courseware.php')) {
            Response::error(__('WP Courseware Plugin is not active or installed', 'bit-integrations'), 400);
        } else {
            Response::success(true);
        }
    }

    public static function WPCWActions()
    {
        if (!is_plugin_active('wp-courseware/wp-courseware.php')) {
            Response::error(__('WP Courseware Plugin is not active or installed', 'bit-integrations'), 400);
        }

        $actions = [];
        foreach (self::$actions as $action) {
            $actions[] = (object) [
                'id'    => $action['id'],
                'title' => $action['title']
            ];
        }

        $response['WPCWActions'] = $actions;
        Response::success($response);
    }

    public static function WPCWCourses()
    {
        if (!is_plugin_active('wp-courseware/wp-courseware.php')) {
            Response::error(__('WP Courseware Plugin is not active or installed', 'bit-integrations'), 400);
        }

        $wpcwCourses = function_exists('wpcw_get_courses') ? wpcw_get_courses() : [];

        $courses = [(object) [
            'id'    => 'select_all_course',
            'title' => 'All Courses'
        ]];

        foreach ($wpcwCourses as $course) {
            $courses[] = (object) [
                'id'    => $course->course_id,
                'title' => $course->course_title
            ];
        }

        $response['WPCWCourses'] = $courses;
        Response::success($response);
    }

    public function execute($integrationData, $fieldValues)
    {
        if (!is_plugin_active('wp-courseware/wp-courseware.php')) {
            LogHandler::save($this->integrationID, ['type' => 'record', 'type_name' => 'insert'], 'error', 'WP Courseware Plugins not found');
            return false;
        }

        $userId = get_current_user_id();
        $integrationDetails = $integrationData->flow_details;
        $action = $integrationDetails->action;
        $course = $integrationDetails->course;
        $allCourse = isset($integrationDetails->selectedAllCourse) ? $integrationDetails->selectedAllCourse : [];

        if (empty($action) || empty($course)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('Action, Course are required for WP Courseware api', 'bit-integrations'));
        }

        $recordApiHelper = new RecordApiHelper($this->integrationID);
        return $recordApiHelper->execute($action, $course, $userId, $allCourse);
    }
}
