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
            error_log(print_r(['path' => BreakdanceHelper::setFields($extra, $form), 'name' => 'Action', 'form' => $form, 'settings' => $settings, 'extra' => $extra], true));
            $formData = BreakdanceHelper::setFields($extra, $form);
            if (get_option('btcbi_breakdance_test') !== false) {
                update_option('btcbi_breakdance_test', $formData);
            }

            $reOrganizeId   = "{$extra['formId']}-{$extra['postId']}";

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

            // $flows          = Flow::exists('Breakdance', $reOrganizeId);
            if (!$flows) {
                return;
            }

            // $data           = $extra['fields'];
            // $data['formId'] = $extra['formId'];
            // $data['postId'] = $extra['postId'];

            Flow::execute('Breakdance', $reOrganizeId, $data, $flows);

            return ['type' => 'success'];
        }
    }
}
