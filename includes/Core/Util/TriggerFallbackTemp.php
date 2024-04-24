<?php

namespace BitCode\FI\Core\Util;

use DateTime;
use BitCode\FI\Flow\Flow;

final class TriggerFallbackTemp
{
    public static function studiocartNewOrderCreated($status, $order_data, $order_type = 'main')
    {
        $studiocartActions = [
            "newOrderCreated" => [
                "id" => 2,
                "title" => "New Order Created"
            ],
        ];

        $flows = Flow::exists('StudioCart', $studiocartActions['newOrderCreated']['id']);

        if (!$flows) {
            return;
        }

        $data = [];
        foreach ($order_data as $key => $field_value) {
            $data[$key] = $field_value;
        }

        return ['triggered_entity' => 'StudioCart', 'triggered_entity_id' => $studiocartActions['newOrderCreated']['id'], 'data' => $data, 'flows' => $flows];
    }

    public static function surecartPurchaseProduct($data)
    {
        if (!self::surecartPluginActive()) {
            wp_send_json_error(__('SureCart is not installed or activated', 'bit-integrations'));
        }
        $accountDetails = \SureCart\Models\Account::find();
        $product = \SureCart\Models\Product::find($data['product_id']);
        $finalData = self::SureCartDataProcess($data, $product, $accountDetails);
        $flows = Flow::exists('SureCart', 1);

        if (!$flows) {
            return;
        }
        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedProduct = !empty($flowDetails->selectedProduct) ? $flowDetails->selectedProduct : [];

        if ($flows && ($data['product_id'] == $selectedProduct || $selectedProduct === 'any')) {
            return ['triggered_entity' => 'SureCart', 'triggered_entity_id' => 1, 'data' => $finalData, 'flows' => $flows];
        }

        return;
    }

    public static function SureCartDataProcess($data, $product, $accountDetails)
    {
        $purchaseFinalData = self::surecartPurchaseDataProcess($data['id']);
        return [
            'store_name' => $accountDetails['name'],
            'store_url' => $accountDetails['url'],
            'product_name' => $product['name'],
            'product_id' => $product['id'],
            'product_description' => $product['description'],
            'product_thumb_id' => $purchaseFinalData['product_thumb_id'],
            'product_thumb' => $purchaseFinalData['product_thumb'],
            'product_price' => $product->price,
            'product_price_id' => $purchaseFinalData['product_price_id'],
            'product_quantity' => $data['quantity'],
            'max_price_amount' => $product['metrics']->max_price_amount,
            'min_price_amount' => $product['metrics']->min_price_amount,
            'order_id' => $purchaseFinalData['order_id'],
            'order_paid_amount' => $data['order_paid_amount'],
            'payment_currency' => $accountDetails['currency'],
            'payment_method' => $purchaseFinalData['payment_method'],
            'customer_id' => $data['customer_id'],
            'subscriptions_id' => $purchaseFinalData['subscriptions_id'],
            'order_number' => $purchaseFinalData['order_number'],
            'order_date' => $purchaseFinalData['order_date'],
            'order_status' => $purchaseFinalData['order_status'],
            'order_paid_amount' => $purchaseFinalData['order_paid_amount'],
            'order_subtotal' => $purchaseFinalData['order_subtotal'],
            'order_total' => $purchaseFinalData['order_total'],

        ];
    }

    public static function surecartPurchaseDataProcess($id)
    {
        $purchase_data = self::surecartPurchaseDetails($id);
        $price = self::surecartGetPrice($purchase_data);
        $chekout = $purchase_data->initial_order->checkout;
        $sanitizeData = [
            'product' => $purchase_data->product->name,
            'product_id' => $purchase_data->product->id,
            'product_thumb_id' => isset($purchase_data->product->image) ? $purchase_data->product->image : '',
            'product_thumb' => isset($purchase_data->product->image_url) ? $purchase_data->product->image_url : '',
            'product_price_id' => isset($price->id) ? $price->id : '',
            'order_id' => $purchase_data->initial_order->id,
            'subscription_id' => isset($purchase_data->subscription->id) ? $purchase_data->subscription->id : '',
            'order_number' => $purchase_data->initial_order->number,
            'order_date' => date(get_option('date_format', 'F j, Y'), $purchase_data->initial_order->created_at),
            'order_status' => $purchase_data->initial_order->status,
            'order_paid_amount' => self::surecartFormatAmount($chekout->charge->amount),
            'order_subtotal' => self::surecartFormatAmount($chekout->subtotal_amount),
            'order_total' => self::surecartFormatAmount($chekout->total_amount),
            'payment_method' => isset($chekout->payment_method->processor_type) ? $chekout->payment_method->processor_type : '',
        ];
        return $sanitizeData;
    }

