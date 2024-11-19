<?php

namespace BitCode\FI\Actions\Voxel;

class VoxelHelper
{
    public static function updateVoxelPost($finalData, $postType, $postId)
    {
        if (!class_exists('Voxel\Post') || !class_exists('Voxel\Post_Type')) {
            return false;
        }

        $post = \Voxel\Post::force_get($postId);

        if (!$post) {
            return false;
        }

        $postType = \Voxel\Post_Type::get($postType);
        $postFields = $postType->get_fields();

        if (!\is_array($postFields) || empty($postFields)) {
            return false;
        }

        foreach ($postFields as $postField) {
            $fieldType = $postField->get_type();

            if (\in_array($fieldType, ['ui-step', 'ui-html', 'ui-heading', 'ui-image', 'repeater'], true)) {
                continue;
            }

            $fieldKey = $postField->get_key();
            $field = $post->get_field($fieldKey);

            switch ($fieldType) {
                case 'event-date':
                case 'recurring-date':
                    $eventDateValue = [];

                    if (!empty($finalData[$fieldKey . '_event_start_date'])) {
                        $eventDateValue['start'] = $finalData[$fieldKey . '_event_start_date'];
                    }

                    if (!empty($finalData[$fieldKey . '_event_end_date'])) {
                        $eventDateValue['end'] = $finalData[$fieldKey . '_event_end_date'];
                    }

                    if (!empty($finalData[$fieldKey . '_event_frequency'])) {
                        $eventDateValue['frequency'] = $finalData[$fieldKey . '_event_frequency'];
                    }

                    if (!empty($finalData[$fieldKey . '_repeat_every'])) {
                        $eventDateValue['unit'] = $finalData[$fieldKey . '_repeat_every'];
                    }

                    if (!empty($finalData[$fieldKey . '_event_until'])) {
                        $eventDateValue['until'] = $finalData[$fieldKey . '_event_until'];
                    }

                    if (!empty($eventDateValue)) {
                        $field->update([$eventDateValue]);
                    }

                    break;
                case 'location':
                    $locationValue = [];

                    if (!empty($finalData[$fieldKey . '_address'])) {
                        $locationValue['address'] = $finalData[$fieldKey . '_address'];
                    }
                    if (!empty($finalData[$fieldKey . '_latitude'])) {
                        $locationValue['latitude'] = $finalData[$fieldKey . '_latitude'];
                    }
                    if (!empty($finalData[$fieldKey . '_longitude'])) {
                        $locationValue['longitude'] = $finalData[$fieldKey . '_longitude'];
                    }

                    if (!empty($locationValue)) {
                        $field->update($locationValue);
                    }

                    break;
                case 'work-hours':
                    $finalWorkHours = [];

                    if (!empty($finalData[$fieldKey . '_work_days'])) {
                        $days = preg_split('/, ?/', $finalData[$fieldKey . '_work_days']);
                        $finalWorkHours['days'] = $days;
                    }

                    if (!empty($finalData[$fieldKey . '_work_hours'])) {
                        $multiHours = preg_split('/, ?/', $finalData[$fieldKey . '_work_hours']);
                        $formattedHours = [];

                        foreach ($multiHours as $hours) {
                            $splitHours = preg_split('/ ?- ?/', $hours);
                            $formattedHours[] = [
                                'from' => isset($splitHours[0]) ? $splitHours[0] : '',
                                'to'   => isset($splitHours[1]) ? $splitHours[1] : '',
                            ];
                        }

                        $finalWorkHours['hours'] = $formattedHours;
                    }

                    if (!empty($finalData[$fieldKey . '_work_status'])) {
                        $finalWorkHours['status'] = $finalData[$fieldKey . '_work_status'];
                    }

                    if (!empty($finalWorkHours)) {
                        $field->update([$finalWorkHours]);
                    }

                    break;
                case 'file':
                case 'image':
                case 'profile-avatar':
                case 'gallery':
                case 'cover':
                    $attachmentValues = [];

                    if (!\is_array($finalData[$fieldKey]) && !empty($finalData[$fieldKey])) {
                        $attchmentIds = explode(',', $finalData[$fieldKey]);

                        foreach ($attchmentIds as $attchmentId) {
                            $attachmentValues[] = [
                                'source'  => 'existing',
                                'file_id' => (int) $attchmentId,
                            ];
                        }
                    }

                    if (!empty($attachmentValues)) {
                        $field->update($attachmentValues);
                    }

                    break;
                case 'post-relation':
                    if (!empty($finalData[$fieldKey])) {
                        $value = array_map(
                            function ($postRelationId) {
                                return (int) $postRelationId;
                            },
                            explode(',', $finalData[$fieldKey])
                        );

                        if (!empty($value)) {
                            $field->update($value);
                        }
                    }

                    break;
                case 'taxonomy':
                    if (!empty($finalData[$fieldKey])) {
                        $value = explode(',', $finalData[$fieldKey]);

                        if (!empty($value)) {
                            $field->update($value);
                        }
                    }

                    break;
                case 'product':
                    if (!empty($finalData[$fieldKey])) {
                        $productData = explode(',', $finalData[$fieldKey]);
                        $previousData = $field->get_value();
                        $updatedData = array_merge($previousData ?? [], $productData);
                        $field->update($updatedData);
                    }

                    break;
                default:
                    if (!empty($finalData[$fieldKey])) {
                        $field->update($finalData[$fieldKey]);
                    }

                    break;
            }
        }
    }

