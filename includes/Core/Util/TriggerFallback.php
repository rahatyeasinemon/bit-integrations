<?php

namespace BitCode\FI\Core\Util;

use BitCode\FI\Flow\Flow;

final class TriggerFallback
{
    public static function handle_formidable_submit($conf_method, $form, $form_option, $entry_id, $extra_args)
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

    public static function academyLmsHandleCourseEnroll($course_id, $enrollment_id)
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

    public static function academyLmsHandleQuizAttempt($attempt)
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

    public static function academyLmsHandleQuizTarget($attempt)
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

    public static function academyLmsHandleLessonComplete($topic_type, $course_id, $topic_id, $user_id)
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

    public static function academyLmsHandleCourseComplete($course_id)
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

    public static function newAffiliateApproved($affiliate_id, $status, $old_status)
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

    public static function userBecomesAffiliate($affiliate_id, $status, $old_status)
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

    public static function affiliateMakesReferral($referral_id)
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

    public static function affiliatesReferralSpecificTypeRejected($referral_id, $new_status, $old_status)
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

    public static function affiliatesReferralSpecificTypePaid($referral_id, $new_status, $old_status)
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

    public static function BuddyBossHandleAcceptFriendRequest($id, $initiator_user_id, $friend_user_id, $friendship)
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

    public static function BuddyBossHandleSendsFriendRequest($id, $initiator_user_id, $friend_user_id, $friendship)
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

    public static function BuddyBossHandleCreateTopic($topic_id, $forum_id, $anonymous_data, $topic_author)
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

    public static function BuddyBossHandleJoinPublicGroup($group_id, $user_id)
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

    public static function BuddyBossHandleJoinPrivateGroup($user_id, $group_id)
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

    public static function BuddyBossHandleLeavesGroup($group_id, $user_id)
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

    public static function BuddyBossHandlePostGroupActivity($content, $user_id, $group_id, $activity_id)
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

    public static function BuddyBossHandleRepliesTopic($reply_id, $topic_id, $forum_id)
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

    public static function BuddyBossHandleRequestPrivateGroup($user_id, $admins, $group_id, $request_id)
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

    public static function BuddyBossHandleSendEmailInvites($user_id, $post)
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

    public static function BuddyBossHandleUpdateAvatar($item_id, $type, $avatar_data)
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

    public static function BuddyBossHandleUpdateProfile($user_id, $posted_field_ids, $errors, $old_values, $new_values)
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

    public static function BuddyBossHandleAccountActive($user_id, $key, $user)
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

    public static function BuddyBossHandleInviteeActiveAccount($user_id, $inviter_id, $post_id)
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

    public static function BuddyBossHandleInviteeRegisterAccount($user_id, $inviter_id, $post_id)
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
}
