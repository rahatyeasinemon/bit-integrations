<?php

/**
 * Voxel Integration
 */

namespace BitCode\FI\Actions\Voxel;

use WP_Error;

/**
 * Provide functionality for Voxel integration
 */
class VoxelController
{
    public function authentication()
    {
        return self::checkIfVoxelExists();
    }

    public static function checkIfVoxelExists()
    {
        if (wp_get_theme()->get_template() === 'voxel') {
            return true;
        }

        wp_send_json_error(wp_sprintf(__('%s is not active or installed!', 'bit-integrations'), 'Voxel'), 400);
    }

    public function getPostTypes()
    {
        self::checkIfVoxelExists();

        $postTypeList = $voxelPostTypes = [];

        if (class_exists('\Voxel\Post_Type')) {
            $voxelPostTypes = \Voxel\Post_Type::get_voxel_types();
        }

        if (!empty($voxelPostTypes)) {
            foreach ($voxelPostTypes as $key => $voxelPostType) {
                $postType = get_post_type_object($key);

                if (!$postType) {
                    continue;
                }

                if (\in_array($postType->name, ['collection', 'profile'], true)) {
                    continue;
                }

                $postTypeList[] = (object) [
                    'value' => $postType->name,
                    'label' => $postType->labels->singular_name,
                ];
            }
        }

        wp_send_json_success($postTypeList, 200);
    }

    public function getPosts($request)
    {
        self::checkIfVoxelExists();

        if (empty($request->postType)) {
            wp_send_json_error(__('No post type found!', 'bit-integrations'), 400);
        }

        $posts = get_posts(
            [
                'post_type'      => $request->postType,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'post_status'    => 'any',
                'posts_per_page' => -1,
            ]
        );

        if (empty($posts) || !$posts) {
            wp_send_json_error(__('No post found!', 'bit-integrations'), 400);
        }

        foreach ($posts as $post) {
            $postList[] = (object) ['value' => (string) $post->ID, 'label' => $post->post_title];
        }

        wp_send_json_success($postList, 200);
    }

    public function getPostFields($request)
    {
        self::checkIfVoxelExists();

        if (empty($request->postType)) {
            wp_send_json_error(__('No post type found!', 'bit-integrations'), 400);
        }

        $fields = $fieldMap = [];

        if (!class_exists('Voxel\Post_Type')) {
            return ['fields' => $fields, 'fieldMap' => $fieldMap];
        }

        $selectedTask = $request->selectedTask;
        $isUpdateTask = \in_array($selectedTask, [VoxelTasks::UPDATE_POST, VoxelTasks::UPDATE_COLLECTION_POST, VoxelTasks::UPDATE_PROFILE]);

        $postType = \Voxel\Post_Type::get($request->postType);
        $postFields = $postType->get_fields();

        if (\is_array($postFields) && !empty($postFields)) {
            if ($selectedTask === VoxelTasks::NEW_PROFILE) {
                $fields = VoxelHelper::generateVoxelFields(['user_email' => !$isUpdateTask]);
                $fieldMap[] = (object) ['formField' => '', 'voxelField' => 'user_email'];
            } elseif ($selectedTask === VoxelTasks::UPDATE_PROFILE) {
                $fields = VoxelHelper::generateVoxelFields(['profile_id' => true]);
                $fieldMap[] = (object) ['formField' => '', 'voxelField' => 'profile_id'];
            } else {
                $fields = VoxelHelper::generateVoxelFields(['post_author_email' => !$isUpdateTask]);
                $fieldMap[] = (object) ['formField' => '', 'voxelField' => 'post_author_email'];
            }

            foreach ($postFields as $postField) {
                $fieldType = $postField->get_type();

                if (\in_array($fieldType, ['ui-step', 'ui-html', 'ui-heading', 'ui-image', 'repeater'], true)) {
                    continue;
                }

                $fieldKey = $postField->get_key();

                switch ($fieldType) {
                    case 'event-date':
                    case 'recurring-date':
                        $eventFields = VoxelHelper::generateVoxelFields(
                            ['event_start_date' => false, 'event_end_date' => false, 'event_frequency' => false, 'repeat_every' => false, 'event_until' => false],
                            $fieldKey,
                            $postField
                        );

                        $fields = array_merge($fields, $eventFields);

                        break;
                    case 'location':
                        $locationFields = VoxelHelper::generateVoxelFields(
                            ['address' => false, 'latitude' => false, 'longitude' => false],
                            $fieldKey,
                            $postField
                        );

                        $fields = array_merge($fields, $locationFields);

                        break;
                    case 'work-hours':
                        $workOursFields = VoxelHelper::generateVoxelFields(
                            ['work_days' => false, 'work_hours' => false, 'work_status' => false],
                            $fieldKey,
                            $postField
                        );

                        $fields = array_merge($fields, $workOursFields);

                        break;
                    default:
                        $fields[] = [
                            'key'      => $fieldKey,
                            'label'    => $postField->get_label(),
                            'required' => $isUpdateTask ? false : $postField->is_required(),
                        ];

                        if (!$isUpdateTask && $postField->is_required()) {
                            $fieldMap[] = (object) ['formField' => '', 'voxelField' => $fieldKey];
                        }

                        break;
                }
            }
        }

        if ($isUpdateTask && $selectedTask !== VoxelTasks::UPDATE_PROFILE) {
            $fieldMap = [(object) ['formField' => '', 'voxelField' => '']];
        }

        wp_send_json_success(['fields' => $fields, 'fieldMap' => $fieldMap], 200);
    }

    public function execute($integrationData, $fieldValues)
    {
        self::checkIfVoxelExists();

        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;
        $selectedTask = $integrationDetails->selectedTask;

        if (empty($fieldMap) || empty($selectedTask)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('Fields map, task are required for Voxel', 'bit-integrations'));
        }

        $selectedOptions = [
            'actions'            => (array) $integrationDetails->actions,
            'selectedPostType'   => $integrationDetails->selectedPostType,
            'selectedPostStatus' => $integrationDetails->selectedPostStatus,
            'selectedPost'       => $integrationDetails->selectedPost,
        ];

        $recordApiHelper = new RecordApiHelper($integId);
        $voxelResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $selectedTask, $selectedOptions);

        if (is_wp_error($voxelResponse)) {
            return $voxelResponse;
        }

        return $voxelResponse;
    }
}
