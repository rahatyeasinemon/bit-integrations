<?php

namespace BitCode\FI\Actions\MasterStudyLms;

use WP_Error;
use BitCode\FI\Log\LogHandler;
use BitCode\FI\Core\Util\Common;

class MasterStudyLmsHelper
{
    public static function getLessonByCourse($courseId)
    {
        global $wpdb;

        $lesson = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title,post_content
                FROM %s
                WHERE FIND_IN_SET(
                    ID,
                    (SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = 'curriculum')
                )
                AND post_type = 'stm-lessons'
                ORDER BY post_title ASC
                ",
                $wpdb->posts,
                absint($courseId)
            )
        );
        return $lesson;
    }

    public static function getQuizByCourse($courseId)
    {
        global $wpdb;
        $quizzes = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title,post_content
                FROM %s
                WHERE FIND_IN_SET(
                    ID,
                    (SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = 'curriculum')
                )
                AND post_type = 'stm-quizzes'
                ORDER BY post_title ASC
                ",
                $wpdb->posts,
                absint($courseId)
            )
        );
        return $quizzes;
    }
}
