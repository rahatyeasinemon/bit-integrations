<?php

namespace BitCode\FI\Core\Util;

use DateTime;
use Groundhogg\DB\Tags;
use BitCode\FI\Flow\Flow;

final class TriggerFallback
{
    public static function handleFormidableSubmit($conf_method, $form, $form_option, $entry_id, $extra_args)
    {
        $form_id = $form->id;
        if (empty($form_id)) {
            return;
        }

        $flows = Flow::exists('Formidable', $form_id);
        if (empty($flows)) {
            return;
        }

        $file = self::getFormidableFields(($form_id));
        $fileFlds = [];
        foreach ($file as $fldKey => $fldVal) {
            if ($fldVal->type == 'file') {
                $fileFlds[] = $fldVal->name;
            }
        }

        $form_data = self::getFormidableFieldsValues($form, $entry_id);
        $post_id = url_to_postid($_SERVER['HTTP_REFERER']);

        if (!empty($form->id)) {
            $data = [];
            if ($post_id) {
                $form_data['post_id'] = $post_id;
            }

            foreach ($form_data as $key => $val) {
                if (in_array($key, $fileFlds)) {
                    if (is_array($val)) {
                        foreach ($val as $fileKey => $file) {
                            $tmpData = wp_get_attachment_url($form_data[$key][$fileKey]);
                            $form_data[$key][$fileKey] = Common::filePath($tmpData);
                        }
                    } else {
                        $tmpData = wp_get_attachment_url($form_data[$key]);
                        $form_data[$key] = Common::filePath($tmpData);
                    }
                }
            }
        }

        return ['triggered_entity' => 'Formidable', 'triggered_entity_id' => $form_id, 'data' => $form_data, 'flows' => $flows];
    }

    public static function getFormidableFields($form_id)
    {
        $fields = \FrmField::get_all_for_form($form_id, '', 'include');
        $field = [];
        if (empty($fields)) {
            wp_send_json_error(__('Form doesn\'t exists any field', 'bit-integrations'));
        }

        $visistedKey = [];

        foreach ($fields as $key => $val) {
            if ($val->type === 'name') {
                $field[] = (object) [
                    'name'     => 'first-name',
                    'label'    => 'First Name',
                    'type'     => 'name'
                ];
                $field[] = (object) [
                    'name'     => 'middle-name',
                    'label'    => 'Middle Name',
                    'type'     => 'name'
                ];
                $field[] = (object) [
                    'name'     => 'last-name',
                    'label'    => 'Last Name',
                    'type'     => 'name'
                ];
                continue;
            } elseif ($val->type === 'address') {
                $allFld = $val->default_value;
                $addressKey = $val->field_key;
                foreach ($allFld as $key => $val) {
                    $field[] = (object) [
                        'name'     => $addressKey . '_' . $key,
                        'label'    => 'address_' . $key,
                        'type'     => 'address'
                    ];
                }
                continue;
            } elseif ($val->type === 'divider' || $val->type === 'end_divider') {
                $formName = $val->name;
                $fldKey = $val->field_key;
                $cnt = 0;
                for ($i = $key + 1; $i < count($fields); $i++) {
                    $id = $fields[$i]->id;
                    if (isset($fields[$i]->form_name) && $fields[$i]->form_name === $formName) {
                        $field[] = (object) [
                            'name'     => $fldKey . '_' . $id,
                            'label'    => $formName . ' ' . $fields[$i]->name,
                            'type'     => $fields[$i]->type
                        ];
                    }
                    $cnt++;
                    array_push($visistedKey, $fields[$i]->field_key);
                }
                continue;
            }
            if (in_array($val->field_key, $visistedKey)) {
                // continue;
            }
            $field[] = (object) [
                'name'     => $val->field_key,
                'label'    => $val->name,
                'type'     => $val->type
            ];
        }

        return $field;
    }

    public static function getFormidableFieldsValues($form, $entry_id)
    {
        $form_fields = [];
        $fields = \FrmFieldsHelper::get_form_fields($form->id);
        $entry_values = new \FrmEntryValues($entry_id);
        $field_values = $entry_values->get_field_values();

        foreach ($fields as $field) {
            $key = $field->field_key;

            $val = (isset($field_values[$field->id]) ? $field_values[$field->id]->get_saved_value() : '');

            if (is_array($val)) {
                if ($field->type === 'name') {
                    if (array_key_exists('first', $val) || array_key_exists('middle', $val) || array_key_exists('last', $val)) {
                        $form_fields['first-name'] = isset($val['first']) ? $val['first'] : '';
                        $form_fields['middle-name'] = isset($val['middle']) ? $val['middle'] : '';
                        $form_fields['last-name'] = isset($val['last']) ? $val['last'] : '';
                    }
                } elseif ($field->type == 'checkbox' || $field->type == 'file') {
                    $form_fields[$key] = $field->type == 'checkbox' && is_array($val) && count($val) == 1 ? $val[0] : $val;
                } elseif ($field->type == 'address') {
                    $addressKey = $field->field_key;
                    foreach ($val as $k => $value) {
                        $form_fields[$addressKey . '_' . $k] = $value;
                    }
                } elseif ($field->type == 'divider') {
                    $repeaterFld = $field->field_key;
                    global $wpdb;

                    $allDividerFlds = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}frm_item_metas WHERE item_id IN (SELECT id FROM {$wpdb->prefix}frm_items WHERE parent_item_id = $entry_id)");
                    $allItemId = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}frm_items WHERE parent_item_id = $entry_id");

                    $repeater = [];
                    foreach ($allItemId as $k => $value) {
                        $itemId = $value->id;
                        foreach ($allDividerFlds as $kTmp => $valueTmp) {
                            $fldId = $valueTmp->field_id;
                            if ($valueTmp->item_id == $itemId) {
                                $form_fields[$repeaterFld . '_' . $fldId . '_' . $itemId] = $valueTmp->meta_value;
                                $repeater[$itemId][] = (object) [
                                    $fldId => $valueTmp->meta_value
                                ];
                            }
                        }
                    }
                    $form_fields[$repeaterFld] = $repeater;
                }
                continue;
            }

