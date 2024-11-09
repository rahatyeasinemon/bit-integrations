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

    public function getAllEvents()
    {
        self::checkIfVoxelExists();

        $events = get_posts(
            [
                'post_type' => 'tribe_events',
                'orderby'   => 'title',
                'order'     => 'ASC',

                'post_status'    => 'publish',
                'posts_per_page' => -1,
            ]
        );

        $eventList = [];

        if (!empty($events)) {
            foreach ($events as $event) {
                $eventList[] = (object) ['value' => (string) $event->ID, 'label' => $event->post_title];
            }
        }

        wp_send_json_success($eventList, 200);
    }

    public function execute($integrationData, $fieldValues)
    {
        self::checkIfVoxelExists();

        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;
        $selectedTask = $integrationDetails->selectedTask;
        $actions = (array) $integrationDetails->actions;
        $selectedEvent = $integrationDetails->selectedEvent;

        if (empty($fieldMap) || empty($selectedTask)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('Fields map, task are required for Voxel', 'bit-integrations'));
        }

        $recordApiHelper = new RecordApiHelper($integId);
        $voxelResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $selectedTask, $selectedEvent, $actions);

        if (is_wp_error($voxelResponse)) {
            return $voxelResponse;
        }

        return $voxelResponse;
    }
}
