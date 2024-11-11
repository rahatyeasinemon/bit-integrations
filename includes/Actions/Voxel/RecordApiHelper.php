<?php

/**
 * Voxel Record Api
 */

namespace BitCode\FI\Actions\Voxel;

use BitCode\FI\Core\Util\Common;
use BitCode\FI\Log\LogHandler;

/**
 * Provide functionality for Record insert, update
 */
class RecordApiHelper
{
    private $_integrationID;

    public function __construct($integId)
    {
        $this->_integrationID = $integId;
    }

    public function newPost($finalData, $selectedOptions)
    {
        if (empty($finalData['post_author_email'])) {
            return ['success' => false, 'message' => __('Request parameter(s) empty!', 'bit-integrations'), 'code' => 400];
        }

        $authorEmail = $finalData['post_author_email'];
        $postType = !empty($selectedOptions['selectedPostType']) ? $selectedOptions['selectedPostType'] : 'post';
        $postStatus = !empty($selectedOptions['selectedPostStatus']) ? $selectedOptions['selectedPostStatus'] : 'draft';
        $postTitle = !empty($finalData['title']) ? $finalData['title'] : '';

        if (is_email($authorEmail)) {
            $user = get_user_by('email', $authorEmail);
            $userId = $user ? $user->ID : 1;
        } else {
            $userId = 1;
        }

        $postData = [
            'post_type'   => $postType,
            'post_title'  => $postTitle,
            'post_status' => $postStatus,
            'post_author' => $userId,
        ];

        $postId = wp_insert_post($postData);

        VoxelHelper::updateVoxelPost($finalData, $postType, $postId);

        return ['success' => true, 'message' => __('New post created successfully. Post ID: ', 'bit-integrations') . $postId];
    }

    public function newCollectionPost($finalData, $selectedOptions)
    {
        if (empty($finalData['post_author_email'])) {
            return ['success' => false, 'message' => __('Request parameter(s) empty!', 'bit-integrations'), 'code' => 400];
        }

        $authorEmail = $finalData['post_author_email'];
        $postType = VoxelHelper::COLLECTION_POST_TYPE;
        $postStatus = !empty($selectedOptions['selectedPostStatus']) ? $selectedOptions['selectedPostStatus'] : 'draft';
        $postTitle = !empty($finalData['title']) ? $finalData['title'] : '';

        if (is_email($authorEmail)) {
            $user = get_user_by('email', $authorEmail);
            $userId = $user ? $user->ID : 1;
        } else {
            $userId = 1;
        }

        $postData = [
            'post_type'   => $postType,
            'post_title'  => $postTitle,
            'post_status' => $postStatus,
            'post_author' => $userId,
        ];

        $postId = wp_insert_post($postData);

        VoxelHelper::updateVoxelPost($finalData, $postType, $postId);

        return ['success' => true, 'message' => __('New collection post created successfully. Post ID: ', 'bit-integrations') . $postId];
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->voxelField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }

        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $selectedTask, $selectedOptions)
    {
        if (isset($fieldMap[0]) && empty($fieldMap[0]->formField)) {
            $finalData = [];
        } else {
            $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        }

        $type = $typeName = '';

        if ($selectedTask === VoxelHelper::NEW_POST) {
            $response = $this->newPost($finalData, $selectedOptions);
            $type = 'New Post';
            $typeName = 'Create New Post';
        } elseif ($selectedTask === VoxelHelper::NEW_COLLECTION_POST) {
            $response = $this->newCollectionPost($finalData, $selectedOptions);
            $type = 'New Collection Post';
            $typeName = 'Create New Collection Post';
        }

        if ($response['success']) {
            $res = ['message' => $response['message']];
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => $type, 'type_name' => $typeName]), 'success', wp_json_encode($res));
        } else {
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => $type, 'type_name' => $typeName]), 'error', wp_json_encode($response));
        }

        return $response;
    }
}
