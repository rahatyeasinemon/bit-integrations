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

                if ($postType) {
                    if (\in_array($postType->name, ['collection', 'profile'], true)) {
                        continue;
                    }

                    $postTypeList[] = (object) [
                        'value' => $postType->name,
                        'label' => $postType->labels->singular_name,
                    ];
                }
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

        if ($request->selectedTask === VoxelHelper::UPDATE_POST) {
            $isUpdateTask = true;
        } else {
            $isUpdateTask = false;
        }

        $postType = \Voxel\Post_Type::get($request->postType);
        $postFields = $postType->get_fields();

        if (\is_array($postFields) && !empty($postFields)) {
            if ($request->postType === VoxelHelper::PROFILE_POST_TYPE) {
                $fields[] = [
                    'key'      => 'user_email',
                    'label'    => 'User Email',
                    'required' => $isUpdateTask ? false : true,
                ];

                $fieldMap[] = (object) ['formField' => '', 'voxelField' => 'user_email'];
            } else {
                $fields[] = [
                    'key'      => 'post_author_email',
                    'label'    => 'Post Author Email',
                    'required' => $isUpdateTask ? false : true,
                ];

                $fieldMap[] = (object) ['formField' => '', 'voxelField' => 'post_author_email'];
            }

            foreach ($postFields as $postField) {
                $fieldType = $postField->get_type();

                if (\in_array($fieldType, ['ui-step', 'ui-html', 'ui-heading', 'ui-image', 'repeater'], true)) {
                    continue;
                }

                $fieldKey = $postField->get_key();

                if ($fieldType === 'event-date' || $fieldType === 'recurring-date') {
                    $eventFields = [
                        [
                            'key'      => $fieldKey . '_event_start_date',
                            'label'    => __('Event Start Date', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                        [
                            'key'      => $fieldKey . '_event_end_date',
                            'label'    => __('Event End Date', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                        [
                            'key'      => $fieldKey . '_event_frequency',
                            'label'    => __('Event Frequency', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                        [
                            'key'      => $fieldKey . '_repeat_every',
                            'label'    => __('Event Unit', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                        [
                            'key'      => $fieldKey . '_event_until',
                            'label'    => __('Event Until', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                    ];

                    $fields = array_merge($fields, $eventFields);
                } elseif ($fieldType === 'location') {
                    $locationFields = [
                        [
                            'key'      => $fieldKey . '_address',
                            'label'    => __('Address', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                        [
                            'key'      => $fieldKey . '_latitude',
                            'label'    => __('Latitude', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                        [
                            'key'      => $fieldKey . '_longitude',
                            'label'    => __('Longitude', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                    ];

                    $fields = array_merge($fields, $locationFields);
                } elseif ($fieldType === 'work-hours') {
                    $workOursFields = [
                        [
                            'key'      => $fieldKey . '_work_days',
                            'label'    => __('Work Days', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                        [
                            'key'      => $fieldKey . '_work_hours',
                            'label'    => __('Work Hours', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                        [
                            'key'      => $fieldKey . '_work_status',
                            'label'    => __('Work Status', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                            'required' => false,
                        ],
                    ];

                    $fields = array_merge($fields, $workOursFields);
                } else {
                    $fields[] = [
                        'key'      => $fieldKey,
                        'label'    => $postField->get_label(),
                        'required' => $isUpdateTask ? false : $postField->is_required(),
                    ];

                    if ($postField->is_required()) {
                        $fieldMap[] = (object) ['formField' => '', 'voxelField' => ''];
                    }
                }
            }
        }

        if ($isUpdateTask) {
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