    public static function getEventFields($fieldKey, $postField)
    {
        return [
            [
                'key'      => $fieldKey . '_event_start_date',
                'label'    => wp_sprintf(__('Event Start Date (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
            [
                'key'      => $fieldKey . '_event_end_date',
                'label'    => wp_sprintf(__('Event End Date (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
            [
                'key'      => $fieldKey . '_event_frequency',
                'label'    => wp_sprintf(__('Event Frequency (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
            [
                'key'      => $fieldKey . '_repeat_every',
                'label'    => wp_sprintf(__('Event unit (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
            [
                'key'      => $fieldKey . '_event_until',
                'label'    => wp_sprintf(__('Event Until (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
        ];
    }

    public static function getLocationFields($fieldKey, $postField)
    {
        return [
            [
                'key'      => $fieldKey . '_address',
                'label'    => __('Address', 'bit-integrations') . ' (' . $postField->get_label() . ')',
                'label'    => wp_sprintf(__('Address (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
            [
                'key'      => $fieldKey . '_latitude',
                'label'    => wp_sprintf(__('Latitude (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
            [
                'key'      => $fieldKey . '_longitude',
                'label'    => wp_sprintf(__('Longitude (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
        ];
    }

    public static function getWorkHoursFields($fieldKey, $postField)
    {
        return [
            [
                'key'      => $fieldKey . '_work_days',
                'label'    => wp_sprintf(__('Work Days (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
            [
                'key'      => $fieldKey . '_work_hours',
                'label'    => wp_sprintf(__('Work Hours (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
            [
                'key'      => $fieldKey . '_work_status',
                'label'    => wp_sprintf(__('Work Status (%s)', 'bit-integrations'), $postField->get_label()),
                'required' => false,
            ],
        ];
    }

    public static function getCommonFieldsAndMap($selectedTask, $isUpdateTask)
    {
        $fields = $fieldMap = [];

        switch ($selectedTask) {
            case VoxelTasks::NEW_PROFILE:
                $fields[] = [
                    'key'      => 'user_email',
                    'label'    => __('User Email', 'bit-integrations'),
                    'required' => !$isUpdateTask,
                ];

                $fieldMap[] = (object) ['formField' => '', 'voxelField' => 'user_email'];

                break;

            case VoxelTasks::UPDATE_PROFILE:
                $fields[] = [
                    'key'      => 'profile_id',
                    'label'    => __('Profile ID', 'bit-integrations'),
                    'required' => true,
                ];

                $fieldMap[] = (object) ['formField' => '', 'voxelField' => 'profile_id'];

                break;

            default:
                $fields[] = [
                    'key'      => 'post_author_email',
                    'label'    => __('Post Author Email', 'bit-integrations'),
                    'required' => !$isUpdateTask,
                ];

                $fieldMap[] = (object) ['formField' => '', 'voxelField' => 'post_author_email'];

                break;
        }

        return ['fields' => $fields, 'fieldMap' => $fieldMap];
    }
}
