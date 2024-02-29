<?php

namespace BitCode\FI\Triggers\Breakdance;

use BitCode\FI\Flow\Flow;
use Breakdance\Forms\Actions\Action;
use BitCode\FI\Triggers\Breakdance\BreakdanceHelper;

if (class_exists('Breakdance\Forms\Actions\Action')) {
    class BreakdanceAction extends Action
    {
        public static function name()
        {
            return 'Bit Integrations';
        }

        /**
         * @return string
         */
        public static function slug()
        {
            return 'bit-integrations-pro';
        }

        /**
         * @param FormData $form
         * @param FormSettings $settings
         * @param FormExtra $extra
         * @return ActionSuccess|ActionError|array<array-key, ActionSuccess|ActionError>
         */
        public function run($form, $settings, $extra)
        {
            $reOrganizeId   = "{$extra['formId']}-{$extra['postId']}";
            $formData       = BreakdanceHelper::setFields($extra, $form);

            if (get_option('btcbi_breakdance_test') !== false) {
                $testData = [
                    'formData'      => $formData,
                    'primaryKey'    => [
                        'key'   => 'formId',
                        'value' => $extra['formId']
                    ]
                ];

                update_option('btcbi_breakdance_test', $testData);
            }


            global $wpdb;
            $flows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT triggered_entity, triggered_entity_id, flow_details 
                    FROM wp_btcbi_flow 
                    WHERE status = true 
                    AND triggered_entity = 'Breakdance' 
                    AND (triggered_entity_id = 'BreakdanceHook' 
                    OR triggered_entity_id = %s)",
                    $reOrganizeId
                )
            );

            if (!$flows) {
                return;
            }

            foreach ($flows as $flow) {
                $flowDetails = json_decode($flow->flow_details);

                if (isset($flowDetails->primaryKey)) {
                    $data               = [];
                    $primaryKeyValue    = BreakdanceHelper::extractValueFromPath($extra, $flowDetails->primaryKey->key);

                    if ($flowDetails->primaryKey->value != $primaryKeyValue) continue;

                    foreach ($formData as $field) {
                        $value                  = BreakdanceHelper::extractValueFromPath($extra, $field['name']);
                        $data[$field['name']]   = $field['type'] != 'file' ? $value : explode(',', $value);
                    }

                    Flow::execute('Breakdance', 'BreakdanceHook', $data, array($flow));
                } elseif ($flow->triggered_entity_id == $reOrganizeId) {
                    Flow::execute('Breakdance', $reOrganizeId, $extra['fields'], array($flow));
                }
            }

            return ['type' => 'success'];
        }
    }
}