    public static function surecartPurchaseDetails($id)
    {
        return \SureCart\Models\Purchase::with(['initial_order', 'order.checkout', 'checkout.shipping_address', 'checkout.payment_method', 'checkout.discount', 'discount.coupon', 'checkout.charge', 'product', 'product.downloads', 'download.media', 'license.activations', 'line_items', 'line_item.price', 'subscription'])->find($id);
    }

    public static function surecartGetPrice($purchase_data)
    {
        if (empty($purchase_data->line_items->data[0])) {
            return;
        }

        $line_item = $purchase_data->line_items->data[0];

        return $line_item->price;
    }

    public static function surecartFormatAmount($amount)
    {
        return $amount / 100;
    }

    public static function surecartPluginActive($option = null)
    {
        if (is_plugin_active('surecart/surecart.php')) {
            return $option === 'get_name' ? 'surecart/surecart.php' : true;
        }
        return false;
    }

    public static function surecartPurchaseRevoked($data)
    {
        $accountDetails = \SureCart\Models\Account::find();
        $finalData = [
            'store_name' => $accountDetails['name'],
            'store_url' => $accountDetails['url'],
            'purchase_id' => $data->id,
            'revoke_date' => $data->revoked_at,
            'customer_id' => $data->customer,
            'product_id' => $data->product->id,
            'product_description' => $data->product->description,
            'product_name' => $data->product->name,
            'product_image_id' => $data->product->image,
            'product_price' => ($data->product->prices->data[0]->full_amount) / 100,
            'product_currency' => $data->product->prices->data[0]->currency,

        ];

        $flows = Flow::exists('SureCart', 2);

        if (!$flows) {
            return;
        }

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedProduct = !empty($flowDetails->selectedProduct) ? $flowDetails->selectedProduct : [];

        if ($flows && ($data->product->id == $selectedProduct || $selectedProduct === 'any')) {
            return ['triggered_entity' => 'SureCart', 'triggered_entity_id' => 2, 'data' => $finalData, 'flows' => $flows];
        }

        return;
    }

    public static function surecartPurchaseUnrevoked($data)
    {
        $accountDetails = \SureCart\Models\Account::find();
        $finalData = [
            'store_name' => $accountDetails['name'],
            'store_url' => $accountDetails['url'],
            'purchase_id' => $data->id,
            'revoke_date' => $data->revoked_at,
            'customer_id' => $data->customer,
            'product_id' => $data->product->id,
            'product_description' => $data->product->description,
            'product_name' => $data->product->name,
            'product_image_id' => $data->product->image,
            'product_price' => ($data->product->prices->data[0]->full_amount) / 100,
            'product_currency' => $data->product->prices->data[0]->currency,
        ];

        $flows = Flow::exists('SureCart', 3);

        if (!$flows) {
            return;
        }

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedProduct = !empty($flowDetails->selectedProduct) ? $flowDetails->selectedProduct : [];

        if ($flows && ($data->product->id == $selectedProduct || $selectedProduct === 'any')) {
            return ['triggered_entity' => 'SureCart', 'triggered_entity_id' => 3, 'data' => $finalData, 'flows' => $flows];
        }

        return;
    }

    // main function was empty in the orginal file
    public static function handleThemifySubmit()
    {
        return;
    }

