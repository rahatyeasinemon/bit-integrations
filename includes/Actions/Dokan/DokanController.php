<?php

/**
 * Dokan Integration
 */

namespace BitCode\FI\Actions\Dokan;

use WP_Error;

/**
 * Provide functionality for Dokan integration
 */
class DokanController
{
    public function authentication()
    {
        if (self::checkedDokanExists()) {
            wp_send_json_success(true);
        } else {
            wp_send_json_error(
                __(
                    'Please! Install Dokan',
                    'bit-integrations'
                ),
                400
            );
        }
    }

    public static function checkedDokanExists()
    {
        if (!is_plugin_active('dokan-lite/dokan.php')) {
            wp_send_json_error(__('Dokan Plugin is not active or not installed', 'bit-integrations'), 400);
        } else {
            return true;
        }
    }

    public function getReputations()
    {
        self::checkedDokanExists();

        $levels = WPF()->member->levels();

        if (empty($levels)) {
            wp_send_json_error(__('No reputations found!', 'bit-integrations'), 400);
        }

        foreach ($levels as $level) {
            $levelsOptions[] = (object) [
                'label' => 'Level' . ' ' . $level . ' - ' . WPF()->member->rating($level, 'title'),
                'value' => (string) $level
            ];
        }

        wp_send_json_success($levelsOptions, 200);
    }

    public function getGroups()
    {
        self::checkedDokanExists();

        $groups = WPF()->usergroup->get_usergroups();

        if (empty($groups)) {
            wp_send_json_error(__('No groups found!', 'bit-integrations'), 400);
        }

        foreach ($groups as $group) {
            $groupsOptions[] = (object) [
                'label' => $group['name'],
                'value' => (string) $group['groupid']
            ];
        }

        wp_send_json_success($groupsOptions, 200);
    }

    public function getForums()
    {
        self::checkedDokanExists();

        $forums = WPF()->forum->get_forums(['type' => 'forum']);

        if (empty($forums)) {
            wp_send_json_error(__('No forums found!', 'bit-integrations'), 400);
        }

        foreach ($forums as $forum) {
            $forumsOptions[] = (object) [
                'label' => $forum['title'],
                'value' => (string) $forum['forumid']
            ];
        }

        wp_send_json_success($forumsOptions, 200);
    }

    public function getTopics()
    {
        self::checkedDokanExists();

        $topics = WPF()->topic->get_topics();

        if (empty($topics)) {
            wp_send_json_error(__('No topics found!', 'bit-integrations'), 400);
        }

        foreach ($topics as $topic) {
            $topicList[] = (object) [
                'label' => $topic['title'],
                'value' => (string) $topic['topicid']
            ];
        }

        wp_send_json_success($topicList, 200);
    }

    public function execute($integrationData, $fieldValues)
    {
        self::checkedDokanExists();

        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $fieldMap = $integrationDetails->field_map;
        $selectedTask = $integrationDetails->selectedTask;
        $selectedReputation = $integrationDetails->selectedReputation;
        $selectedGroup = $integrationDetails->selectedGroup;
        $selectedForum = $integrationDetails->selectedForum;
        $selectedTags = $integrationDetails->selectedTags;
        $actions = $integrationDetails->actions;
        $selectedTopic = $integrationDetails->selectedTopic;

        if (empty($fieldMap) || empty($selectedTask)) {
            return new WP_Error('REQ_FIELD_EMPTY', __('Fields map, task are required for Dokan', 'bit-integrations'));
        }

        $topicOptions = [
            'selectedForum' => $selectedForum,
            'selectedTags'  => $selectedTags,
            'actions'       => $actions
        ];

        $recordApiHelper = new RecordApiHelper($integId);
        $dokanResponse = $recordApiHelper->execute($fieldValues, $fieldMap, $selectedTask, $selectedReputation, $selectedGroup, $topicOptions, $selectedTopic);

        if (is_wp_error($dokanResponse)) {
            return $dokanResponse;
        }

        return $dokanResponse;
    }
}
