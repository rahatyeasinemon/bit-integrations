<?php

namespace BitCode\FI\Core\Util;

use DateTime;
use Groundhogg\DB\Tags;
use BitCode\FI\Flow\Flow;

final class TriggerFallback
{
    public static function handleKadenceFormSubmit($form_args, $fields, $form_id, $post_id)
    {
        if (!$form_id) {
            return;
        }
        $flows = Flow::exists('Kadence', $post_id . '_' . $form_id);
        if (!$flows) {
            return;
        }
        $data = [];
        foreach ($fields as $key => $field) {
            $data['kb_field_' . $key] = $field['value'];
        }

        return ['triggered_entity' => 'Kadence', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }

    // LearnDash

    protected static function flowFilter($flows, $key, $value)
    {
        $filteredFlows = [];
        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
            }
            if (!isset($flow->flow_details->$key) || $flow->flow_details->$key === 'any' || $flow->flow_details->$key == $value || $flow->flow_details->$key === '') {
                $filteredFlows[] = $flow;
            }
        }
        return $filteredFlows;
    }

    public static function getUserInfo($user_id)
    {
        $userInfo = get_userdata($user_id);
        $user = [];
        if ($userInfo) {
            $userData = $userInfo->data;
            $user_meta = get_user_meta($user_id);
            $user = [
                'first_name' => $user_meta['first_name'][0],
                'last_name' => $user_meta['last_name'][0],
                'user_login' => $userData->user_login,
                'user_email' => $userData->user_email,
                'user_url' => $userData->user_url,
                'display_name' => $userData->display_name,
                'nickname' => $userData->user_nicename,
                'user_pass' => $userData->user_pass,
            ];
        }
        return $user;
    }


    public static function learndashHandleCourseEnroll($user_id, $course_id, $access_list, $remove)
    {
        if (!empty($remove)) {
            $flows = Flow::exists('LearnDash', 2);
            $flows = self::flowFilter($flows, 'unenrollCourse', $course_id);
        } else {
            $flows = Flow::exists('LearnDash', 1);
            $flows = self::flowFilter($flows, 'selectedCourse', $course_id);
        }
        if (!$flows) {
            return;
        }

        $course = get_post($course_id);
        $course_url = get_permalink($course_id);
        $result_course = [
            'course_id' => $course->ID,
            'course_title' => $course->post_title,
            'course_url' => $course_url,
        ];
        $user = self::getUserInfo($user_id);

        $result = $result_course + $user;

        return ['triggered_entity' => 'LearnDash', 'triggered_entity_id' => 1, 'data' => $result, 'flows' => $flows];
    }

    public static function learndashHandleLessonCompleted($data)
    {
        $user = $data['user']->data;
        $course = $data['course'];
        $lesson = $data['lesson'];
        if ($course && $user) {
            $course_id = $course->ID;
            $lesson_id = $lesson->ID;
            $user_id = $user->ID;
        }
        $flows = Flow::exists('LearnDash', 4);
        $flows = self::flowFilter($flows, 'selectedLesson', $lesson_id);

        if (!$flows) {
            return;
        }

        $course_url = get_permalink($course_id);
        $result_course = [
            'course_id' => $course->ID,
            'course_title' => $course->post_title,
            'course_url' => $course_url,
        ];

        $lesson_url = get_permalink($lesson_id);
        $result_lesson = [
            'lesson_id' => $lesson->ID,
            'lesson_title' => $lesson->post_title,
            'lesson_url' => $lesson_url,
        ];

        $user = self::getUserInfo($user_id);

        $lessonDataFinal = $result_course + $result_lesson + $user;
        return ['triggered_entity' => 'LearnDash', 'triggered_entity_id' => 4, 'data' => $lessonDataFinal, 'flows' => $flows];
        
    }

    public static function learndashHandleQuizAttempt($data, $user)
    {
        $user = $user->data;
        $course = $data['course'];
        $lesson = $data['lesson'];
        if ($course && $user) {
            $course_id = $course->ID;
            $lesson_id = $lesson->ID;
            $user_id = $user->ID;
            $quiz_id = $data['quiz'];
            $score = $data['score'];
            $pass = $data['pass'];
            $total_points = $data['total_points'];
            $points = $data['points'];
            $percentage = $data['percentage'];
        }
        for ($i = 6; $i < 9; $i++) {
            $flows = Flow::exists('LearnDash', $i);
            $flows = self::flowFilter($flows, 'selectedQuiz', $quiz_id);

            if (!$flows) {
                continue;
            }
            if ($i == 7 && $pass) {
                continue;
            }
            if ($i == 8 && !$pass) {
                continue;
            }
            $course_url = get_permalink($course_id);
            $result_course = [
                'course_id' => $course->ID,
                'course_title' => $course->post_title,
                'course_url' => $course_url,
            ];

            $lesson_url = get_permalink($lesson_id);
            $result_lesson = [
                'lesson_id' => $lesson->ID,
                'lesson_title' => $lesson->post_title,
                'lesson_url' => $lesson_url,
            ];

            $quiz_url = get_permalink($quiz_id);

            $quiz_query_args = [
                'post_type' => 'sfwd-quiz',
                'post_status' => 'publish',
                'orderby' => 'post_title',
                'order' => 'ASC',
                'posts_per_page' => 1,
                'ID' => $quiz_id,
            ];

            $quizList = get_posts($quiz_query_args);

            $result_quiz = [
                'quiz_id' => $quiz_id,
                'quiz_title' => $quizList[0]->post_title,
                'quiz_url' => $quiz_url,
                'score' => $score,
                'pass' => $pass,
                'total_points' => $total_points,
                'points' => $points,
                'percentage' => $percentage,
            ];

            $user = self::getUserInfo($user_id);

            $quizAttemptDataFinal = $result_course + $result_lesson + $result_quiz + $user;
            Flow::execute('LearnDash', $i, $quizAttemptDataFinal, $flows);
            
        }
        return;
    }

    public static function learndashHandleTopicCompleted($data)
    {
        if (empty($data)) {
            return;
        }
        $user = $data['user']->data;
        $course = $data['course'];
        $lesson = $data['lesson'];
        $topic = $data['topic'];
        if ($course && $user && $topic) {
            $course_id = $course->ID;
            $lesson_id = $lesson->ID;
            $user_id = $user->ID;
            $topic_id = $topic->ID;
        }
        $flows = Flow::exists('LearnDash', 5);
        $flows = self::flowFilter($flows, 'selectedTopic', $topic_id);

        if (!$flows) {
            return;
        }

        $course_url = get_permalink($course_id);
        $result_course = [
            'course_id' => $course->ID,
            'course_title' => $course->post_title,
            'course_url' => $course_url,
        ];

        $lesson_url = get_permalink($lesson_id);
        $result_lesson = [
            'lesson_id' => $lesson->ID,
            'lesson_title' => $lesson->post_title,
            'lesson_url' => $lesson_url,
        ];

        $topic_url = get_permalink($topic_id);
        $result_topic = [
            'topic_id' => $topic->ID,
            'topic_title' => $topic->post_title,
            'topic_url' => $topic_url,
        ];

        $user = self::getUserInfo($user_id);

        $topicDataFinal = $result_course + $result_lesson + $result_topic + $user;
        return ['triggered_entity' => 'LearnDash', 'triggered_entity_id' => 5, 'data' => $topicDataFinal, 'flows' => $flows];
    }

    public static function learndashHandleCourseCompleted($data)
    {
        $user = $data['user']->data;
        $course = $data['course'];
        if ($course && $user) {
            $course_id = $course->ID;
            $user_id = $user->ID;
        }
        $flows = Flow::exists('LearnDash', 3);
        $flows = self::flowFilter($flows, 'completeCourse', $course_id);
        if (!$flows) {
            return;
        }

        $course_url = get_permalink($course_id);
        $result_course = [
            'course_id' => $course->ID,
            'course_title' => $course->post_title,
            'course_url' => $course_url,
        ];
        $user = self::getUserInfo($user_id);
        $result = $result_course + $user;
        return ['triggered_entity' => 'LearnDash', 'triggered_entity_id' => 3, 'data' => $result, 'flows' => $flows];
    }

    public static function learndashHandleAddedGroup($user_id, $group_id)
    {
        if (!$group_id || !$user_id) {
            return;
        }
        $flows = Flow::exists('LearnDash', 9);
        $flows = self::flowFilter($flows, 'selectedGroup', $group_id);

        if (!$flows) {
            return;
        }
        $group = get_post($group_id);
        $group_url = get_permalink($group_id);
        $result_group = [
            'group_id' => $group->ID,
            'group_title' => $group->post_title,
            'group_url' => $group_url,
        ];

        $user = self::getUserInfo($user_id);

        $groupDataFinal = $result_group + $user;
        return ['triggered_entity' => 'LearnDash', 'triggered_entity_id' => 9, 'data' => $groupDataFinal, 'flows' => $flows];
    }

    public static function learndashHandleRemovedGroup($user_id, $group_id)
    {
        if (!$group_id || !$user_id) {
            return;
        }
        $flows = Flow::exists('LearnDash', 10);
        $flows = self::flowFilter($flows, 'selectedGroup', $group_id);

        if (!$flows) {
            return;
        }
        $group = get_post($group_id);
        $group_url = get_permalink($group_id);
        $result_group = [
            'group_id' => $group->ID,
            'group_title' => $group->post_title,
            'group_url' => $group_url,
        ];

        $user = self::getUserInfo($user_id);

        $groupDataFinal = $result_group + $user;
        return ['triggered_entity' => 'LearnDash', 'triggered_entity_id' => 10, 'data' => $groupDataFinal, 'flows' => $flows];

    }

    public static function learndashHandleAssignmentSubmit($assignment_post_id, $assignment_meta)
    {
        if (!$assignment_post_id || !$assignment_meta) {
            return;
        }
        $file_name = $assignment_meta['file_name'];
        $file_link = $assignment_meta['file_link'];
        $file_path = $assignment_meta['file_path'];
        $user_id = $assignment_meta['user_id'];
        $lesson_id = $assignment_meta['lesson_id'];
        $course_id = $assignment_meta['course_id'];
        $assignment_id = $assignment_post_id;

        $flows = Flow::exists('LearnDash', 11);
        $flows = self::flowFilter($flows, 'selectedGroup', $lesson_id);

        if (!$flows) {
            return;
        }
        $course = get_post($course_id);
        $course_url = get_permalink($course_id);
        $result_course = [
            'course_id' => $course->ID,
            'course_title' => $course->post_title,
            'course_url' => $course_url,
        ];

        $lesson = get_post($lesson_id);
        $lesson_url = get_permalink($lesson_id);
        $result_lesson = [
            'lesson_id' => $lesson->ID,
            'lesson_title' => $lesson->post_title,
            'lesson_url' => $lesson_url,
        ];

        $result_assignment = [
            'assignment_id' => $assignment_id,
            'file_name' => $file_name,
            'file_link' => $file_link,
            'file_path' => $file_path,
        ];

        $user = self::getUserInfo($user_id);

        $assignmentDataFinal = $result_course + $result_lesson + $result_assignment + $user;
        return ['triggered_entity' => 'LearnDash', 'triggered_entity_id' => 11, 'data' => $assignmentDataFinal, 'flows' => $flows];
    }

    // lifterLms

    public static function lifterLmsGetUserInfo($user_id)
    {
        $userInfo = get_userdata($user_id);
        $user = [];
        if ($userInfo) {
            $userData = $userInfo->data;
            $user_meta = get_user_meta($user_id);
            $user = [
                'first_name' => $user_meta['first_name'][0],
                'last_name' => $user_meta['last_name'][0],
                'user_email' => $userData->user_email,
                'nickname' => $userData->user_nicename,
                'avatar_url' => get_avatar_url($user_id),
            ];
        }
        return $user;
    }

    public static function lifterLmsGetQuizDetail($quizId)
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title FROM $wpdb->posts
                WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'llms_quiz' AND $wpdb->posts.ID = %d",
                $quizId
            )
        );
    }

    public static function lifterLmsHandleAttemptQuiz($user_id, $quiz_id, $quiz_obj)
    {
        $flows = Flow::exists('LifterLms', 1);
        if (!$flows) {
            return;
        }

        $userInfo = self::lifterLmsGetUserInfo($user_id);
        $quizDetail = self::lifterLmsGetQuizDetail($quiz_id);

        $finalData = [
            'user_id' => $user_id,
            'quiz_id' => $quiz_id,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
            'quiz_title' => $quizDetail[0]->post_title,
        ];

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedQuiz = !empty($flowDetails->selectedQuiz) ? $flowDetails->selectedQuiz : [];
        if ($flows && ($quiz_id == $selectedQuiz || $selectedQuiz === 'any')) {
            return ['triggered_entity' => 'LifterLms', 'triggered_entity_id' => 1, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function lifterLmsHandleQuizPass($user_id, $quiz_id, $quiz_obj)
    {
        $flows = Flow::exists('LifterLms', 2);
        if (!$flows) {
            return;
        }

        $userInfo = self::lifterLmsGetUserInfo($user_id);
        $quizDetail = self::lifterLmsGetQuizDetail($quiz_id);

        $finalData = [
            'user_id' => $user_id,
            'quiz_id' => $quiz_id,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
            'quiz_title' => $quizDetail[0]->post_title,
        ];

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedQuiz = !empty($flowDetails->selectedQuiz) ? $flowDetails->selectedQuiz : [];
        if ($flows && ($quiz_id == $selectedQuiz || $selectedQuiz === 'any')) {
            return ['triggered_entity' => 'LifterLms', 'triggered_entity_id' => 2, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function lifterLmsHandleQuizFail($user_id, $quiz_id, $quiz_obj)
    {
        $flows = Flow::exists('LifterLms', 3);
        if (!$flows) {
            return;
        }

        $userInfo = self::lifterLmsGetUserInfo($user_id);
        $quizDetail = self::lifterLmsGetQuizDetail($quiz_id);

        $finalData = [
            'user_id' => $user_id,
            'quiz_id' => $quiz_id,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
            'quiz_title' => $quizDetail[0]->post_title,
        ];

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedQuiz = !empty($flowDetails->selectedQuiz) ? $flowDetails->selectedQuiz : [];
        if ($flows && ($quiz_id == $selectedQuiz || $selectedQuiz === 'any')) {
            return ['triggered_entity' => 'LifterLms', 'triggered_entity_id' => 3, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function lifterLmsGetLessonDetail($lessonId)
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title FROM $wpdb->posts
                WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'lesson' AND $wpdb->posts.ID = %d",
                $lessonId
            )
        );
    }

    public static function lifterLmsHandleLessonComplete($user_id, $lesson_id)
    {
        $flows = Flow::exists('LifterLms', 4);
        if (!$flows) {
            return;
        }

        $userInfo = self::lifterLmsGetUserInfo($user_id);
        $lessonDetail = self::lifterLmsGetLessonDetail($lesson_id);

        $finalData = [
            'user_id' => $user_id,
            'lesson_id' => $lesson_id,
            'lesson_title' => $lessonDetail[0]->post_title,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];

        return ['triggered_entity' => 'LifterLms', 'triggered_entity_id' => 4, 'data' => $finalData, 'flows' => $flows];
    }

    public static function lifterLmsGetCourseDetail($courseId)
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title FROM $wpdb->posts
                WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'course' AND $wpdb->posts.ID = %d",
                $courseId
            )
        );
    }

    public static function lifterLmsHandleCourseComplete($user_id, $course_id)
    {
        $flows = Flow::exists('LifterLms', 5);
        if (!$flows) {
            return;
        }

        $userInfo = self::lifterLmsGetUserInfo($user_id);
        $courseDetail = self::lifterLmsGetCourseDetail($course_id);

        $finalData = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'course_title' => $courseDetail[0]->post_title,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];
        return ['triggered_entity' => 'LifterLms', 'triggered_entity_id' => 5, 'data' => $finalData, 'flows' => $flows];
    }

    public static function lifterLmsHandleCourseEnroll($user_id, $product_id)
    {
        $flows = Flow::exists('LifterLms', 6);
        if (!$flows) {
            return;
        }

        $userInfo = self::lifterLmsGetUserInfo($user_id);
        $courseDetail = self::lifterLmsGetCourseDetail($product_id);

        $finalData = [
            'user_id' => $user_id,
            'course_id' => $product_id,
            'course_title' => $courseDetail[0]->post_title,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];
        return ['triggered_entity' => 'LifterLms', 'triggered_entity_id' => 6, 'data' => $finalData, 'flows' => $flows];
    }

    public static function lifterLmsHandleCourseUnEnroll($student_id, $course_id, $a, $status)
    {
        $flows = Flow::exists('LifterLms', 7);

        if (!$flows || empty($course_id) || $status != 'cancelled') {
            return;
        }

        $userInfo = self::lifterLmsGetUserInfo($student_id);
        $courseDetail = self::lifterLmsGetCourseDetail($course_id);

        $finalData = [
            'user_id' => $student_id,
            'course_id' => $course_id,
            'course_title' => $courseDetail[0]->post_title,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];
        return ['triggered_entity' => 'LifterLms', 'triggered_entity_id' => 7, 'data' => $finalData, 'flows' => $flows];
    }

    public static function lifterLmsGetMembershipDetail($membershipId)
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title FROM $wpdb->posts
        WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'llms_membership' AND $wpdb->posts.ID = %d",
                $membershipId
            )
        );
    }

    public static function lifterLmsHandleMembershipCancel($data, $user_id, $a, $b)
    {
        $flows = Flow::exists('LifterLms', 8);
        $product_id = $data->get('product_id');

        if (!$flows || !$user_id || !$product_id) {
            return;
        }

        $userInfo = self::lifterLmsGetUserInfo($user_id);
        $membershipDetail = self::lifterLmsGetMembershipDetail($product_id);

        $finalData = [
            'user_id' => $user_id,
            'membership_title' => $product_id,
            'membership_id' => $membershipDetail[0]->post_title,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];
        return ['triggered_entity' => 'LifterLms', 'triggered_entity_id' => 8, 'data' => $finalData, 'flows' => $flows];
    }

    // mail poet

    public static function mailPoetHandleDateField($item)
    {
        if (
            array_key_exists('year', $item)
            && array_key_exists('month', $item)
            && array_key_exists('day', $item)
            && (!empty($item['year']) || !empty($item['month']) || !empty($item['day']))
        ) {
            $year  = (int) !empty($item['year']) ? $item['year'] : date('Y');
            $month = (int) !empty($item['month']) ? $item['month'] : 1;
            $day   = (int) !empty($item['day']) ? $item['day'] : 1;
        } elseif (
            array_key_exists('year', $item)
            && array_key_exists('month', $item)
            && (!empty($item['year']) || !empty($item['month']))
        ) {
            $year  = (int) !empty($item['year']) ? $item['year'] : date('Y');
            $month = (int) !empty($item['month']) ? $item['month'] : 1;
            $day   = 1;
        } elseif (array_key_exists('year', $item) && !empty($item['year'])) {
            $year  = $item['year'];
            $month = 1;
            $day   = 1;
        } elseif (array_key_exists('month', $item) && !empty($item['month'])) {
            $year  = date('Y');
            $month = $item['month'];
            $day   = 1;
        }

        if (isset($year, $month, $day)) {
            $date = new DateTime();
            $date->setDate($year, $month, $day);
            return $date->format('Y-m-d');
        }

        return null;
    }

    public static function handleMailpoetSubmit($data, $segmentIds, $form)
    {
        $formData = [];

        foreach ($data as $key => $item) {
            $keySeparated = explode('_', $key);

            if ($keySeparated[0] === 'cf') {
                if (is_array($item)) {
                    $formData[$keySeparated[1]] = self::mailPoetHandleDateField($item);
                } else {
                    $formData[$keySeparated[1]] = $item;
                }
            } else {
                if (is_array($item)) {
                    $formData[$key] = self::mailPoetHandleDateField($item);
                } else {
                    $formData[$key] = $item;
                }
            }
        }

        $form_id = $form->getId();

        if (!empty($form_id) && $flows = Flow::exists('MailPoet', $form_id)) {
            return ['triggered_entity' => 'MailPoet', 'triggered_entity_id' => $form_id, 'data' => $formData, 'flows' => $flows];
        }
    }

    // masterStudy Lms

    public static function masterStudyLmsGetUserInfo($user_id)
    {
        $userInfo = get_userdata($user_id);
        $user = [];
        if ($userInfo) {
            $userData = $userInfo->data;
            $user_meta = get_user_meta($user_id);
            $user = [
                'first_name' => $user_meta['first_name'][0],
                'last_name' => $user_meta['last_name'][0],
                'user_email' => $userData->user_email,
                'nickname' => $userData->user_nicename,
                'avatar_url' => get_avatar_url($user_id),
            ];
        }
        return $user;
    }

    public static function masterStudyGetCourseDetail($courseId)
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title,post_content FROM $wpdb->posts
                WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'stm-courses' AND $wpdb->posts.ID = %d",
                $courseId
            )
        );
    }

    public static function stmLmsHandleCourseComplete($course_id, $user_id, $progress)
    {
        $flows = Flow::exists('MasterStudyLms', 1);
        if (!$flows) {
            return;
        }

        $userInfo = self::masterStudyLmsGetUserInfo($user_id);
        $courseDetails = self::masterStudyGetCourseDetail($course_id);

        $finalData = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'course_title' => $courseDetails[0]->post_title,
            'course_description' => $courseDetails[0]->post_content,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedCourse = !empty($flowDetails->selectedCourse) ? $flowDetails->selectedCourse : [];
        if ($flows && ($course_id == $selectedCourse || $selectedCourse === 'any')) {
            return ['triggered_entity' => 'MasterStudyLms', 'triggered_entity_id' => 1, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function stmLmsHandleCourseEnroll($user_id, $course_id)
    {
        $flows = Flow::exists('MasterStudyLms', 3);
        if (!$flows) {
            return;
        }

        $userInfo = self::masterStudyLmsGetUserInfo($user_id);
        $courseDetails = self::masterStudyGetCourseDetail($course_id);

        $finalData = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'course_title' => $courseDetails[0]->post_title,
            'course_description' => $courseDetails[0]->post_content,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedCourse = !empty($flowDetails->selectedCourse) ? $flowDetails->selectedCourse : [];
        if ($flows && ($course_id == $selectedCourse || $selectedCourse === 'any')) {
            return ['triggered_entity' => 'MasterStudyLms', 'triggered_entity_id' => 3, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function masterStudyGetLessonDetail($lessonId)
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title,post_content FROM $wpdb->posts
        WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'stm-lessons' AND $wpdb->posts.ID = %d",
                $lessonId
            )
        );
    }

    public static function stmLmsHandleLessonComplete($user_id, $lesson_id)
    {
        $flows = Flow::exists('MasterStudyLms', 2);
        if (!$flows) {
            return;
        }

        $userInfo = self::masterStudyLmsGetUserInfo($user_id);
        $lessonDetails = self::masterStudyGetLessonDetail($lesson_id);

        $finalData = [
            'user_id' => $user_id,
            'lesson_id' => $lesson_id,
            'lesson_title' => $lessonDetails[0]->post_title,
            'lesson_description' => $lessonDetails[0]->post_content,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedLesson = !empty($flowDetails->selectedLesson) ? $flowDetails->selectedLesson : [];
        if ($flows && ($lesson_id == $selectedLesson || $selectedLesson === 'any')) {
            return ['triggered_entity' => 'MasterStudyLms', 'triggered_entity_id' => 2, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function masterStudyGetQuizDetails($quiz_id)
    {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title,post_content FROM $wpdb->posts
                 WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'stm-quizzes' AND $wpdb->posts.ID = %d",
                $quiz_id
            )
        );
    }

    public static function stmLmsHandleQuizComplete($user_id, $quiz_id, $user_quiz_progress)
    {
        $flows = Flow::exists('MasterStudyLms', 4);
        if (!$flows) {
            return;
        }

        $userInfo = self::masterStudyLmsGetUserInfo($user_id);
        $quizDetails = self::masterStudyGetQuizDetails($quiz_id);

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedCourse = !empty($flowDetails->selectedCourse) ? $flowDetails->selectedCourse : [];
        $courseDetails = self::masterStudyGetCourseDetail($selectedCourse);

        $finalData = [
            'user_id' => $user_id,
            'course_id' => $selectedCourse,
            'course_title' => $courseDetails[0]->post_title,
            'course_description' => $courseDetails[0]->post_content,
            'quiz_id' => $quiz_id,
            'quiz_title' => $quizDetails[0]->post_title,
            'quiz_description' => $quizDetails[0]->post_content,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];

        $selectedQuiz = !empty($flowDetails->selectedQuiz) ? $flowDetails->selectedQuiz : [];

        if (($quiz_id == $selectedQuiz || $selectedQuiz === 'any')) {
            return ['triggered_entity' => 'MasterStudyLms', 'triggered_entity_id' => 4, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function stmLmsHandleQuizFailed($user_id, $quiz_id, $user_quiz_progress)
    {
        $flows = Flow::exists('MasterStudyLms', 5);
        if (!$flows) {
            return;
        }

        $userInfo = self::masterStudyLmsGetUserInfo($user_id);
        $quizDetails = self::masterStudyGetQuizDetails($quiz_id);

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedCourse = !empty($flowDetails->selectedCourse) ? $flowDetails->selectedCourse : [];
        $courseDetails = self::masterStudyGetCourseDetail($selectedCourse);

        $finalData = [
            'user_id' => $user_id,
            'course_id' => $selectedCourse,
            'course_title' => $courseDetails[0]->post_title,
            'course_description' => $courseDetails[0]->post_content,
            'quiz_id' => $quiz_id,
            'quiz_title' => $quizDetails[0]->post_title,
            'quiz_description' => $quizDetails[0]->post_content,
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];

        $selectedQuiz = !empty($flowDetails->selectedQuiz) ? $flowDetails->selectedQuiz : [];

        if (($quiz_id == $selectedQuiz || $selectedQuiz === 'any')) {
            return ['triggered_entity' => 'MasterStudyLms', 'triggered_entity_id' => 5, 'data' => $finalData, 'flows' => $flows];
        }
    }

    // Memberpress

    public static function MemberpressGetUserInfo($user_id)
    {
        $userInfo = get_userdata($user_id);
        $user = [];
        if ($userInfo) {
            $userData = $userInfo->data;
            $user_meta = get_user_meta($user_id);
            $user = [
                'user_id' => $user_id,
                'first_name' => $user_meta['first_name'][0],
                'last_name' => $user_meta['last_name'][0],
                'user_email' => $userData->user_email,
                'nickname' => $userData->user_nicename,
                'avatar_url' => get_avatar_url($user_id),
            ];
        }
        return $user;
    }

    public static function meprOneTimeMembershipSubscribe(\MeprEvent $event)
    {
        $transaction = $event->get_data();
        $product = $transaction->product();
        $product_id = $product->ID;
        $user_id = absint($transaction->user()->ID);
        if ('lifetime' !== (string) $product->period_type) {
            return;
        }

        $postData = get_post($product_id);
        $userData = self::MemberpressGetUserInfo($user_id);
        $finalData = array_merge((array)$postData, $userData);

        if ($user_id && $flows = Flow::exists('Memberpress', 1)) {
            return ['triggered_entity' => 'Memberpress', 'triggered_entity_id' => 1, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function meprRecurringMembershipSubscribe(\MeprEvent $event)
    {
        $transaction = $event->get_data();
        $product = $transaction->product();
        $product_id = $product->ID;
        $user_id = absint($transaction->user()->ID);
        if ('lifetime' === (string) $product->period_type) {
            return;
        }

        $postData = get_post($product_id);
        $userData = self::MemberpressGetUserInfo($user_id);
        $finalData = array_merge((array)$postData, $userData);

        if ($user_id && $flows = Flow::exists('Memberpress', 2)) {
            return ['triggered_entity' => 'Memberpress', 'triggered_entity_id' => 2, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function meprMembershipSubscribeCancel($old_status, $new_status, $subscription)
    {
        $old_status = (string) $old_status;
        $new_status = (string) $new_status;

        if ($old_status === $new_status && $new_status !== 'cancelled') {
            return;
        }

        $product_id = $subscription->rec->product_id;
        $user_id = intval($subscription->rec->user_id);
        $userData = self::MemberpressGetUserInfo($user_id);
        $finalData = array_merge((array)$subscription->rec, $userData);

        $flows = Flow::exists('Memberpress', 3);
        if (!$flows) {
            return;
        }

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedCancelMembership = !empty($flowDetails->selectedCancelMembership) ? $flowDetails->selectedCancelMembership : [];

        if ($product_id === $selectedCancelMembership || $selectedCancelMembership === 'any') {
            return ['triggered_entity' => 'Memberpress', 'triggered_entity_id' => 3, 'data' => $finalData, 'flows' => $flows];
            
        }
    }

    public static function meprMembershipSubscribeExpire($old_status, $new_status, $subscription)
    {
        $old_status = (string) $old_status;
        $new_statuss = (string) $new_status;

        if ($new_statuss !== 'suspended') {
            return;
        }
        $product_id = $subscription->rec->product_id;
        $user_id = intval($subscription->rec->user_id);
        $userData = self::MemberpressGetUserInfo($user_id);
        $finalData = array_merge((array)$subscription->rec, $userData);

        $flows = Flow::exists('Memberpress', 5);
        if (!$flows) {
            return;
        }

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedRecurringMembership = !empty($flowDetails->selectedRecurringMembership) ? $flowDetails->selectedRecurringMembership : [];

        if ($product_id === $selectedRecurringMembership || $selectedRecurringMembership === 'any') {
            return ['triggered_entity' => 'Memberpress', 'triggered_entity_id' => 5, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function meprMembershipSubscribePaused(\MeprEvent $event)
    {
        $transaction = $event->get_data();
        $product = $transaction->product();
        $product_id = $product->ID;
        $user_id = absint($transaction->user()->ID);

        $postData = get_post($product_id);
        $userData = self::MemberpressGetUserInfo($user_id);
        $finalData = array_merge((array)$postData, $userData);

        $flows = Flow::exists('Memberpress', 4);
        if (!$flows) {
            return;
        }

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedCancelMembership = !empty($flowDetails->selectedCancelMembership) ? $flowDetails->selectedCancelMembership : [];

        if ($product_id === $selectedCancelMembership || $selectedCancelMembership === 'any') {
            return ['triggered_entity' => 'Memberpress', 'triggered_entity_id' => 4, 'data' => $finalData, 'flows' => $flows];
        }
    }

    // metform

    public static function handleMetformProSubmit($form_setting, $form_data, $email_name)
    {
        self::handle_submit_data($form_data['id'], $form_data);
    }

    public static function handleMetformSubmit($form_id, $form_data, $form_settings)
    {
        self::handle_submit_data($form_id, $form_data);
    }

    private static function handle_submit_data($form_id, $form_data)
    {
        if (!$form_id) {
            return;
        }
        $flows = Flow::exists('Met', $form_id);
        if (!$flows) {
            return;
        }

        unset($form_data['action'], $form_data['form_nonce'], $form_data['id']);
        $data = $form_data;
        return ['triggered_entity' => 'Met', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }

    public static function metaBoxFields($form_id)
    {
        if (function_exists('rwmb_meta')) {
            $meta_box_registry = rwmb_get_registry('meta_box');
            $fileUploadTypes = ['file_upload', 'single_image', 'file'];
            $form = $meta_box_registry->get($form_id);
            $fieldDetails = $form->meta_box['fields'];
            $fields = [];
            foreach ($fieldDetails as $field) {
                if (!empty($field['id']) && $field['type'] !== 'submit') {
                    $fields[] = [
                        'name' => $field['id'],
                        'type' => in_array($field['type'], $fileUploadTypes) ? 'file' : $field['type'],
                        'label' => $field['name'],
                    ];
                }
            }
            return $fields;
        }
    }

    public static function handleMetaboxSubmit($object)
    {
        $formId = $object->config['id'];
        $fields = self::metaBoxFields($formId);
        $postId = $object->post_id;
        $metaBoxFieldValues = [];

        foreach ($fields as $index => $field) {
            $fieldValues = rwmb_meta($field['name'], $args = [], $postId);
            if (isset($fieldValues)) {
                if ($field['type'] !== 'file') {
                    $metaBoxFieldValues[$field['name']] = $fieldValues;
                } elseif ($field['type'] === 'file') {
                    if (isset($fieldValues['path'])) {
                        $metaBoxFieldValues[$field['name']] = $fieldValues['path'];
                    } elseif (gettype($fieldValues) === 'array') {
                        foreach (array_values($fieldValues) as $index => $file) {
                            if (isset($file['path'])) {
                                $metaBoxFieldValues[$field['name']][$index] = $file['path'];
                            }
                        }
                    }
                }
            }
        }

        $postFieldValues = (array) get_post($object->post_id);

        $data = array_merge($postFieldValues, $metaBoxFieldValues);

        if (!empty($formId) && $flows = Flow::exists('MetaBox', $formId)) {
            return ['triggered_entity' => 'MetaBox', 'triggered_entity_id' => $formId, 'data' => $data, 'flows' => $flows];

        }
    }
}