            $form_fields[$key] = $val;
        }

        return $form_fields;
    }

    protected static function academyLmsFlowFilter($flows, $key, $value)
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

    public static function academyHandleCourseEnroll($course_id, $enrollment_id)
    {
        $flows = Flow::exists('AcademyLms', 1);
        $flows = self::academyLmsFlowFilter($flows, 'selectedCourse', $course_id);
        if (!$flows) {
            return;
        }

        $author_id = get_post_field('post_author', $course_id);
        $author_name = get_the_author_meta('display_name', $author_id);

        $student_id = get_post_field('post_author', $enrollment_id);
        $student_name = get_the_author_meta('display_name', $student_id);
        $result_student = [];
        if ($student_id && $student_name) {
            $result_student = [
                'student_id' => $student_id,
                'student_name' => $student_name,
            ];
        }

        $result_course = [];
        $course = get_post($course_id);
        $result_course = [
            'course_id' => $course->ID,
            'course_title' => $course->post_title,
            'course_author' => $author_name,
        ];
        $result = $result_student + $result_course;

        $courseInfo = get_post_meta($course_id);
        $course_temp = [];
        foreach ($courseInfo as $key => $val) {
            if (is_array($val)) {
                $val = maybe_unserialize($val[0]);
            }
            $course_temp[$key] = $val;
        }

        $result = $result + $course_temp;
        $result['post_id'] = $enrollment_id;

        return ['triggered_entity' => 'AcademyLms', 'triggered_entity_id' => 1, 'data' => $result, 'flows' => $flows];
    }

    public static function academyHandleQuizAttempt($attempt)
    {
        $flows = Flow::exists('AcademyLms', 2);
        $quiz_id = $attempt->quiz_id;

        $flows = $flows ? self::academyLmsFlowFilter($flows, 'selectedQuiz', $quiz_id) : false;
        if (!$flows || empty($flow)) {
            return;
        }

        if ('academy_quiz' !== get_post_type($quiz_id)) {
            return;
        }

        if ('pending' === $attempt->attempt_status) {
            return;
        }

        $attempt_details = [];
        foreach ($attempt as $key => $val) {
            if (is_array($val)) {
                $val = maybe_unserialize($val[0]);
            }
            $attempt_details[$key] = maybe_unserialize($val);
        }

        return ['triggered_entity' => 'AcademyLms', 'triggered_entity_id' => 2, 'data' => $attempt_details, 'flows' => $flows];
    }

    public static function academyHandleQuizTarget($attempt)
    {
        $flows = Flow::exists('AcademyLms', 5);
        $quiz_id = $attempt->quiz_id;

        $flows = $flows ? self::academyLmsFlowFilter($flows, 'selectedQuiz', $quiz_id) : false;
        if (!$flows) {
            return;
        }

        if ('academy_quiz' !== get_post_type($quiz_id)) {
            return;
        }

        if ('pending' === $attempt->attempt_status) {
            return;
        }

        $attempt_details = [];
        foreach ($attempt as $key => $val) {
            if (is_array($val)) {
                $val = maybe_unserialize($val[0]);
            }
            $attempt_details[$key] = maybe_unserialize($val);
        }
        foreach ($flows as $flow) {
            $flow_details = $flow->flow_details;
            $reqPercent = $flow_details->requiredPercent;
            $mark = $attempt_details['total_marks'] * ($reqPercent / 100);
            $condition = $flow_details->selectedCondition;
            $achived = self::academyLmsCheckedAchived($condition, $mark, $attempt_details['earned_marks']);
            $attempt_details['achived_status'] = $achived;
        }
        return ['triggered_entity' => 'AcademyLms', 'triggered_entity_id' => 5, 'data' => $attempt_details, 'flows' => $flows];
    }

    public static function academyLmsCheckedAchived($condition, $mark, $earnMark)
    {
        $res = 'Not Achived';

        if ($condition === 'equal_to') {
            if ($earnMark == $mark) {
                $res = 'Achived';
            }
        } elseif ($condition === 'not_equal_to') {
            if ($earnMark != $mark) {
                $res = 'Achived';
            }
        } elseif ($condition === 'less_than') {
            if ($earnMark < $mark) {
                $res = 'Achived';
            }
        } elseif ($condition === 'greater_than') {
            if ($earnMark > $mark) {
                $res = 'Achived';
            }
        } elseif ($condition === 'greater_than_equal') {
            if ($earnMark >= $mark) {
                $res = 'Achived';
            }
        } elseif ($condition === 'less_than_equal') {
            if ($earnMark <= $mark) {
                $res = 'Achived';
            }
        }
        return $res;
    }

    public static function academyHandleLessonComplete($topic_type, $course_id, $topic_id, $user_id)
    {
        $flows = Flow::exists('AcademyLms', 3);
        $flows = $flows ? self::academyLmsFlowFilter($flows, 'selectedLesson', $topic_id) : false;
        if (!$flows) {
            return;
        }

        $topicData = [];
        if ($topic_type === 'lesson') {
            $lessonPost = \Academy\Traits\Lessons::get_lesson($topic_id);
            $topicData = [
                'lesson_id' => $lessonPost->ID,
                'lesson_title' => $lessonPost->lesson_title,
                'lesson_description' => $lessonPost->lesson_content,
                'lesson_status' => $lessonPost->lesson_status,
            ];
        }

        if ($topic_type === 'quiz') {
            $quiz = get_post($topic_id);
            $topicData = [
                'quiz_id' => $quiz->ID,
                'quiz_title' => $quiz->post_title,
                'quiz_description' => $quiz->post_content,
                'quiz_url' => $quiz->guid,
            ];
        }

        $user = self::academyLmsGetUserInfo($user_id);
        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $courseData = [];
        $coursePost = get_post($course_id);
        $courseData = [
            'course_id' => $coursePost->ID,
            'course_title' => $coursePost->post_title,
            'course_description' => $coursePost->post_content,
            'course_url' => $coursePost->guid,
        ];

        $lessonDataFinal = $topicData + $courseData + $current_user;
        $lessonDataFinal['post_id'] = $topic_id;

        return ['triggered_entity' => 'AcademyLms', 'triggered_entity_id' => 3, 'data' => $lessonDataFinal, 'flows' => $flows];
    }

    public static function academyHandleCourseComplete($course_id)
    {
        $flows = Flow::exists('AcademyLms', 4);
        $flows = $flows ? self::academyLmsFlowFilter($flows, 'selectedCourse', $course_id) : false;

        if (!$flows) {
            return;
        }

        $coursePost = get_post($course_id);
        $courseData = [
            'course_id' => $coursePost->ID,
            'course_title' => $coursePost->post_title,
            'course_url' => $coursePost->guid,
        ];
        $user = self::academyLmsGetUserInfo(get_current_user_id());
        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $courseDataFinal = $courseData + $current_user;
        $courseDataFinal['post_id'] = $course_id;

        return ['triggered_entity' => 'AcademyLms', 'triggered_entity_id' => 4, 'data' => $courseDataFinal, 'flows' => $flows];
    }

    public static function academyLmsGetUserInfo($user_id)
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

    public static function affwpNewAffiliateApproved($affiliate_id, $status, $old_status)
    {
        $flows = Flow::exists('Affiliate', 1);
        if (!$flows) {
            return;
        }
        $user_id =  affwp_get_affiliate_user_id($affiliate_id);

        if (!$user_id) {
            return;
        }
        if ('pending' === $status) {
            return;
        }

        $affiliate = affwp_get_affiliate($affiliate_id);
        $user = get_user_by('id', $user_id);

        $data = [
            'status' => $status,
            'flat_rate_basis' => $affiliate->flat_rate_basis,
            'payment_email' => $affiliate->payment_email,
            'rate_type' => $affiliate->rate_type,
            'old_status' => $old_status,


        ];

        return ['triggered_entity' => 'Affiliate', 'triggered_entity_id' => 1, 'data' => $data, 'flows' => $flows];
    }

    public static function affwpUserBecomesAffiliate($affiliate_id, $status, $old_status)
    {
        if ('active' !== $status) {
            return $status;
        }

        $flows = Flow::exists('Affiliate', 2);
        if (!$flows) {
            return;
        }
        $user_id =  affwp_get_affiliate_user_id($affiliate_id);

        if (!$user_id) {
            return;
        }

        $affiliate = affwp_get_affiliate($affiliate_id);
        $user = get_user_by('id', $user_id);

        $data = [
            'status' => $status,
            'flat_rate_basis' => $affiliate->flat_rate_basis,
            'payment_email' => $affiliate->payment_email,
            'rate_type' => $affiliate->rate_type,
            'old_status' => $old_status,


        ];

        return ['triggered_entity' => 'Affiliate', 'triggered_entity_id' => 2, 'data' => $data, 'flows' => $flows];
    }

    public static function affwpAffiliateMakesReferral($referral_id)
    {
        $flows = Flow::exists('Affiliate', 3);
        if (!$flows) {
            return;
        }
        $referral = affwp_get_referral($referral_id);
        $affiliate = affwp_get_affiliate($referral->affiliate_id);
        $user_id = affwp_get_affiliate_user_id($referral->affiliate_id);
        $affiliateNote = maybe_serialize(affwp_get_affiliate_meta($affiliate->affiliate_id, 'notes', true));
        $user               = get_user_by('id', $user_id);
        $data = [
            'affiliate_id' => $referral->affiliate_id,
            'affiliate_url' => maybe_serialize(affwp_get_affiliate_referral_url(array('affiliate_id' => $referral->affiliate_id))),
            'referral_description' => $referral->description,
            'amount' => $referral->amount,
            'context' => $referral->context,
            'campaign' => $referral->campaign,
            'reference' => $referral->reference,
            'flat_rate_basis' => $affiliate->flat_rate_basis,
            'account_email' => $user->user_email,
            'payment_email' => $affiliate->payment_email,
            'rate_type' => $affiliate->rate_type,
            'affiliate_note' => $affiliateNote,

        ];

        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
                $flowDetails = $flow->flow_details;
            }
        }

        $allTypes = $flowDetails->allType;

        $selectedTypeID = $flowDetails->selectedType;


        foreach ($allTypes as $type) {
            if ($referral->type == $type->type_key && $type->type_id == $selectedTypeID) {
                $execData = ['triggered_entity' => 'Affiliate', 'triggered_entity_id' => 3, 'data' => $data, 'flows' => $flows];
                break;
            }
        }

        if ($selectedTypeID == 'any') {
            $execData = ['triggered_entity' => 'Affiliate', 'triggered_entity_id' => 3, 'data' => $data, 'flows' => $flows];
        }
        return $execData;
    }

    public static function affwpAffiliatesReferralSpecificTypeRejected($referral_id, $new_status, $old_status)
    {
        $flows = Flow::exists('Affiliate', 4);
        if (!$flows) {
            return;
        }


        if ((string) $new_status === (string) $old_status || 'rejected' !== (string) $new_status) {
            return $new_status;
        }

        $referral      = affwp_get_referral($referral_id);
        $type          = $referral->type;
        $user_id       = affwp_get_affiliate_user_id($referral->affiliate_id);
        $user               = get_user_by('id', $user_id);
        $affiliate          = affwp_get_affiliate($referral->affiliate_id);
        $affiliateNote = maybe_serialize(affwp_get_affiliate_meta($affiliate->affiliate_id, 'notes', true));


        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
                $flowDetails = $flow->flow_details;
            }
        }

        $allTypes = $flowDetails->allType;

        $selectedTypeID = $flowDetails->selectedType;

        $data = [
            'affiliate_id' => $referral->affiliate_id,
            'affiliate_url' => maybe_serialize(affwp_get_affiliate_referral_url(array('affiliate_id' => $referral->affiliate_id))),
            'referral_description' => $referral->description,
            'amount' => $referral->amount,
            'context' => $referral->context,
            'campaign' => $referral->campaign,
            'reference' => $referral->reference,
            'status' => $new_status,
            'flat_rate_basis' => $affiliate->flat_rate_basis,
            'account_email' => $user->user_email,
            'payment_email' => $affiliate->payment_email,
            'rate_type' => $affiliate->rate_type,
            'affiliate_note' => $affiliateNote,
            'old_status' => $old_status,

        ];

        foreach ($allTypes as $type) {
            if ($referral->type == $type->type_key && $type->type_id == $selectedTypeID) {
                $execData = ['triggered_entity' => 'Affiliate', 'triggered_entity_id' => 4, 'data' => $data, 'flows' => $flows];
            }
        }

        if ($selectedTypeID == 'any') {
            $execData = ['triggered_entity' => 'Affiliate', 'triggered_entity_id' => 4, 'data' => $data, 'flows' => $flows];
        }

        return $execData;
    }

    public static function affwpAffiliatesReferralSpecificTypePaid($referral_id, $new_status, $old_status)
    {
        $flows = Flow::exists('Affiliate', 5);
        if (!$flows) {
            return;
        }


        if ((string) $new_status === (string) $old_status || 'paid' !== (string) $new_status) {
            return $new_status;
        }

        $referral      = affwp_get_referral($referral_id);
        $type          = $referral->type;
        $user_id       = affwp_get_affiliate_user_id($referral->affiliate_id);
        $user               = get_user_by('id', $user_id);
        $affiliate          = affwp_get_affiliate($referral->affiliate_id);
        $affiliateNote = maybe_serialize(affwp_get_affiliate_meta($affiliate->affiliate_id, 'notes', true));


        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
                $flowDetails = $flow->flow_details;
            }
        }

        $allTypes = $flowDetails->allType;

        $selectedTypeID = $flowDetails->selectedType;

        $data = [
            'affiliate_id' => $referral->affiliate_id,
            'affiliate_url' => maybe_serialize(affwp_get_affiliate_referral_url(array('affiliate_id' => $referral->affiliate_id))),
            'referral_description' => $referral->description,
            'amount' => $referral->amount,
            'context' => $referral->context,
            'campaign' => $referral->campaign,
            'reference' => $referral->reference,
            'status' => $new_status,
            'flat_rate_basis' => $affiliate->flat_rate_basis,
            'account_email' => $user->user_email,
            'payment_email' => $affiliate->payment_email,
            'rate_type' => $affiliate->rate_type,
            'affiliate_note' => $affiliateNote,
            'old_status' => $old_status,

        ];

        foreach ($allTypes as $type) {
            if ($referral->type == $type->type_key && $type->type_id == $selectedTypeID) {
                $execData = ['triggered_entity' => 'Affiliate', 'triggered_entity_id' => 5, 'data' => $data, 'flows' => $flows];
            }
        }

        if ($selectedTypeID == 'any') {
            $execData = ['triggered_entity' => 'Affiliate', 'triggered_entity_id' => 5, 'data' => $data, 'flows' => $flows];
        }

        return $execData;
    }

    public static function handleArFormSubmit($params, $errors, $form, $item_meta_values)
    {
        $form_id = $form->id;
        $flows = Flow::exists('ARForm', $form_id);
        if (!$flows) {
            return;
        }

        return ['triggered_entity' => 'ARForm', 'triggered_entity_id' => $form_id, 'data' => $item_meta_values, 'flows' => $flows];
    }

    public static function ARMemberHandleRegisterForm($user_id, $post_data)
    {
        if (array_key_exists('arm_form_id', $post_data) === false) {
            return;
        }
        $form_id = $post_data['arm_form_id'];
        $flows = Flow::exists('ARMember', $form_id = $post_data['arm_form_id']);
        if (empty($flows)) {
            return;
        }
        $userInfo = static::ARMemberGetUserInfo($user_id);
        $post_data['user_id'] = $user_id;
        $post_data['nickname'] = $userInfo['nickname'];
        $post_data['avatar_url'] = $userInfo['avatar_url'];

        return ['triggered_entity' => 'ARMember', 'triggered_entity_id' => $form_id, 'data' => $post_data, 'flows' => $flows];
    }

    public static function ARMemberGetUserInfo($user_id)
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

    public static function ARMemberHandleUpdateUserByForm($user_ID, $posted_data)
    {
        if (array_key_exists('form_random_key', $posted_data) === false) {
            return;
        }
        $form_id = str_starts_with($posted_data['form_random_key'], '101');
        if (!$form_id) {
            return;
        }
        $form_id = '101_2';
        $flows = Flow::exists('ARMember', $form_id);
        if (empty($flows)) {
            return;
        }
        $userInfo = static::ARMemberGetUserInfo($user_ID);
        $posted_data['user_id'] = $user_ID;
        $posted_data['nickname'] = $userInfo['nickname'];
        $posted_data['avatar_url'] = $userInfo['avatar_url'];

        return ['triggered_entity' => 'ARMember', 'triggered_entity_id' => $form_id, 'data' => $posted_data, 'flows' => $flows];
    }

    public static function ARMemberHandleMemberAddByAdmin($user_id, $post_data)
    {
        if (array_key_exists('action', $post_data) === false) {
            return;
        }
        $form_id = $post_data['form'];
        if (!$form_id) {
            return;
        }
        $form_id = '101_3';
        $flows = Flow::exists('ARMember', $form_id);
        if (empty($flows)) {
            return;
        }
        $userInfo = static::ARMemberGetUserInfo($user_id);
        $post_data['user_id'] = $user_id;
        $post_data['nickname'] = $userInfo['nickname'];
        $post_data['avatar_url'] = $userInfo['avatar_url'];

        return ['triggered_entity' => 'ARMember', 'triggered_entity_id' => $form_id, 'data' => $post_data, 'flows' => $flows];
    }

    public static function ARMemberHandleCancelSubscription($user_id, $plan_id)
    {
        $flows = Flow::exists('ARMember', '4');
        if (empty($flows)) {
            return;
        }
        $finalData = static::ARMemberGetUserInfo($user_id, $plan_id);
        return ['triggered_entity' => 'ARMember', 'triggered_entity_id' => 4, 'data' => $finalData, 'flows' => $flows];
    }

    public static function ARMemberHandlePlanChangeAdmin($user_id, $plan_id)
    {
        $flows = Flow::exists('ARMember', '5');
        if (empty($flows)) {
            return;
        }
        $finalData = static::ARMemberGetUserInfo($user_id, $plan_id);
        return ['triggered_entity' => 'ARMember', 'triggered_entity_id' => 5, 'data' => $finalData, 'flows' => $flows];
    }

    public static function ARMemberHandleRenewSubscriptionPlan($user_id, $plan_id)
    {
        $flows = Flow::exists('ARMember', '6');
        if (empty($flows)) {
            return;
        }
        $finalData = static::ARMemberGetUserInfo($user_id, $plan_id);
        return ['triggered_entity' => 'ARMember', 'triggered_entity_id' => 6, 'data' => $finalData, 'flows' => $flows];
    }

    public static function beaverContactFormSubmitted($mailto, $subject, $template, $headers, $settings, $result)
    {
        $form_id = 'bb_contact_form';
        $flows = Flow::exists('Beaver', $form_id);
        if (!$flows) {
            return;
        }

        $template = str_replace('Name', '|Name', $template);
        $template = str_replace('Email', '|Email', $template);
        $template = str_replace('Phone', '|Phone', $template);
        $template = str_replace('Message', '|Message', $template);

        $filterData = explode('|', $template);
        $filterData = array_map('trim', $filterData);
        $filterData = array_filter($filterData, function ($value) {
            return $value !== '';
        });

        $data = ['subject' => isset($subject) ? $subject : '',];
        foreach ($filterData as $value) {
            $item = explode(':', $value);
            $data[strtolower($item[0])] = trim($item[1]);
        }
        return ['triggered_entity' => 'Beaver', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }

    public static function beaverLoginFormSubmitted($settings, $password, $name, $template_id, $post_id)
    {
        $form_id = 'bb_login_form';
        $flows = Flow::exists('Beaver', $form_id);
        if (!$flows) {
            return;
        }

        $data = [
            'name' => isset($name) ? $name : '',
            'password' => isset($password) ? $password : '',
        ];
        return ['triggered_entity' => 'Beaver', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }

    public static function beaverSubscribeFormSubmitted($response, $settings, $email, $name, $template_id, $post_id)
    {
        $form_id = 'bb_subscription_form';
        $flows = Flow::exists('Beaver', $form_id);
        if (!$flows) {
            return;
        }

        $data = [
            'name' => isset($name) ? $name : '',
            'email' => isset($email) ? $email : '',
        ];
        return ['triggered_entity' => 'Beaver', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }

    public static function handleBricksSubmit($form)
    {
        $fields = $form->get_fields();
        $formId = $fields['formId'];
        $files = $form->get_uploaded_files();

        $flows = Flow::exists('Bricks', $formId);
        if (!$flows) {
            return;
        }

        $data = [];
        foreach ($fields as $key => $value) {
            $fieldId = str_replace('form-field-', '', $key);
            $data[$fieldId] = (is_array($value) && count($value) == 1) ? $value[0] : $value;
        }
        foreach ($files as $key => $item) {
            $fieldId = str_replace('form-field-', '', $key);

            if (is_array($item)) {
                foreach ($item as $file) {
                    if (!isset($file['file'])) {
                        continue;
                    }
                    $data[$fieldId][] = $file['file'];
                }
            } else {
                if (!isset($item['file'])) {
                    continue;
                }
                $data[$fieldId] = $item['file'];
            }
        }

        return ['triggered_entity' => 'Bricks', 'triggered_entity_id' => $formId, 'data' => $data, 'flows' => $flows];
    }

    public static function handleBrizySubmit($fields, $form)
    {
        if (!method_exists($form, 'getId')) {
            return;
        }
        $form_id = $form->getId();
        $flows = Flow::exists('Brizy', $form_id);
        if (!$flows) {
            return;
        }

        $data = [];
        $AllFields = $fields;
        foreach ($AllFields as $element) {
            if ($element->type == 'FileUpload' && !empty($element->value)) {
                $upDir = wp_upload_dir();
                $files = $element->value;
                $value = [];
                $newFileLink = Common::filePath($files);
                $data[$element->name] = $newFileLink;
            } elseif ($element->type == 'checkbox') {
                $value = explode(',', $element->value);
                $data[$element->name] = $value;
            } else {
                $data[$element->name] = $element->value;
            }
        }

        return ['triggered_entity' => 'Brizy', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }

    public static function BuddyBossGetUserInfo($user_id, $extra = false)
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
        if ($extra == '13') {
            $user['user_profile_url'] = maybe_serialize(bbp_get_user_profile_url($user_id));
        }
        return $user;
    }

    public static function buddyBossHandleAcceptFriendRequest($id, $initiator_user_id, $friend_user_id, $friendship)
    {
        $flows = Flow::exists('BuddyBoss', 1);
        if (!$flows) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($friend_user_id);
        $current_user = [];
        $init_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];
        $user = static::BuddyBossGetUserInfo($initiator_user_id);
        $init_user = [
            'friend_first_name' => $user['first_name'],
            'friend_last_name' => $user['last_name'],
            'friend_email' => $user['user_email'],
            'friend_nickname' => $user['nickname'],
            'friend_avatar_url' => $user['avatar_url'],
            'friend_id' => $initiator_user_id,
        ];
        $data = $current_user + $init_user;

        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 1, 'data' => $data, 'flows' => $flows];
    }

    public static function buddyBossHandleSendsFriendRequest($id, $initiator_user_id, $friend_user_id, $friendship)
    {
        $flows = Flow::exists('BuddyBoss', 2);
        if (!$flows) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($initiator_user_id);
        $current_user = [];
        $init_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];
        $user = static::BuddyBossGetUserInfo($friend_user_id);
        $init_user = [
            'friend_first_name' => $user['first_name'],
            'friend_last_name' => $user['last_name'],
            'friend_email' => $user['user_email'],
            'friend_nickname' => $user['nickname'],
            'friend_avatar_url' => $user['avatar_url'],
            'friend_id' => $friend_user_id,
        ];
        $data = $current_user + $init_user;

        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 2, 'data' => $data, 'flows' => $flows];
    }

    protected static function BuddyBossFlowFilter($flows, $key, $value)
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

    public static function BuddyBossGetTopicInfo($topic_id)
    {
        $topicInfo = get_post($topic_id);
        $topic = [];
        if ($topicInfo) {
            $topic = [
                'topic_title' => $topicInfo->post_title,
                'topic_id' => $topicInfo->ID,
                'topic_url' => get_permalink($topicInfo->ID),
                'topic_content' => $topicInfo->post_content,
            ];
        }
        return $topic;
    }

    public static function BuddyBossGetForumInfo($forum_id)
    {
        $forumInfo = get_post($forum_id);
        $forum = [];
        if ($forumInfo) {
            $forum = [
                'forum_title' => $forumInfo->post_title,
                'forum_id' => $forumInfo->ID,
                'forum_url' => get_permalink($forumInfo->ID),
            ];
        }
        return $forum;
    }

    public static function buddyBossHandleCreateTopic($topic_id, $forum_id, $anonymous_data, $topic_author)
    {
        $flows = Flow::exists('BuddyBoss', 3);
        $flows = static::BuddyBossFlowFilter($flows, 'selectedForum', $forum_id);
        if (!$flows) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($topic_author);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $topics = static::BuddyBossGetTopicInfo($topic_id);
        $forums = static::BuddyBossGetForumInfo($forum_id);
        $data = $current_user + $topics + $forums;

        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 3, 'data' => $data, 'flows' => $flows];
    }

    public static function buddyBossHandleJoinPublicGroup($group_id, $user_id)
    {
        $flows = Flow::exists('BuddyBoss', 9);
        $flows = static::BuddyBossFlowFilter($flows, 'selectedGroup', $group_id);
        if (!$flows) {
            return;
        }

        $groups = static::BuddyBossGetGroupInfo($group_id, 'public');
        if (!count($groups)) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($user_id);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $data = $current_user + $groups;
        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 9, 'data' => $data, 'flows' => $flows];
    }

    public static function BuddyBossGetGroupInfo($group_id, $status = '', $extra = false)
    {
        global $wpdb;
        if ($status == '') {
            $group = $wpdb->get_results(
                $wpdb->prepare("select id,name,description from {$wpdb->prefix}bp_groups where id = %d", $group_id)
            );
        } else {
            $group = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT id,name,description FROM {$wpdb->prefix}bp_groups WHERE id = %d AND status = %s",
                    $group_id,
                    $status
                )
            );
        }

        if (count($group)) {
            $groupInfo = [
                'group_id' => $group[0]->id,
                'group_title' => $group[0]->name,
                'group_desc' => $group[0]->description
            ];
        }
        if ($extra == '9') {
            $group_obj = groups_get_group($group_id);
            $groupInfo['manage_group_request_url'] = maybe_serialize(bp_get_group_permalink($group_obj) . 'admin/membership-requests/');
        }
        return $groupInfo;
    }

    public static function buddyBossHandleJoinPrivateGroup($user_id, $group_id)
    {
        $flows = Flow::exists('BuddyBoss', 10);
        $flows = static::BuddyBossFlowFilter($flows, 'selectedGroup', $group_id);
        if (!$flows) {
            return;
        }

        $groups = static::BuddyBossGetGroupInfo($group_id, 'private');
        if (!count($groups)) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($user_id);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $data = $current_user + $groups;
        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 10, 'data' => $data, 'flows' => $flows];
    }

    public static function buddyBossHandleLeavesGroup($group_id, $user_id)
    {
        $flows = Flow::exists('BuddyBoss', 11);
        $flows = static::BuddyBossFlowFilter($flows, 'selectedGroup', $group_id);
        if (!$flows) {
            return;
        }
        $groups = static::BuddyBossGetGroupInfo($group_id);
        if (!count($groups)) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($user_id);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $data = $current_user + $groups;
        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 11, 'data' => $data, 'flows' => $flows];
    }

    public static function BuddyBossGetActivityInfo($activity_id, $group_id, $user_id)
    {
        global $wpdb;

        $activity = $wpdb->get_results("select id,content from {$wpdb->prefix}bp_activity where id = $activity_id");

        $group = groups_get_group($group_id);
        $activityInfo = [];
        if (count($activity)) {
            $activityInfo = [
                'activity_id' => $activity[0]->id,
                'activity_url' => bp_get_group_permalink($group) . 'activity',
                'activity_content' => $activity[0]->content,
                'activity_stream_url' => bp_core_get_user_domain($user_id) . 'activity/' . $activity_id,
            ];
        }
        return $activityInfo;
    }

    public static function buddyBossHandlePostGroupActivity($content, $user_id, $group_id, $activity_id)
    {
        $flows = Flow::exists('BuddyBoss', 12);
        $flows = static::BuddyBossFlowFilter($flows, 'selectedGroup', $group_id);
        if (!$flows) {
            return;
        }

        $groups = static::BuddyBossGetGroupInfo($group_id);
        if (!count($groups)) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($user_id);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $posts = static::BuddyBossGetActivityInfo($activity_id, $group_id, $user_id);
        $data = $current_user + $groups + $posts;
        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 12, 'data' => $data, 'flows' => $flows];
    }

    public static function BuddyBossGetReplyInfo($reply_id)
    {
        $replyInfo = get_post($reply_id);
        $reply = [];
        if ($replyInfo) {
            $reply = [
                'reply_content' => $replyInfo->post_content,
            ];
        }
        return $reply;
    }

    public static function buddyBossHandleRepliesTopic($reply_id, $topic_id, $forum_id)
    {
        $flows = Flow::exists('BuddyBoss', 4);
        $flows = static::BuddyBossFlowFilter($flows, 'selectedTopic', $topic_id);
        if (!$flows) {
            return;
        }

        $topics = static::BuddyBossGetTopicInfo($topic_id);
        if (!count($topics)) {
            return;
        }

        $forums = static::BuddyBossGetForumInfo($forum_id);
        if (!count($forums)) {
            return;
        }

        $replies = static::BuddyBossGetReplyInfo($reply_id);
        if (!count($replies)) {
            return;
        }

        $user_id = get_current_user_id();
        $user = static::BuddyBossGetUserInfo($user_id);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $data = $current_user + $topics + $forums + $replies;
        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 4, 'data' => $data, 'flows' => $flows];
    }

    public static function buddyBossHandleRequestPrivateGroup($user_id, $admins, $group_id, $request_id)
    {
        $flows = Flow::exists('BuddyBoss', 13);
        $flows = static::BuddyBossFlowFilter($flows, 'selectedGroup', $group_id);
        if (!$flows) {
            return;
        }

        $groups = static::BuddyBossGetGroupInfo($group_id, 'private', '13');
        if (!count($groups)) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($user_id, '13');
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
            'user_profile_url' => $user['user_profile_url'],
        ];

        $data = $current_user + $groups;
        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 13, 'data' => $data, 'flows' => $flows];
    }

    public static function buddyBossHandleSendEmailInvites($user_id, $post)
    {
        $flows = Flow::exists('BuddyBoss', 5);
        if (!$flows) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($user_id);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $data = $current_user;
        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 5, 'data' => $data, 'flows' => $flows];
    }

    public static function buddyBossHandleUpdateAvatar($item_id, $type, $avatar_data)
    {
        $flows = Flow::exists('BuddyBoss', 6);
        if (!$flows) {
            return;
        }

        $user_id = $avatar_data['item_id'];

        $user = static::BuddyBossGetUserInfo($user_id);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $data = $current_user;
        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 6, 'data' => $data, 'flows' => $flows];
    }

    public static function BuddyBossFields($id)
    {
        if (empty($id)) {
            wp_send_json_error(
                __(
                    'Requested parameter is empty',
                    'bit-integrations'
                ),
                400
            );
        }
        if ($id == 1 || $id == 2) {
            $fields = [
                'First Name' => (object) [
                    'fieldKey' => 'first_name',
                    'fieldName' => 'First Name'
                ],
                'Last Name' => (object) [
                    'fieldKey' => 'last_name',
                    'fieldName' => 'Last Name'
                ],
                'Nick Name' => (object) [
                    'fieldKey' => 'nickname',
                    'fieldName' => 'Nick Name'
                ],
                'Avatar URL' => (object) [
                    'fieldKey' => 'avatar_url',
                    'fieldName' => 'Avatar URL'
                ],
                'Email' => (object) [
                    'fieldKey' => 'user_email',
                    'fieldName' => 'Email',
                ],
                'Friend ID' => (object) [
                    'fieldKey' => 'friend_id',
                    'fieldName' => 'Friend ID',
                ],
                'Friend First Name' => (object) [
                    'fieldKey' => 'friend_first_name',
                    'fieldName' => 'Friend First Name'
                ],
                'Friend Last Name' => (object) [
                    'fieldKey' => 'friend_last_name',
                    'fieldName' => 'Friend Last Name'
                ],
                'Fiend Nick Name' => (object) [
                    'fieldKey' => 'friend_nickname',
                    'fieldName' => 'Fiend Nick Name'
                ],
                'Friend Email' => (object) [
                    'fieldKey' => 'friend_email',
                    'fieldName' => 'Friend Email'
                ],
                'Friend Avatar URL' => (object) [
                    'fieldKey' => 'friend_avatar_url',
                    'fieldName' => 'Friend Avatar URL'
                ],

            ];
        } elseif ($id == 3 || $id == 4) {
            $fields = [
                'First Name' => (object) [
                    'fieldKey' => 'first_name',
                    'fieldName' => 'First Name'
                ],
                'Last Name' => (object) [
                    'fieldKey' => 'last_name',
                    'fieldName' => 'Last Name'
                ],
                'Nick Name' => (object) [
                    'fieldKey' => 'nickname',
                    'fieldName' => 'Nick Name'
                ],
                'Avatar URL' => (object) [
                    'fieldKey' => 'avatar_url',
                    'fieldName' => 'Avatar URL'
                ],
                'Email' => (object) [
                    'fieldKey' => 'user_email',
                    'fieldName' => 'Email',
                ],
                'Topic Title' => (object) [
                    'fieldKey' => 'topic_title',
                    'fieldName' => 'Topic Title',
                ],
                'Topic ID' => (object) [
                    'fieldKey' => 'topic_id',
                    'fieldName' => 'Topic ID',
                ],
                'Topic URL' => (object) [
                    'fieldKey' => 'topic_url',
                    'fieldName' => 'Topic URL',
                ],
                'Topic Content' => (object) [
                    'fieldKey' => 'topic_content',
                    'fieldName' => 'Topic Content',
                ],
                'Forum ID' => (object) [
                    'fieldKey' => 'forum_id',
                    'fieldName' => 'Forum ID',
                ],
                'Forum Title' => (object) [
                    'fieldKey' => 'forum_title',
                    'fieldName' => 'Forum Title',
                ],
                'Forum URL' => (object) [
                    'fieldKey' => 'forum_url',
                    'fieldName' => 'Forum URL',
                ],
            ];
            if ($id == 4) {
                $fields['Reply Content'] = (object) [
                    'fieldKey' => 'reply_content',
                    'fieldName' => 'Reply Content',
                ];
            }
        } elseif ($id == 7) {
            $buddyBossProfileFields = static::getBuddyBossProfileField();
            foreach ($buddyBossProfileFields as $key => $val) {
                $fields[$val->name] = (object) [
                    'fieldKey' => str_replace(' ', '_', $val->name),
                    'fieldName' => $val->name,
                ];
            }
        } elseif ($id == 9 || $id == 10 || $id == 11 || $id == 13) {
            $fields = [
                'Group Title' => (object) [
                    'fieldKey' => 'group_title',
                    'fieldName' => 'Group Title',
                ],
                'Group ID' => (object) [
                    'fieldKey' => 'group_id',
                    'fieldName' => 'Group ID',
                ],
                'Group Description' => (object) [
                    'fieldKey' => 'group_desc',
                    'fieldName' => 'Group Description',
                ],
                'First Name' => (object) [
                    'fieldKey' => 'first_name',
                    'fieldName' => 'First Name'
                ],
                'Last Name' => (object) [
                    'fieldKey' => 'last_name',
                    'fieldName' => 'Last Name'
                ],
                'Nick Name' => (object) [
                    'fieldKey' => 'nickname',
                    'fieldName' => 'Nick Name'
                ],
                'Avatar URL' => (object) [
                    'fieldKey' => 'avatar_url',
                    'fieldName' => 'Avatar URL'
                ],
                'Email' => (object) [
                    'fieldKey' => 'user_email',
                    'fieldName' => 'Email',
                ]
            ];
            if ($id == 13) {
                $fields['User Profile URL'] = (object) [
                    'fieldKey' => 'user_profile_url',
                    'fieldName' => 'User Profile URL',
                ];

                $fields['Manage Group Request URL'] = (object) [
                    'fieldKey' => 'manage_group_request_url',
                    'fieldName' => 'Manage Group Request URL',
                ];
            }
        } elseif ($id == 12) {
            $fields = [
                'Group Title' => (object) [
                    'fieldKey' => 'group_title',
                    'fieldName' => 'Group Title',
                ],
                'Group ID' => (object) [
                    'fieldKey' => 'group_id',
                    'fieldName' => 'Group ID',
                ],
                'Group Description' => (object) [
                    'fieldKey' => 'group_desc',
                    'fieldName' => 'Group Description',
                ],
                'First Name' => (object) [
                    'fieldKey' => 'first_name',
                    'fieldName' => 'First Name'
                ],
                'Last Name' => (object) [
                    'fieldKey' => 'last_name',
                    'fieldName' => 'Last Name'
                ],
                'Nick Name' => (object) [
                    'fieldKey' => 'nickname',
                    'fieldName' => 'Nick Name'
                ],
                'Avatar URL' => (object) [
                    'fieldKey' => 'avatar_url',
                    'fieldName' => 'Avatar URL'
                ],
                'Email' => (object) [
                    'fieldKey' => 'user_email',
                    'fieldName' => 'Email',
                ],
                'Activity ID' => (object) [
                    'fieldKey' => 'activity_id',
                    'fieldName' => 'Activity ID',
                ],
                'Activity URL' => (object) [
                    'fieldKey' => 'activity_url',
                    'fieldName' => 'Activity URL',
                ],
                'Activity Content' => (object) [
                    'fieldKey' => 'activity_content',
                    'fieldName' => 'Activity Content',
                ],
                'Activity Stream URL' => (object) [
                    'fieldKey' => 'activity_stream_url',
                    'fieldName' => 'Activity Stream URL',
                ],

            ];
        } else {
            $fields = [
                'First Name' => (object) [
                    'fieldKey' => 'first_name',
                    'fieldName' => 'First Name'
                ],
                'Last Name' => (object) [
                    'fieldKey' => 'last_name',
                    'fieldName' => 'Last Name'
                ],
                'Nick Name' => (object) [
                    'fieldKey' => 'nickname',
                    'fieldName' => 'Nick Name'
                ],
                'Avatar URL' => (object) [
                    'fieldKey' => 'avatar_url',
                    'fieldName' => 'Avatar URL'
                ],
                'Email' => (object) [
                    'fieldKey' => 'user_email',
                    'fieldName' => 'Email',
                ],
            ];
        }

        foreach ($fields as $field) {
            $fieldsNew[] = [
                'name' => $field->fieldKey,
                'type' => 'text',
                'label' => $field->fieldName,
            ];
        }
        return $fieldsNew;
    }

    public static function getBuddyBossProfileField()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bp_xprofile_fields';
        $results = $wpdb->get_results("SELECT id, type , name FROM $table_name");
        return $results;
    }

    public static function buddyBossHandleUpdateProfile($user_id, $posted_field_ids, $errors, $old_values, $new_values)
    {
        $flows = Flow::exists('BuddyBoss', 7);
        if (!$flows) {
            return;
        }

        $current_user = [];

        $fields = static::BuddyBossFields(7);
        for ($i = 0; $i < count($fields); $i++) {
            $current_user[$fields[$i]['name']] = $new_values[$i + 1]['value'];
        }

        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 7, 'data' => $current_user, 'flows' => $flows];
    }

    public static function buddyBossHandleAccountActive($user_id, $key, $user)
    {
        $flows = Flow::exists('BuddyBoss', 8);
        if (!$flows) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($user_id);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 8, 'data' => $current_user, 'flows' => $flows];
    }

    public static function buddyBossHandleInviteeActiveAccount($user_id, $inviter_id, $post_id)
    {
        $flows = Flow::exists('BuddyBoss', 14);
        if (!$flows) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($inviter_id);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 14, 'data' => $current_user, 'flows' => $flows];
    }

    public static function buddyBossHandleInviteeRegisterAccount($user_id, $inviter_id, $post_id)
    {
        $flows = Flow::exists('BuddyBoss', 15);
        if (!$flows) {
            return;
        }

        $user = static::BuddyBossGetUserInfo($inviter_id);
        $current_user = [];

        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        return ['triggered_entity' => 'BuddyBoss', 'triggered_entity_id' => 15, 'data' => $current_user, 'flows' => $flows];
    }

    public static function CartFlowHandleOrderCreateWc($order_id, $importType)
    {
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            return false;
        }

        $metaData = get_post_meta($order_id);
        $chekoutPageId = (int)$metaData['_wcf_checkout_id'][0];
        $flows = Flow::exists('CartFlow', $chekoutPageId);

        if (!$flows) {
            return false;
        }

        $order = wc_get_order($order_id);
        $finalData = [];
        foreach ($metaData as $key => $value) {
            $finalData[ltrim($key, '_')] = $value[0];
        }
        $finalData['order_products'] = static::CartFlowAccessOrderData($order);
        $finalData['order_id'] = $order_id;

        return ['triggered_entity' => 'CartFlow', 'triggered_entity_id' => $chekoutPageId, 'data' => $finalData, 'flows' => $flows];
    }

    public static function CartFlowAccessOrderData($order)
    {
        $line_items_all = [];
        $count = 0;
        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $item->get_product_id();
            $variation_id = $item->get_variation_id();
            $product = $item->get_product();
            $product_name = $item->get_name();
            $quantity = $item->get_quantity();
            $subtotal = $item->get_subtotal();
            $total = $item->get_total();
            $subtotal_tax = $item->get_subtotal_tax();
            $taxclass = $item->get_tax_class();
            $taxstat = $item->get_tax_status();
            $label = 'line_items_';
            $count++;
            $line_items_all['line_items'][] = (object)[
                'product_id' => $product_id,
                'variation_id' => $variation_id,
                'product_name' => $product_name,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'total' => $total,
                'subtotal_tax' => $subtotal_tax,
                'tax_class' => $taxclass,
                'tax_status' => $taxstat,
            ];
        }
        return $line_items_all;
    }

    public static function CF7HandleSubmit()
    {
        $submission = \WPCF7_Submission::get_instance();
        $postID = (int) $submission->get_meta('container_post_id');

        if (!$submission || !$posted_data = $submission->get_posted_data()) {
            return;
        }

        if (isset($posted_data['_wpcf7'])) {
            $form_id = $posted_data['_wpcf7'];
        } else {
            $current_form = \WPCF7_ContactForm::get_current();
            $form_id = $current_form->id();
        }

        $flows = Flow::exists('CF7', $form_id);

        if (!$flows) {
            return false;
        }

        $files = $submission->uploaded_files();
        $posted_data = array_merge($posted_data, $files);

        if ($postID) {
            $posted_data['post_id'] = $postID;
        }

        // array to string conversion for radio and select fields
        $data = [];
        foreach ($posted_data as $key => $value) {
            if (is_array($value) && count($value) == 1) {
                $data[$key] = $posted_data[$key][0];
            } else {
                $data[$key] = $posted_data[$key];
            }
        }

        return ['triggered_entity' => 'CF7', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }

    public static function handleDiviSubmit($et_pb_contact_form_submit, $et_contact_error, $contact_form_info)
    {
        $form_id = $contact_form_info['contact_form_unique_id'] . '_' . $contact_form_info['contact_form_number'];
        $flows = Flow::exists('Divi', $form_id);
        if (!$flows || $et_contact_error) {
            return;
        }

        $data = [];
        $fields = $et_pb_contact_form_submit;
        foreach ($fields as $key => $field) {
            $data[$key] = $field['value'];
        }

        return ['triggered_entity' => 'Divi', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }

    public static function eddHandlePurchaseProduct($payment_id)
    {
        $flows = Flow::exists('EDD', 1);
        if (!$flows) {
            return;
        }

        $cart_items = edd_get_payment_meta_cart_details($payment_id);
        if (!class_exists('\EDD_Payment') || empty($cart_items)) {
            return;
        }

        $payment = new \EDD_Payment($payment_id);

        foreach ($cart_items as $item) {
            $final_data = [
                'user_id' => $payment->user_id,
                'first_name' => $payment->first_name,
                'last_name' => $payment->last_name,
                'user_email' => $payment->email,
                'product_name' => $item['name'],
                'product_id' => $item['id'],
                'order_item_id' => $item['order_item_id'],
                'discount_codes' => $payment->discounts,
                'order_discounts' => $item['discount'],
                'order_subtotal' => $payment->subtotal,
                'order_total' => $payment->total,
                'order_tax' => $payment->tax,
                'payment_method' => $payment->gateway,
            ];
        }

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedProduct = !empty($flowDetails->selectedProduct) ? $flowDetails->selectedProduct : [];

        return ['triggered_entity' => 'EDD', 'triggered_entity_id' => 1, 'data' => $final_data, 'flows' => $flows];
    }

    public static function eddHandlePurchaseProductDiscountCode($payment_id, $payment, $customer)
    {
        $flows = Flow::exists('EDD', 2);
        if (!$flows) {
            return;
        }

        $cart_items = edd_get_payment_meta_cart_details($payment_id);
        if (!class_exists('\EDD_Payment') || empty($cart_items)) {
            return;
        }

        $payment = new \EDD_Payment($payment_id);
        foreach ($cart_items as $item) {
            $final_data = [
                'user_id' => $payment->user_id,
                'first_name' => $payment->first_name,
                'last_name' => $payment->last_name,
                'user_email' => $payment->email,
                'product_name' => $item['name'],
                'product_id' => $item['id'],
                'order_item_id' => $item['order_item_id'],
                'discount_codes' => $payment->discounts,
                'order_discounts' => $item['discount'],
                'order_subtotal' => $payment->subtotal,
                'order_total' => $payment->total,
                'order_tax' => $payment->tax,
                'payment_method' => $payment->gateway,
                'status' => $payment->status,
            ];
        }

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedDiscount = !empty($flowDetails->selectedDiscount) ? $flowDetails->selectedDiscount : [];

        return ['triggered_entity' => 'EDD', 'triggered_entity_id' => 2, 'data' => $final_data, 'flows' => $flows];
    }

    public static function eddHandleOrderRefunded($order_id)
    {
        $flows = Flow::exists('EDD', 3);
        if (!$flows) {
            return;
        }

        $order_detail   = edd_get_payment($order_id);
        $total_discount = 0;

        if (empty($order_detail)) {
            return;
        }

        $payment_id = $order_detail->ID;
        $user_id    = edd_get_payment_user_id($payment_id);

        if (!$user_id) {
            $user_id = wp_get_current_user()->ID;
        }

        $userInfo = static::eddGetUserInfo($user_id);

        $payment_info = [
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
            'discount_codes'  => $order_detail->discounts,
            'order_discounts' => $total_discount,
            'order_subtotal'  => $order_detail->subtotal,
            'order_total'     => $order_detail->total,
            'order_tax'       => $order_detail->tax,
            'payment_method'  => $order_detail->gateway,
        ];

        return ['triggered_entity' => 'EDD', 'triggered_entity_id' => 3, 'data' => $payment_info, 'flows' => $flows];
    }

    public static function eddGetUserInfo($user_id)
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

    public static function essentialBlocksHandler(...$args)
    {
        if ($flows = Flow::exists('EssentialBlocks', current_action())) {

            foreach ($flows as $flow) {
                $flowDetails = json_decode($flow->flow_details);
                if (!isset($flowDetails->primaryKey)) {
                    continue;
                }

                $primaryKeyValue = Helper::extractValueFromPath($args, $flowDetails->primaryKey->key);
                if ($flowDetails->primaryKey->value === $primaryKeyValue) {
                    $fieldKeys      = [];
                    $formatedData   = [];

                    if ($flowDetails->body->data && is_array($flowDetails->body->data)) {
                        $fieldKeys = array_map(function ($field) use ($args) {
                            return $field->key;
                        }, $flowDetails->body->data);
                    } elseif (isset($flowDetails->field_map) && is_array($flowDetails->field_map)) {
                        $fieldKeys = array_map(function ($field) use ($args) {
                            return $field->formField;
                        }, $flowDetails->field_map);
                    }

                    foreach ($fieldKeys as $key) {
                        $formatedData[$key] = Helper::extractValueFromPath($args, $key);
                    }

                    $execData = ['triggered_entity' => 'EssentialBlocks', 'triggered_entity_id' => current_action(), 'data' => $formatedData, 'flows' => array($flow)];
                }
            }
            return $execData;
        }

        return;
    }

    public static function evfHandleSubmission($entry_id, $fields, $entry, $form_id, $form_data)
    {
        $flows = Flow::exists('EVF', $form_id);

        if (!$flows) {
            return;
        }

        $processedEntry = self::evfProcessValues($entry, $fields, $form_data);
        return ['triggered_entity' => 'EVF', 'triggered_entity_id' => 3, 'data' => $processedEntry, 'flows' => $flows];
    }

    private static function evfFieldType($type)
    {
        switch ($type) {
            case 'first-name':
            case 'last-name':
            case 'range-slider':
            case 'payment-quantity':
            case 'payment-total':
            case 'rating':
                return 'text';
            case 'phone':
                return 'tel';
            case 'privacy-policy':
            case 'payment-checkbox':
            case 'payment-multiple':
                return 'checkbox';
            case 'payment-single':
                return 'radio';
            case 'image-upload':
            case 'file-upload':
            case 'signature':
                return 'file';

            default:
                return $type;
        }
    }

    public static function evfProcessValues($entry, $fields, $form_data)
    {
        $processedValues = [];

        foreach ($fields as $index => $field) {
            $methodName = 'process' . str_replace(' ', '', ucwords(str_replace('-', ' ', self::evfFieldType($field['type'])))) . 'FieldValue';
            if (method_exists(new self, $methodName)) {
                $processedValues =  array_merge($processedValues, call_user_func_array([new self, $methodName], [$index, $field, $form_data]));
            } else {
                $processedValues["$index"] =   $entry['form_fields'][$index];
            }
        }

        return $processedValues;
    }

    public static function ffHandleSubmit($entryId, $formData, $form)
    {
        $form_id = $form->id;
        if (!empty($form_id) && $flows = Flow::exists('FF', $form_id)) {
            foreach ($formData as $primaryFld => $primaryFldValue) {
                if ($primaryFld === 'repeater_field') {
                    foreach ($primaryFldValue as $secondaryFld => $secondaryFldValue) {
                        foreach ($secondaryFldValue as $tertiaryFld => $tertiaryFldValue) {
                            $formData["$primaryFld:$secondaryFld-$tertiaryFld"] = $tertiaryFldValue;
                        }
                    }
                }
                if (is_array($primaryFldValue) && array_keys($primaryFldValue) !== range(0, count($primaryFldValue) - 1)) {
                    foreach ($primaryFldValue as $secondaryFld => $secondaryFldValue) {
                        $formData["$primaryFld:$secondaryFld"] = $secondaryFldValue;
                    }
                }
            }

            if (isset($form->form_fields) && isset(json_decode($form->form_fields)->fields)) {
                $formFields = json_decode($form->form_fields)->fields;
                foreach ($formFields as $fieldInfo) {
                    $attributes = $fieldInfo->attributes;
                    $type = isset($attributes->type) ? $attributes->type : $fieldInfo->element;
                    if ($type === 'file') {
                        $formData[$attributes->name] = Common::filePath($formData[$attributes->name]);
                    }
                    if (property_exists($fieldInfo, 'element') && $fieldInfo->element === 'input_date') {
                        $dateTimeHelper = new DateTimeHelper();
                        $currentDateFormat = $fieldInfo->settings->date_format;
                        $formData[$attributes->name] = $dateTimeHelper->getFormated($formData[$attributes->name], $currentDateFormat, wp_timezone(), 'Y-m-d\TH:i:sP', null);
                    }
                }
            }

            return ['triggered_entity' => 'FF', 'triggered_entity_id' => $form_id, 'data' => $formData, 'flows' => $flows];
        }
    }

    protected static function fluentcrmFlowFilter($flows, $key, $value)
    {
        $filteredFlows = [];
        if (is_array($flows) || is_object($flows)) {
            foreach ($flows as $flow) {
                if (is_string($flow->flow_details)) {
                    $flow->flow_details = json_decode($flow->flow_details);
                }
                if (!isset($flow->flow_details->$key) || $flow->flow_details->$key === 'any' || in_array($flow->flow_details->$key, $value) || $flow->flow_details->$key === '') {
                    $filteredFlows[] = $flow;
                }
            }
        }
        return $filteredFlows;
    }
    public static function fluentcrmGetContactData($email)
    {
        $contactApi     = \FluentCrmApi('contacts');
        $contact        = $contactApi->getContact($email);
        $customFields   = $contact->custom_fields();

        $data = [
            "prefix" => $contact->prefix,
            "first_name" => $contact->first_name,
            "last_name" => $contact->last_name,
            "full_name" => $contact->full_name,
            "email" => $contact->email,
            "timezone" => $contact->timezone,
            "address_line_1" => $contact->address_line_1,
            "address_line_2" => $contact->address_line_2,
            "city" => $contact->city,
            "state" => $contact->state,
            "postal_code" => $contact->postal_code,
            "country" => $contact->country,
            "ip" => $contact->ip,
            "phone" => $contact->phone,
            "source" => $contact->source,
            "date_of_birth" => $contact->date_of_birth,
        ];

        if (!empty($customFields)) {
            foreach ($customFields as $key => $value) {
                $data[$key] = $value;
            }
        }

        $lists = $contact->lists;
        $fluentCrmLists = [];
        foreach ($lists as $list) {
            $fluentCrmLists[] = (object) [
                'list_id' => $list->id,
                'list_title' => $list->title
            ];
        }

        $data['tags'] = implode(', ', array_column($contact->tags->toArray() ?? [], 'title'));
        $data['lists'] = $fluentCrmLists;
        return $data;
    }

    public static function fluentcrmHandleAddTag($tag_ids, $subscriber)
    {
        $flows = Flow::exists('FluentCrm', 'fluentcrm-1');
        $flows = self::fluentcrmFlowFilter($flows, 'selectedTag', $tag_ids);

        if (!$flows) {
            return;
        }

        $email = $subscriber->email;
        $data = ['tag_ids' => $tag_ids];
        $dataContact = self::fluentcrmGetContactData($email);
        $data = $data + $dataContact;

        return ['triggered_entity' => 'FluentCrm', 'triggered_entity_id' => 'fluentcrm-1', 'data' => $data, 'flows' => $flows];
    }

    public static function fluentcrmHandleRemoveTag($tag_ids, $subscriber)
    {
        $flows = Flow::exists('FluentCrm', 'fluentcrm-2');
        $flows = self::fluentcrmFlowFilter($flows, 'selectedTag', $tag_ids);

        if (!$flows) {
            return;
        }

        $email = $subscriber->email;
        $data = ['removed_tag_ids' => $tag_ids];
        $dataContact = self::fluentcrmGetContactData($email);
        $data = $data + $dataContact;

        return ['triggered_entity' => 'FluentCrm', 'triggered_entity_id' => 'fluentcrm-2', 'data' => $data, 'flows' => $flows];
    }

    public static function fluentcrmHandleAddList($list_ids, $subscriber)
    {
        $flows = Flow::exists('FluentCrm', 'fluentcrm-3');
        $flows = self::fluentcrmFlowFilter($flows, 'selectedList', $list_ids);

        if (!$flows) {
            return;
        }

        $email = $subscriber->email;
        $data = ['list_ids' => $list_ids];
        $dataContact = self::fluentcrmGetContactData($email);
        $data = $data + $dataContact;

        return ['triggered_entity' => 'FluentCrm', 'triggered_entity_id' => 'fluentcrm-3', 'data' => $data, 'flows' => $flows];
    }

    public static function fluentcrmHandleRemoveList($list_ids, $subscriber)
    {
        $flows = Flow::exists('FluentCrm', 'fluentcrm-4');
        $flows = self::fluentcrmFlowFilter($flows, 'selectedList', $list_ids);

        if (!$flows) {
            return;
        }

        $email = $subscriber->email;
        $data = ['remove_list_ids' => $list_ids];
        $dataContact = self::fluentcrmGetContactData($email);
        $data = $data + $dataContact;

        return ['triggered_entity' => 'FluentCrm', 'triggered_entity_id' => 'fluentcrm-4', 'data' => $data, 'flows' => $flows];
    }

    public static function fluentcrmHandleContactCreate($subscriber)
    {
        $flows = Flow::exists('FluentCrm', 'fluentcrm-6');
        if (!$flows) {
            return;
        }

        $email  = $subscriber->email;
        $data   = self::fluentcrmGetContactData($email);

        return ['triggered_entity' => 'FluentCrm', 'triggered_entity_id' => 'fluentcrm-6', 'data' => $data, 'flows' => $flows];
    }

    public static function fluentcrmHandleChangeStatus($subscriber, $old_status)
    {
        $newStatus = [$subscriber->status];

        $flows = Flow::exists('FluentCrm', 'fluentcrm-5');
        $flows = self::fluentcrmFlowFilter($flows, 'selectedStatus', $newStatus);

        $email = $subscriber->email;

        $data = [
            'old_status' => $old_status,
            'new_status' => $newStatus,
        ];

        $dataContact = self::fluentcrmGetContactData($email);
        $data = $data + $dataContact;

        return ['triggered_entity' => 'FluentCrm', 'triggered_entity_id' => 'fluentcrm-5', 'data' => $data, 'flows' => $flows];
    }

    public static function handleFormcraftSubmit($template, $meta, $content, $integrations)
    {
        $form_id = $template['Form ID'];
        $flows = Flow::exists('FormCraft', $form_id);

        if (!$flows) {
            return;
        }

        $finalData = [];
        if (!empty($content)) {
            foreach ($content as $value) {
                if ($value['type'] === 'fileupload') {
                    $finalData[$value['identifier']] = $value['url'][0];
                } else {
                    $finalData[$value['identifier']] = $value['value'];
                }
            }
        }

        return ['triggered_entity' => 'FormCraft', 'triggered_entity_id' => $form_id, 'data' => $finalData, 'flows' => $flows];
    }

    public static function handleForminatorSubmit($entry, $form_id, $form_data)
    {
        $post_id = url_to_postid($_SERVER['HTTP_REFERER']);

        if (!empty($form_id) && $flows = Flow::exists('Forminator', $form_id)) {
            $data = [];
            if ($post_id) {
                $data['post_id'] = $post_id;
            }
            foreach ($form_data as $fldDetail) {
                if (is_array($fldDetail['value'])) {
                    if (array_key_exists('file', $fldDetail['value'])) {
                        $data[$fldDetail['name']] = [$fldDetail['value']['file']['file_path']];
                    } elseif (explode("-", $fldDetail['name'])[0] == 'name') {
                        if ($fldDetail['name']) {
                            $last_dash_position = strrpos($fldDetail['name'], "-");
                            $index = substr($fldDetail['name'], $last_dash_position + 1);
                        }
                        foreach ($fldDetail['value'] as $nameKey => $nameVal) {
                            $data[$nameKey . '-' . $index] = $nameVal;
                        }
                    } elseif (explode("-", $fldDetail['name'])[0] == 'address') {
                        if ($fldDetail['name']) {
                            $last_dash_position = strrpos($fldDetail['name'], "-");
                            $index = substr($fldDetail['name'], $last_dash_position + 1);
                        }
                        foreach ($fldDetail['value'] as $nameKey => $nameVal) {
                            $data[$nameKey . '-' . $index] = $nameVal;
                        }
                    } else {
                        $val = $fldDetail['value'];
                        if (array_key_exists('ampm', $val)) {
                            $time = $val['hours'] . ':' . $val['minutes'] . ' ' . $val['ampm'];
                            $data[$fldDetail['name']] = $time;
                        } elseif (array_key_exists('year', $val)) {
                            $date = $val['year'] . '-' . $val['month'] . '-' . $val['day'];
                            $data[$fldDetail['name']] = $date;
                        } elseif (array_key_exists('formatting_result', $val)) {
                            $data[$fldDetail['name']] = $fldDetail['value']['formatting_result'];
                        } else {
                            $data[$fldDetail['name']] = $fldDetail['value'];
                        }
                    }
                } else {
                    if (self::ForminatorIsValidDate($fldDetail['value'])) {
                        $dateTmp = new DateTime($fldDetail['value']);
                        $dateFinal = date_format($dateTmp, 'Y-m-d');
                        $data[$fldDetail['name']] = $dateFinal;
                    } else {
                        $data[$fldDetail['name']] = $fldDetail['value'];
                    }
                }
            }

            return ['triggered_entity' => 'Forminator', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
        }
    }

    public static function ForminatorIsValidDate($date, $format = 'd/m/Y')
    {
        $dateTime = DateTime::createFromFormat($format, $date);
        return $dateTime && $dateTime->format($format) === $date;
    }

    public static function gamipressHandleUserEarnRank($user_id, $new_rank, $old_rank, $admin_id, $achievement_id)
    {
        $flows = Flow::exists('GamiPress', 1);

        if (!$flows) {
            return;
        }
        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
                $flowDetails = $flow->flow_details;
            }
        }

        $userData = self::gamipressGetUserInfo($user_id);

        if ($flowDetails->selectedRank === $new_rank->post_name) {
            $newRankData = [
                'rank_type' => $new_rank->post_type,
                'rank' => $new_rank->post_name,
            ];

            $data = array_merge($userData, $newRankData);

            return ['triggered_entity' => 'GamiPress', 'triggered_entity_id' => 1, 'data' => $data, 'flows' => $flows];
        }
    }

    public static function gamipressGetUserInfo($user_id)
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
                'user_url' => $userData->user_url,
                'display_name' => $userData->display_name,
            ];
        }
        return $user;
    }

    public static function gamipressHandleAwardAchievement($user_id, $achievement_id, $trigger, $site_id, $args)
    {
        $flows = Flow::exists('GamiPress', 2);
        if (!$flows) {
            return;
        }

        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
                $flowDetails = $flow->flow_details;
            }
        }

        global $wpdb;
        $awards = $wpdb->get_results(
            "SELECT ID, post_name, post_title, post_type FROM wp_posts where id = {$achievement_id}"
        );

        $userData = self::gamipressGetUserInfo($user_id);
        $awardData = [
            'achievement_type' => $awards[0]->post_type,
            'award' => $awards[0]->post_name,
        ];
        $data = array_merge($userData, $awardData);

        if ($flowDetails->selectedAward === $awards[0]->post_name) {
            return ['triggered_entity' => 'GamiPress', 'triggered_entity_id' => 2, 'data' => $data, 'flows' => $flows];
        }
    }

    public static function gamipressHandleGainAchievementType($user_id, $achievement_id, $trigger, $site_id, $args)
    {
        $flows = Flow::exists('GamiPress', 3);
        if (!$flows) {
            return;
        }
        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
                $flowDetails = $flow->flow_details;
            }
        }

        $postData = get_post($achievement_id);

        $data = [
            'post_id' => $achievement_id,
            'post_title' => $postData->post_title,
            'post_url' => get_permalink($achievement_id),
            'post_type' => $postData->post_type,
            'post_author_id' => $postData->post_author,
            // 'post_author_email' => $postData->post_author_email,
            'post_content' => $postData->post_content,
            'post_parent_id' => $postData->post_parent,
        ];

        if ($flowDetails->selectedAchievementType === $postData->post_type || $flowDetails->selectedAchievementType === 'any-achievement') {
            return ['triggered_entity' => 'GamiPress', 'triggered_entity_id' => 3, 'data' => $data, 'flows' => $flows];
        }
    }

    public static function gamipressHandleRevokeAchieve($user_id, $achievement_id, $earning_id)
    {
        $postData = get_post($achievement_id);
        $expectedData = get_post($postData->post_parent);

        $data = [
            'post_id' => $achievement_id,
            'post_title' => !empty($expectedData->post_title) ? $expectedData->post_title : '',
            'post_url' => get_permalink($achievement_id),
            'post_type' => isset($expectedData->post_type),
            'post_author_id' => isset($expectedData->post_author),
            // 'post_author_email' => $postData->post_author_email,
            'post_content' => isset($expectedData->post_content),
            'post_parent_id' => isset($expectedData->post_parent),
        ];

        for ($i = 4; $i <= 5; $i++) {
            if ($i == 4) {
                $flows = Flow::exists('GamiPress', $i);
                Flow::execute('GamiPress', $i, $data, $flows);
            }
            if ($i == 5) {
                $flows = Flow::exists('GamiPress', $i);
                foreach ($flows as $flow) {
                    if (is_string($flow->flow_details)) {
                        $flow->flow_details = json_decode($flow->flow_details);
                        $flowDetails = $flow->flow_details;
                    }
                }
                if ($flowDetails->selectedAchievementType === $expectedData->post_type || $flowDetails->selectedAchievementType === 'any-achievement') {
                    Flow::execute('GamiPress', $i, $data, $flows);
                }
            }
        }

        return;
    }

    public static function gamipressHandleEarnPoints($user_id, $new_points, $total_points, $admin_id, $achievement_id, $points_type, $reason, $log_type)
    {
        $flows = Flow::exists('GamiPress', 6);
        if (!$flows) {
            return;
        }

        $userData = self::gamipressGetUserInfo($user_id);
        unset($userData['user_url']);

        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
                $flowDetails = $flow->flow_details;
            }
        }
        $pointData = [
            'total_points' => $total_points,
            'new_points' => $new_points,
            'points_type' => $points_type,
        ];
        $data = array_merge($userData, $pointData);
        if ($flowDetails->selectedPoint === (string)$total_points || $flowDetails->selectedPoint === '') {
            return ['triggered_entity' => 'GamiPress', 'triggered_entity_id' => 6, 'data' => $data, 'flows' => $flows];
        }
    }

    public static function gformAfterSubmission($entry, $form)
    {
        $form_id = $form['id'];
        if (!empty($form_id) && $flows = Flow::exists('GF', $form_id)) {
            $upDir = wp_upload_dir();
            foreach ($form['fields'] as $key => $value) {
                if ($value->type === 'fileupload' && isset($entry[$value->id])) {
                    if ($value->multipleFiles === false) {
                        $entry[$value->id] = Common::filePath($entry[$value->id]);
                    } else {
                        $entry[$value->id] = Common::filePath(json_decode($entry[$value->id], true));
                    }
                }
                if ($value->type === 'checkbox' && is_array($value->inputs)) {
                    foreach ($value->inputs as $input) {
                        if (isset($entry[$input['id']])) {
                            $entry[$value->id][] = $entry[$input['id']];
                        }
                    }
                }
            }
            $finalData = $entry + ['title' => $form['title']];
            return ['triggered_entity' => 'GF', 'triggered_entity_id' => $form_id, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function giveHandleUserDonation($payment_id, $status, $old_status)
    {
        $flows = Flow::exists('GiveWp', 1);
        if (!$flows) {
            return;
        }

        if ('publish' !== $status) {
            return;
        }

        $payment = new \Give_Payment($payment_id);

        if (empty($payment)) {
            return;
        }
        $payment_exists = $payment->ID;
        if (empty($payment_exists)) {
            return;
        }

        $give_form_id = $payment->form_id;
        $user_id = $payment->user_id;

        if (0 === $user_id) {
            return;
        }

        $finalData = json_decode(wp_json_encode($payment), true);

        $donarUserInfo = give_get_payment_meta_user_info($payment_id);
        if ($donarUserInfo) {
            $finalData['title'] = $donarUserInfo['title'];
            $finalData['first_name'] = $donarUserInfo['first_name'];
            $finalData['last_name'] = $donarUserInfo['last_name'];
            $finalData['email'] = $donarUserInfo['email'];
            $finalData['address1'] = $donarUserInfo['address']['line1'];
            $finalData['address2'] = $donarUserInfo['address']['line2'];
            $finalData['city'] = $donarUserInfo['address']['city'];
            $finalData['state'] = $donarUserInfo['address']['state'];
            $finalData['zip'] = $donarUserInfo['address']['zip'];
            $finalData['country'] = $donarUserInfo['address']['country'];
            $finalData['donar_id'] = $donarUserInfo['donor_id'];
        }

        $finalData['give_form_id'] = $give_form_id;
        $finalData['give_form_title'] = $payment->form_title;
        $finalData['currency'] = $payment->currency;
        $finalData['give_price_id'] = $payment->price_id;
        $finalData['price'] = $payment->total;

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedDonationForm = !empty($flowDetails->selectedDonationForm) ? $flowDetails->selectedDonationForm : [];
        if ($flows && $give_form_id === $selectedDonationForm || $selectedDonationForm === 'any') {
            return ['triggered_entity' => 'GiveWp', 'triggered_entity_id' => 1, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function giveWpGetUserInfo($user_id)
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

    public static function giveHandleSubscriptionDonationCancel($subscription_id, $subscription)
    {
        $flows = Flow::exists('GiveWp', 2);
        if (!$flows) {
            return;
        }

        $give_form_id = $subscription->form_id;
        $amount = $subscription->recurring_amount;
        $donor = $subscription->donor;
        $user_id = $donor->user_id;
        $getUserData = static::giveWpGetUserInfo($user_id);
        $finalData = [
            'subscription_id' => $subscription_id,
            'give_form_id' => $give_form_id,
            'amount' => $amount,
            'donor' => $donor,
            'user_id' => $user_id,
            'first_name' => $getUserData['first_name'],
            'last_name' => $getUserData['last_name'],
            'user_email' => $getUserData['email'],
            'nickname' => $getUserData['nickname'],
            'avatar_url' => $getUserData['avatar_url'],
        ];

        if (0 === $user_id) {
            return;
        }

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedRecurringDonationForm = !empty($flowDetails->selectedRecurringDonationForm) ? $flowDetails->selectedRecurringDonationForm : '';
        if ($flows && !empty($selectedRecurringDonationForm) && $give_form_id === $selectedRecurringDonationForm) {
            return ['triggered_entity' => 'GiveWp', 'triggered_entity_id' => 2, 'data' => $finalData, 'flows' => $flows];
        }
    }

    public static function giveHandleRecurringDonation($status, $row_id, $data, $where)
    {
        $flows = Flow::exists('GiveWp', 3);
        if (!$flows) {
            return;
        }

        $subscription = new \Give_Subscription($row_id);
        $recurring_amount = $subscription->recurring_amount;
        $give_form_id = $subscription->form_id;

        $total_payment = $subscription->get_total_payments();
        $donor = $subscription->donor;
        $user_id = $donor->user_id;

        if (0 === absint($user_id)) {
            return;
        }

        if ($total_payment > 1 && 'active' === (string) $data['status']) {
            $user = static::giveWpGetUserInfo($user_id);
            $finalData = [
                'give_form_id' => $give_form_id,
                'recurring_amount' => $recurring_amount,
                'total_payment' => $total_payment,
                'donor' => $donor,
                'user_id' => $user_id,
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'user_email' => $user['user_email'],
                'nickname' => $user['nickname'],
                'avatar_url' => $user['avatar_url'],
            ];
        }

        return ['triggered_entity' => 'GiveWp', 'triggered_entity_id' => 3, 'data' => $finalData, 'flows' => $flows];
    }

    public static function groundhoggHandleSubmit($a, $fieldValues)
    {
        $form_id    = 1;
        $flows = Flow::exists('Groundhogg', $form_id);
        if (!$flows) {
            return;
        }

        global $wp_rest_server;
        $request    = $wp_rest_server->get_raw_data();
        $data       = json_decode($request);
        $meta       = $data->meta;

        $fieldValues['primary_phone']   = $meta->primary_phone;
        $fieldValues['mobile_phone']    = $meta->mobile_phone;

        if (isset($data->tags)) {
            $fieldValues['tags'] = self::groundhoggSetTagNames($data->tags);
        }


        $data = $fieldValues;
        return ['triggered_entity' => 'Groundhogg', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }

    private static function groundhoggSetTagNames($tag_ids)
    {
        $tags       = new Tags();
        $tag_list   = [];
        foreach ($tag_ids as $tag_id) {
            $tag_list[] = $tags->get_tag($tag_id)->tag_name;
        }
        return implode(',', $tag_list);
    }

    public static function groundhoggTagApplied($a, $b)
    {
        $data           = $a['data'];
        $form_id        = 2;
        $flows          = Flow::exists('Groundhogg', $form_id);

        if (!$flows) {
            return;
        }

        $getSelected    = $flows[0]->flow_details;
        $enCode         = json_decode($getSelected);

        if (isset($a['tags'])) {
            $data['tags'] = self::groundhoggSetTagNames($a['tags']);
        }

        if ($enCode->selectedTag == $b || $enCode->selectedTag == 'any') {
            return ['triggered_entity' => 'Groundhogg', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
        }

        return;
    }

    public static function groundhoggTagRemove($a, $b)
    {
        $data           = $a['data'];
        $form_id        = 3;
        $flows          = Flow::exists('Groundhogg', $form_id);

        if (!$flows) {
            return;
        }

        $getSelected    = $flows[0]->flow_details;
        $enCode         = json_decode($getSelected);

        if (isset($a['tags'])) {
            $data['tags'] = self::groundhoggSetTagNames($a['tags']);
        }

        if ($enCode->selectedTag == $b || $enCode->selectedTag == 'any') {
            return ['triggered_entity' => 'Groundhogg', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
        }

        return;
    }

    public static function happySaveImage($base64_img, $title)
    {
        // Upload dir.
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/bihappy';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0700);
        }
        $upload_path = $upload_dir;

        $img = str_replace('data:image/png;base64,', '', $base64_img);
        $img = str_replace(' ', '+', $img);
        $decoded = base64_decode($img);
        $filename = $title . '.png';
        $file_type = 'image/png';
        $hashed_filename = md5($filename . microtime()) . '_' . $filename;

        //Save the image in the uploads directory.
        $upload_file = file_put_contents($upload_path . '/' . $hashed_filename, $decoded);
        if ($upload_file) {
            $path = $upload_path . '/' . $hashed_filename;
            return $path;
        }
        return $base64_img;
    }

    public static function happyGetPath($val)
    {
        $img = maybe_unserialize($val);
        $hash_ids = array_filter(array_values($img));
        $attachments = happyforms_get_attachment_controller()->get([
            'hash_id' => $hash_ids,
        ]);

        $attachment_ids = wp_list_pluck($attachments, 'ID');
        $links = array_map('wp_get_attachment_url', $attachment_ids);
        $value = implode(', ', $links);
        return $value;
    }

    public static function handleHappySubmit($submission, $form, $a)
    {
        $post_id = url_to_postid($_SERVER['HTTP_REFERER']);
        $form_id = $form['ID'];

        if (!empty($form_id) && $flows = Flow::exists('Happy', $form_id)) {
            $data = [];
            if ($post_id) {
                $data['post_id'] = $post_id;
            }
            $form_data = $submission;

            foreach ($form_data as $key => $val) {
                if (str_contains($key, 'signature')) {
                    $baseUrl = maybe_unserialize($val)['signature_raster_data'];
                    $path = self::happySaveImage($baseUrl, 'sign');
                    $form_data[$key] = $path;
                } elseif (str_contains($key, 'date')) {
                    if (strtotime($val)) {
                        $dateTmp = new DateTime($val);
                        $dateFinal = date_format($dateTmp, 'Y-m-d');
                        $form_data[$key] = $dateFinal;
                    }
                } elseif (str_contains($key, 'attachment')) {
                    $image = self::happyGetPath($val);
                    $form_data[$key] = Common::filePath($image);
                }
            }
            return ['triggered_entity' => 'Happy', 'triggered_entity_id' => $form_id, 'data' => $form_data, 'flows' => $flows];
        }

        return;
    }

    public static function jetEnginePostMetaData($meta_id, $post_id, $meta_key, $meta_value)
    {
        $postCreateFlow = Flow::exists('JetEngine', 1);
        if (!$postCreateFlow) {
            return;
        }

        $postData = get_post($post_id);
        $finalData = (array)$postData + ['meta_key' => $meta_key, 'meta_value' => $meta_value];
        $postData = get_post($post_id);
        $user_id = get_current_user_id();
        $postType = $postData->post_type;

        $info = isset($postCreateFlow[0]->flow_details) ? json_decode($postCreateFlow[0]->flow_details) : '';
        $selectedPostType = !empty($info->selectedPostType) ? $info->selectedPostType : 'any-post-type';
        $selectedMetaKey = !empty($info->selectedMetaKey) ? $info->selectedMetaKey : '';
        $selectedMetaValue = !empty($info->selectedMetaValue) ? $info->selectedMetaValue : '';

        $isPostTypeMatched = $selectedPostType ? $selectedPostType === $postType : true;
        $isMetaKeyMatched = $selectedMetaKey ? $selectedMetaKey === $meta_key : true;
        $isMetaValueMatched = $selectedMetaValue ? $selectedMetaValue === $meta_value : true;
        $isEditable = $user_id && $postCreateFlow && !($meta_key === '_edit_lock');
        if (1 && $isPostTypeMatched && $isMetaKeyMatched && $isEditable) {
            return ['triggered_entity' => 'JetEngine', 'triggered_entity_id' => 1, 'data' => $finalData, 'flows' => $postCreateFlow];
        }

        return;
    }

    public static function jetEnginePostMetaValueCheck($meta_id, $post_id, $meta_key, $meta_value)
    {
        $postCreateFlow = Flow::exists('JetEngine', 2);
        if (!$postCreateFlow) {
            return;
        }

        $postData = get_post($post_id);
        $finalData = (array)$postData + ['meta_key' => $meta_key, 'meta_value' => $meta_value];
        $postData = get_post($post_id);
        $user_id = get_current_user_id();
        $postType = $postData->post_type;

        $info = isset($postCreateFlow[0]->flow_details) ? json_decode($postCreateFlow[0]->flow_details) : '';
        $selectedPostType = !empty($info->selectedPostType) ? $info->selectedPostType : 'any-post-type';
        $selectedMetaKey = !empty($info->selectedMetaKey) ? $info->selectedMetaKey : '';
        $selectedMetaValue = !empty($info->selectedMetaValue) ? $info->selectedMetaValue : '';

        $isPostTypeMatched = $selectedPostType ? $selectedPostType === $postType : true;
        $isMetaKeyMatched = $selectedMetaKey ? $selectedMetaKey === $meta_key : true;
        $isMetaValueMatched = $selectedMetaValue ? $selectedMetaValue === $meta_value : true;
        $isEditable = $user_id && $postCreateFlow && !($meta_key === '_edit_lock');
        if (2 && $isPostTypeMatched && $isMetaKeyMatched && $isMetaValueMatched && $isEditable) {
            return ['triggered_entity' => 'JetEngine', 'triggered_entity_id' => 2, 'data' => $finalData, 'flows' => $postCreateFlow];
        }

        return;
    }
}