    public static function thriveApprenticeHandleCourseComplete($course_details, $user_details)
    {
        $flows = Flow::exists('ThriveApprentice', 1);
        if (!$flows) {
            return;
        }

        $userInfo = self::thriveApprenticeGetUserInfo($user_details['user_id']);

        $finalData = [
            'user_id' => $user_details['user_id'],
            'course_id' => $course_details['course_id'],
            'course_title' => $course_details['course_title'],
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedCourse = !empty($flowDetails->selectedCourse) ? $flowDetails->selectedCourse : [];

        if ($course_details['course_id'] == $selectedCourse || $selectedCourse === 'any') {
            return ['triggered_entity' => 'ThriveApprentice', 'triggered_entity_id' => 1, 'data' => $finalData, 'flows' => $flows];
        }

        return;
    }

    // main function was unavailable in the orginal file
    public static function thriveApprenticeHandleLessonComplete()
    {
        return;
    }

    public static function thriveApprenticeHandleModuleComplete($module_details, $user_details)
    {
        $flows = Flow::exists('ThriveApprentice', 3);
        if (!$flows) {
            return;
        }

        $userInfo = self::thriveApprenticeGetUserInfo($user_details['user_id']);

        $finalData = [
            'user_id' => $user_details['user_id'],
            'module_id' => $module_details['module_id'],
            'module_title' => $module_details['module_title'],
            'first_name' => $userInfo['first_name'],
            'last_name' => $userInfo['last_name'],
            'nickname' => $userInfo['nickname'],
            'avatar_url' => $userInfo['avatar_url'],
            'user_email' => $userInfo['user_email'],
        ];

        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedModule = !empty($flowDetails->selectedModule) ? $flowDetails->selectedModule : [];

        if ($module_details['module_id'] == $selectedModule || $selectedModule === 'any') {
            return ['triggered_entity' => 'ThriveApprentice', 'triggered_entity_id' => 3, 'data' => $finalData, 'flows' => $flows];
        }

        return;
    }

    public static function thriveApprenticeGetUserInfo($user_id)
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

    public static function TutorLmsHandleCourseEnroll($course_id, $enrollment_id)
    {
        $flows = Flow::exists('TutorLms', 1);
        $flows = self::TutorLmsFlowFilter($flows, 'selectedCourse', $course_id);
        if (!$flows) {
            return;
        }

        $author_id      = get_post_field('post_author', $course_id);
        $author_name    = get_the_author_meta('display_name', $author_id);
        $student_id     = get_post_field('post_author', $enrollment_id);
        $userData       = get_userdata($student_id);
        $result_student = [];

        if ($student_id && $userData) {
            $result_student = [
                'student_id'            => $student_id,
                'student_name'          => $userData->display_name,
                'student_first_name'    => $userData->user_firstname,
                'student_last_name'     => $userData->user_lastname,
                'student_email'         => $userData->user_email,
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

        return ['triggered_entity' => 'TutorLms', 'triggered_entity_id' => 1, 'data' => $result, 'flows' => $flows];
    }

    public static function TutorLmsHandleQuizAttempt($attempt_id)
    {
        $flows = Flow::exists('TutorLms', 2);

        $attempt = tutor_utils()->get_attempt($attempt_id);

        $quiz_id = $attempt->quiz_id;

        $flows = self::TutorLmsFlowFilter($flows, 'selectedQuiz', $quiz_id);
        if (!$flows) {
            return;
        }

        if ('tutor_quiz' !== get_post_type($quiz_id)) {
            return;
        }

        if ('attempt_ended' !== $attempt->attempt_status) {
            return;
        }

        $attempt_details = [];
        $attempt_info = [];

        foreach ($attempt as $key => $val) {
            if (is_array($val)) {
                $val = maybe_unserialize($val[0]);
            }
            $attempt_details[$key] = maybe_unserialize($val);
        }

        if (array_key_exists('attempt_info', $attempt_details)) {
            $attempt_info_tmp = $attempt_details['attempt_info'];
            unset($attempt_details['attempt_info']);

            foreach ($attempt_info_tmp as $key => $val) {
                $attempt_info[$key] = maybe_unserialize($val);
            }

            $attempt_details['passing_grade'] = $attempt_info['passing_grade'];
            $totalMark = $attempt_details['total_marks'];
            $earnMark = $attempt_details['earned_marks'];
            $passGrade = $attempt_details['passing_grade'];
            $mark = $totalMark * ($passGrade / 100);

            if ($earnMark >= $mark) {
                $attempt_details['result_status'] = 'Passed';
            } else {
                $attempt_details['result_status'] = 'Failed';
            }
        }

        $attempt_details['post_id'] = $attempt_id;

        return ['triggered_entity' => 'TutorLms', 'triggered_entity_id' => 2, 'data' => $attempt_details, 'flows' => $flows];
    }

    public static function TutorLmsHandleLessonComplete($lesson_id)
    {
        $flows = Flow::exists('TutorLms', 3);
        $flows = self::TutorLmsFlowFilter($flows, 'selectedLesson', $lesson_id);

        if (!$flows) {
            return;
        }
        $lessonPost = get_post($lesson_id);

        $lessonData = [];
        $lessonData = [
            'lesson_id' => $lessonPost->ID,
            'lesson_title' => $lessonPost->post_title,
            'lesson_description' => $lessonPost->post_content,
            'lesson_url' => $lessonPost->guid,
        ];

        $user = self::TutorLmsGetUserInfo(get_current_user_id());
        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $courseData = [];
        $topicPost = get_post($lessonPost->post_parent);
        $topicData = [
            'topic_id' => $topicPost->ID,
            'topic_title' => $topicPost->post_title,
            'topic_description' => $topicPost->post_content,
            'topic_url' => $topicPost->guid,
        ];
        $coursePost = get_post($topicPost->post_parent);
        $courseData = [
            'course_id' => $coursePost->ID,
            'course_name' => $coursePost->post_title,
            'course_description' => $coursePost->post_content,
            'course_url' => $coursePost->guid,
        ];

        $lessonDataFinal = $lessonData + $topicData + $courseData + $current_user;
        $lessonDataFinal['post_id'] = $lesson_id;

        return ['triggered_entity' => 'TutorLms', 'triggered_entity_id' => 3, 'data' => $lessonDataFinal, 'flows' => $flows];
    }

    public static function TutorLmsHandleCourseComplete($course_id)
    {
        $flows = Flow::exists('TutorLms', 4);
        $flows = self::TutorLmsFlowFilter($flows, 'selectedCourse', $course_id);

        if (!$flows) {
            return;
        }

        $coursePost = get_post($course_id);

        $courseData = [];
        $courseData = [
            'course_id' => $coursePost->ID,
            'course_title' => $coursePost->post_title,
            'course_url' => $coursePost->guid,
        ];

        $user = self::TutorLmsGetUserInfo(get_current_user_id());
        $current_user = [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_email' => $user['user_email'],
            'nickname' => $user['nickname'],
            'avatar_url' => $user['avatar_url'],
        ];

        $courseDataFinal = $courseData + $current_user;
        $courseDataFinal['post_id'] = $course_id;

        return ['triggered_entity' => 'TutorLms', 'triggered_entity_id' => 4, 'data' => $courseDataFinal, 'flows' => $flows];
    }

    public static function TutorLmsHandleQuizTarget($attempt_id)
    {
        $flows = Flow::exists('TutorLms', 5);

        $attempt = tutor_utils()->get_attempt($attempt_id);

        $quiz_id = $attempt->quiz_id;

        $flows = self::TutorLmsFlowFilter($flows, 'selectedQuiz', $quiz_id);
        if (!$flows) {
            return;
        }

        if ('tutor_quiz' !== get_post_type($quiz_id)) {
            return;
        }

        if ('attempt_ended' !== $attempt->attempt_status) {
            return;
        }

        $attempt_details = [];
        $attempt_info = [];

        foreach ($attempt as $key => $val) {
            if (is_array($val)) {
                $val = maybe_unserialize($val[0]);
            }
            $attempt_details[$key] = maybe_unserialize($val);
        }

        if (array_key_exists('attempt_info', $attempt_details)) {
            $attempt_info_tmp = $attempt_details['attempt_info'];
            unset($attempt_details['attempt_info']);

            foreach ($attempt_info_tmp as $key => $val) {
                $attempt_info[$key] = maybe_unserialize($val);
            }

            $attempt_details['passing_grade'] = $attempt_info['passing_grade'];
            $totalMark = $attempt_details['total_marks'];
            $earnMark = $attempt_details['earned_marks'];
            $passGrade = $attempt_details['passing_grade'];
            $mark = $totalMark * ($passGrade / 100);

            if ($earnMark >= $mark) {
                $attempt_details['result_status'] = 'Passed';
            } else {
                $attempt_details['result_status'] = 'Failed';
            }

            foreach ($flows as $flow) {
                $flow_details = $flow->flow_details;
                $reqPercent = $flow_details->requiredPercent;
                $mark = $totalMark * ($reqPercent / 100);
                $condition = $flow_details->selectedCondition;
                $achived = self::TutorLmsCheckedAchived($condition, $mark, $earnMark);
                $attempt_details['achived_status'] = $achived;

                $attempt_details['post_id'] = $attempt_id;

                Flow::execute('TutorLms', 5, $attempt_details, $flows = [$flow]);
            }
        }

        return;
    }

    protected static function TutorLmsFlowFilter($flows, $key, $value)
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

    public static function TutorLmsGetUserInfo($user_id)
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

    public static function TutorLmsCheckedAchived($condition, $mark, $earnMark)
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

    public static function ultimateMemberHandleUserSpecificRoleChange($user_id, $role, $old_roles)
    {
        $form_id = 'roleSpecificChange';
        $flows = Flow::exists('UltimateMember', $form_id);
        if (empty($flows)) {
            return;
        }
        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedRole = !empty($flowDetails->selectedRole) ? $flowDetails->selectedRole : [];
        $finalData = self::ultimateMemberGetUserInfo($user_id);
        $finalData['role'] = $role;

        if ($finalData && $role === $selectedRole) {
            return ['triggered_entity' => 'UltimateMember', 'triggered_entity_id' => $form_id, 'data' => $finalData, 'flows' => $flows];
        }

        return;
    }

    public static function ultimateMemberHandleUserRoleChange($user_id, $role, $old_roles)
    {
        $form_id = 'roleChange';
        $flows = Flow::exists('UltimateMember', $form_id);
        if (empty($flows)) {
            return;
        }
        $finalData = self::ultimateMemberGetUserInfo($user_id);
        $finalData['role'] = $role;

        if ($finalData) {
            return ['triggered_entity' => 'UltimateMember', 'triggered_entity_id' => $form_id, 'data' => $finalData, 'flows' => $flows];
        }

        return;
    }

    public static function ultimateMemberHandleUserRegisViaForm($user_id, $um_args)
    {
        $form_id = $um_args['form_id'];
        $flows = Flow::exists('UltimateMember', $form_id);

        if (empty($flows)) {
            return;
        }

        if (!empty($um_args['submitted'])) {
            return ['triggered_entity' => 'UltimateMember', 'triggered_entity_id' => $form_id, 'data' => $um_args['submitted'], 'flows' => $flows];
        }

        return;
    }

    public static function ultimateMemberHandleUserLogViaForm($um_args)
    {
        if (!isset($um_args['form_id']) || !function_exists('um_user')) {
            return;
        }
        $user_id = um_user('ID');
        $form_id = $um_args['form_id'];
        $flows = Flow::exists('UltimateMember', $form_id);
        if (empty($flows)) {
            return;
        }
        $finalData = self::ultimateMemberGetUserInfo($user_id);
        $finalData['username'] = $um_args['username'];

        if ($finalData) {
            return ['triggered_entity' => 'UltimateMember', 'triggered_entity_id' => $form_id, 'data' => $finalData, 'flows' => $flows];
        }

        return;
    }

    public static function ultimateMemberGetUserInfo($user_id)
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

    public static function handleWeformsSubmit($entry_id, $form_id, $page_id, $form_settings)
    {
        $flows = Flow::exists('WeForms', $form_id);

        if (!$flows) {
            return;
        }

        $dataAll = \weforms_get_entry_data($entry_id);

        foreach ($dataAll['fields'] as $key => $field) {
            if ($field['type'] === 'image_upload' || $field['type'] === 'file_upload') {
                $dataAll['data'][$key] = explode('"', $dataAll['data'][$key])[1];
            }
        }

        $submittedData = $dataAll['data'];

        foreach ($submittedData as $key => $value) {
            $str = "$key";
            $pattern = "/name/i";
            $isName = preg_match($pattern, $str);
            if ($isName) {
                unset($submittedData[$key]);
                $nameValues = explode('|', $value);
                if (count($nameValues) == 2) {
                    $nameOrganized = [
                        'first_name' => $nameValues[0],
                        'last_name' => $nameValues[1]

                    ];
                } else {
                    $nameOrganized = [
                        'first_name' => $nameValues[0],
                        'middle_name' => $nameValues[1],
                        'last_name' => $nameValues[2]
                    ];
                }
            }
        }

        $finalData = array_merge($submittedData, $nameOrganized);

        return ['triggered_entity' => 'WeForms', 'triggered_entity_id' => $form_id, 'data' => $finalData, 'flows' => $flows];
    }

    public static function wpcwUserEnrolledCourse($userId, $courses)
    {
        $user = get_user_by('id', $userId);
        $flows = Flow::exists('WPCourseware', 'userEnrolledCourse');

        if (!$flows || !$user || !function_exists('WPCW_courses_getCourseDetails')) {
            return;
        }

        foreach ($courses as $courseId) {
            $course = WPCW_courses_getCourseDetails($courseId);

            if (!$course) {
                continue;
            }

            $data = [
                'enroll_user_id'    => $userId,
                'enroll_user_name'  => $user->display_name,
                'enroll_user_email' => $user->user_email,
                'course_id'         => $courseId,
                'course_title'      => $course->course_title,
            ];

            return ['triggered_entity' => 'WPCourseware', 'triggered_entity_id' => 'userEnrolledCourse', 'data' => $data, 'flows' => $flows];
        }
    }

    public static function wpcwCourseCompleted($userId, $unitId, $course)
    {
        $flows = Flow::exists('WPCourseware', 'courseCompleted');
        $flows = self::wpcwFlowFilter($flows, 'selectedCourse', $course->course_id);

        if (!$flows) {
            return;
        }

        $user = get_user_by('id', $userId);

        if (!$user) {
            return;
        }

        $data = [
            'enroll_user_id'    => $userId,
            'enroll_user_name'  => $user->display_name,
            'enroll_user_email' => $user->user_email,
            'course_id'         => $course->course_id,
            'course_title'      => $course->course_title,
        ];

        return ['triggered_entity' => 'WPCourseware', 'triggered_entity_id' => 'courseCompleted', 'data' => $data, 'flows' => $flows];
    }

    public static function wpcwModuleCompleted($userId, $unitId, $module)
    {
        $flows = Flow::exists('WPCourseware', 'moduleCompleted');
        $flows = self::wpcwFlowFilter($flows, 'selectedModule', $module->module_id);

        if (!$flows) {
            return;
        }

        $user = get_user_by('id', $userId);

        if (!$user) {
            return;
        }

        $data = [
            'enroll_user_id'    => $userId,
            'enroll_user_name'  => $user->display_name,
            'enroll_user_email' => $user->user_email,
            'module_id'         => $module->module_id,
            'module_title'      => $module->module_title,
            'course_title'      => $module->course_title,
        ];

        return ['triggered_entity' => 'WPCourseware', 'triggered_entity_id' => 'moduleCompleted', 'data' => $data, 'flows' => $flows];
    }

    public static function wpcwUnitCompleted($userId, $unitId, $unitData)
    {
        $flows = Flow::exists('WPCourseware', 'unitCompleted');
        $flows = self::wpcwFlowFilter($flows, 'selectedUnit', $unitId);

        if (!$flows) {
            return;
        }

        $unit = get_post($unitId);
        $user = get_user_by('id', $userId);
        if (!$unit || !$user) {
            return;
        }

        $data = [
            'enroll_user_id' => $userId,
            'enroll_user_name' => $user->display_name,
            'enroll_user_email' => $user->user_email,
            'unit_id' => $unitId,
            'unit_title' => $unit->post_title,
            'module_title' => $unitData->module_title,
            'course_title' => $unitData->course_title,
        ];

        return ['triggered_entity' => 'WPCourseware', 'triggered_entity_id' => 'unitCompleted', 'data' => $data, 'flows' => $flows];
    }

    protected static function wpcwFlowFilter($flows, $key, $value)
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

    public static function wpefHandleSubmission($data)
    {
        if (!($data instanceof \IPT_FSQM_Form_Elements_Data)) {
            return;
        }
        $form_id = $data->form_id;

        if (empty($form_id)) {
            return;
        }

        $flows = Flow::exists('WPEF', $form_id);

        if (!$flows) {
            return;
        }

        $entry = array_merge(
            self::wpefProcessValues($data, 'pinfo'),
            self::wpefProcessValues($data, 'freetype'),
            self::wpefProcessValues($data, 'mcq')
        );

        return ['triggered_entity' => 'WPEF', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }

    public static function wpefProcessValues($data, $type)
    {
        $formID = $data->form_id;
        $dataID = $data->data_id;
        $fields = $data->data->{$type};
        $processedValues = [];

        foreach ($fields as $index => $field) {
            if ($field['type'] == 'datetime') {
                $processedValues["{$field['m_type']}.$index"] =  self::wpefProcessDateFieldValue($index, $field, $data);
            } else if ($field['type'] == 'feedback_matrix') {
                $processedValues["{$field['m_type']}.$index"] =   $field['rows'];
            } else if ($field['type'] == 'gps') {
                $processedValues["{$field['m_type']}.$index"] =   $field['lat'] . ", " .  $field['long'];
            } else if ($field['type'] == 'upload') {
                $processedValues["{$field['m_type']}.$index"] = self::wpefProcessUploadFieldValue($index, $field, $data);
            } else if ($field['type'] == 'address') {
                $processedValues = array_merge($processedValues, self::wpefProcessAddressFieldValue($index, $field, $data));
            } else {
                $processedValues["{$field['m_type']}.$index"] =   '';
                if (isset($field['value'])) {
                    $processedValues["{$field['m_type']}.$index"] =   $field['value'];
                } else if (isset($field['values'])) {
                    $processedValues["{$field['m_type']}.$index"] =   $field['values'];
                } else if (isset($field['options'])) {
                    $processedValues["{$field['m_type']}.$index"] =   is_array($field['options']) && count($field['options']) == 1 ? $field['options'][0] : $field['options'];
                } else if (isset($field['rows'])) {
                    $processedValues["{$field['m_type']}.$index"] =   $field['rows'];
                } else if (isset($field['order'])) {
                    $processedValues["{$field['m_type']}.$index"] =   $field['order'];
                }
            }
        }

        return $processedValues;
    }

    public static function wpefProcessDateFieldValue($index, $field, $data)
    {
        $processedValue = '';
        $fieldInfo =  $data->{$field['m_type']}[$index];
        $dateTimeHelper = new DateTimeHelper();
        $f_date_format = $fieldInfo['settings']['date_format'];
        $f_time_format = $fieldInfo['settings']['time_format'];
        if ($f_date_format == 'mm/dd/yy') {
            $date_format = 'm/d/Y';
        } else if ($f_date_format == 'yy-mm-dd') {
            $date_format = 'Y-m-d';
        } else if ($f_date_format == 'dd.mm.yy') {
            $date_format = 'd.m.Y';
        } else {
            $date_format = 'd-m-Y';
        }

        if ($f_time_format == 'HH:mm:ss') {
            $time_format = 'H:i:s';
        } else {
            $time_format = 'h:i:s A';
        }

        $date_time_format = "$date_format $time_format";
        $processedValue = $dateTimeHelper->getFormated($field['value'], $date_time_format, wp_timezone(), 'Y-m-d\TH:i', null);
        return $processedValue;
    }

    public static function wpefProcessUploadFieldValue($index, $field, $data)
    {
        $processedValue = [];
        $elementValueHelper = new \IPT_EForm_Form_Elements_Values($data->data_id, $data->form_id);
        $elementValueHelper->reassign($data->data_id, $data);
        foreach ($field['id'] as $value) {
            $fileInfo = $elementValueHelper->value_upload($data->{$field['m_type']}[$index], $field, 'json', 'label', $value);
            foreach ($fileInfo as $f) {
                if (isset($f['guid'])) {
                    $processedValue[] = Common::filePath($f['guid']);
                }
            }
        }
        return $processedValue;
    }

    public static function wpefProcessAddressFieldValue($index, $field, $data)
    {
        $processedValue = [];
        foreach ($field['values'] as $key => $value) {
            $processedValue["{$field['m_type']}.$index.$key"] = $value;
        }
        return $processedValue;
    }

    public static function handleWsFormSubmit($form, $submit)
    {
        $form_id = $submit->form_id;

        $flows = Flow::exists('WSForm', $form_id);
        if (!$flows) {
            return;
        }

        $data = [];
        if (isset($submit->meta)) {
            foreach ($submit->meta as $key => $field_value) {
                if (empty($field_value) || (is_array($field_value) && !array_key_exists('id', $field_value))) {
                    continue;
                }
                $value = wsf_submit_get_value($submit, $key);

                if (($field_value['type'] == 'file' || $field_value['type'] == 'signature') && !empty($value)) {
                    $upDir = wp_upload_dir();
                    $files = $value;
                    $value = [];

                    if (is_array($files)) {
                        foreach ($files as $k => $file) {
                            if (array_key_exists('hash', $file)) {
                                continue;
                            }
                            $value[$k] = $upDir['basedir'] . '/' . $file['path'];
                        }
                    }
                } elseif ($field_value['type'] == 'radio') {
                    $value = is_array($value) ? $value[0] : $value;
                }
                $data[$key] = $value;
            }
        }

        return ['triggered_entity' => 'WSForm', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];
    }
}
