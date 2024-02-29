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

            $reOrganizeId = "{$extra['formId']}-{$extra['postId']}";
            $flows = Flow::exists('Breakdance', $reOrganizeId);
            if (!$flows) {
                return;
            }

            $data = $extra['fields'];

            Flow::execute('Breakdance', $reOrganizeId, $data, $flows);

            return ['type' => 'success'];
        }
    }
}
