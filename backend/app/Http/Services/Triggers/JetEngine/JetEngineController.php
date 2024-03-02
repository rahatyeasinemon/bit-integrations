<?php

namespace BitApps\BTCBI\Http\Services\Triggers\JetEngine;

use BitApps\BTCBI\Model\Flow;
use BitApps\BTCBI\Http\Services\Triggers\JetEngine\JetEngineHelper;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

final class JetEngineController
{
    public static function info()
    {
        return [
            'name' => 'JetEngine',
            'title' => 'JetEngine',
            'type' => 'form',
            'trigger' => 'Post',
            'is_active' => true,
            'list' => [
                'action' => 'jetengine/get',
                'method' => 'get',
            ],
            'fields' => [
                'action' => 'jetengine/get/form',
                'method' => 'post',
                'data' => ['id'],
            ],
            'isPro' => false,
        ];
    }

    public static function fields($id)
    {
        $triggerIdList = [1, 2];

        if (in_array($id, $triggerIdList)) {
            $fields = JetEngineHelper::postFields();
        }

        return $fields;
    }

    public function getAll()
    {
        $triggers = [
            ['id' => 1, 'title' => 'A user updates a specific JetEngine field on a specific post type'],
            ['id' => 2, 'title' => 'A user updates a specific JetEngine field on a specific post type a specific value'],
        ];

        return Response::success($triggers);
    }

    public function get_a_form($data)
    {
        $responseData = [];
        $missing_field = null;

        if (!property_exists($data, 'id')) {
            $missing_field = 'Form ID';
        }

        if (!is_null($missing_field)) {
            Response::error(sprintf(__('%s can\'t be empty', 'bit-integrations'), $missing_field));
        }

        $ids = [1, 2];

        if (in_array($data->id, $ids)) {
            $responseData['types'] = array_values(JetEngineHelper::getPostTypes());
            array_unshift($responseData['types'], ['id' => 'any-post-type', 'title' => 'Any Post Type']);
        }

        $responseData['fields'] = self::fields($data->id);

        if (count($responseData['fields']) <= 0) {
            return Response::error(__('Form fields doesn\'t exists', 'bit-integrations'));
        }

        return Response::success($responseData);
    }

    public static function post_meta_data($meta_id, $post_id, $meta_key, $meta_value)
    {
        $postData = get_post($post_id);
        $finalData = (array) $postData + ['meta_key' => $meta_key, 'meta_value' => $meta_value];
        $postData = get_post($post_id);
        $user_id = get_current_user_id();
        $postCreateFlow = Flow::exists('JetEngine', 1);
        if (!$postCreateFlow) {
            return;
        }
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
            Flow::execute('JetEngine', 1, $finalData, $postCreateFlow);
        }
    }

    public static function post_meta_value_check($meta_id, $post_id, $meta_key, $meta_value)
    {
        $postData = get_post($post_id);
        $finalData = (array) $postData + ['meta_key' => $meta_key, 'meta_value' => $meta_value];
        $postData = get_post($post_id);
        $user_id = get_current_user_id();
        $postCreateFlow = Flow::exists('JetEngine', 2);
        if (!$postCreateFlow) {
            return;
        }
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
            Flow::execute('JetEngine', 2, $finalData, $postCreateFlow);
        }
    }

    public static function getAllPostTypes()
    {
        $types = array_values(JetEngineHelper::getPostTypes());
        array_unshift($types, ['id' => 'any-post-type', 'title' => 'Any Post Type']);
        return Response::success($types);
    }

    public static function getAllPosts()
    {
        $posts = JetEngineHelper::getPostTitles();
        array_unshift($posts, ['id' => 'any-post', 'title' => 'Any Post']);
        return Response::success($posts);
    }
}

// public static function post_meta_data($meta_id, $post_id, $meta_key, $meta_value)
//     {
//         $postData = get_post($post_id);
//         $finalData = (array)$postData + ['meta_key' => $meta_key, 'meta_value' => $meta_value];
//         $postData = get_post($post_id);
//         $user_id = get_current_user_id();
//         $typeId = 1;
//         $postCreateFlow = Flow::exists('JetEngine', $typeId);
//         if (!$postCreateFlow && $postCreateFlow = Flow::exists('JetEngine', 2)) {
//             $typeId = 2;
//         };
//         $postType = $postData->post_type;

//         $info = isset($postCreateFlow[0]->flow_details) ? json_decode($postCreateFlow[0]->flow_details) : '';
//         $selectedPostType = !empty($info->selectedPostType) ? $info->selectedPostType : 'any-post-type';
//         $selectedMetaKey = !empty($info->selectedMetaKey) ? $info->selectedMetaKey : '';
//         $selectedMetaValue = !empty($info->selectedMetaValue) ? $info->selectedMetaValue : '';

//         $isPostTypeMatched = $selectedPostType ? $selectedPostType === $postType : true;
//         $isMetaKeyMatched = $selectedMetaKey ? $selectedMetaKey === $meta_key : true;
//         $isMetaValueMatched = $selectedMetaValue ? $selectedMetaValue === $meta_value : true;
//         $isEditable = $user_id && $postCreateFlow && !($meta_key === '_edit_lock');
//         if ($typeId === 1 && $isPostTypeMatched && $isMetaKeyMatched && $isEditable) {
//             Flow::execute('JetEngine', 1, $finalData, $postCreateFlow);
//         } elseif ($typeId === 2 && $isPostTypeMatched && $isMetaKeyMatched && $isMetaValueMatched && $isEditable) {
//             Flow::execute('JetEngine', 2, $finalData, $postCreateFlow);
//         }
//     }
