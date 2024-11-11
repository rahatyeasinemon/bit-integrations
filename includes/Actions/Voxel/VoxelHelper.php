<?php

namespace BitCode\FI\Actions\Voxel;

class VoxelHelper
{
    public const NEW_POST = 'newPost';

    public const NEW_COLLECTION_POST = 'newCollectionPost';

    // constants for static post type

    public const COLLECTION_POST_TYPE = 'collection';

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

            if ($fieldType === 'event-date' || $fieldType === 'recurring-date') {
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
            } elseif ($fieldType === 'location') {
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
            } elseif ($fieldType === 'work-hours') {
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
            } elseif (\in_array($fieldType, ['file', 'image', 'profile-avatar', 'gallery', 'cover'])) {
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
            } elseif ($fieldType === 'post-relation') {
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
            } elseif ($fieldType === 'taxonomy') {
                if (!empty($finalData[$fieldKey])) {
                    $value = explode(',', $finalData[$fieldKey]);

                    if (!empty($value)) {
                        $field->update($value);
                    }
                }
            } elseif ($fieldType === 'product') {
                if (!empty($finalData[$fieldKey])) {
                    $productData = explode(',', $finalData[$fieldKey]);
                    $previousData = $field->get_value();
                    $updatedData = array_merge($previousData ?? [], $productData);
                    $field->update($updatedData);
                }
            } else {
                if (!empty($finalData[$fieldKey])) {
                    $field->update($finalData[$fieldKey]);
                }
            }
        }
    }
}
