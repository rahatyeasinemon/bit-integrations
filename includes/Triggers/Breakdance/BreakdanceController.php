<?php

namespace BitCode\FI\Triggers\Breakdance;

use WP_Error;
use BitCode\FI\Flow\Flow;
use Breakdance\Forms\Actions\Action;
use BitCode\FI\Triggers\TriggerController;
use Breakdance\Forms\Actions\ActionProvider;
use BitCode\FI\Triggers\Breakdance\BreakdanceSubmitData;

final class BreakdanceController
{
    private $instance = null;
    public static $bAllForm = [];

    public static function pluginActive($option = null)
    {
        if (is_plugin_active('breakdance/plugin.php')) {
            return $option === 'get_name' ? 'breakdance/plugin.php' : true;
        } else {
            return false;
        }
    }

    public static function addAction()
    {
        if (class_exists(__NAMESPACE__ . '\BreakdanceAction')) {
            \Breakdance\Forms\Actions\registerAction(new BreakdanceAction());
        }
    }

    public function getTestData()
    {
        return TriggerController::getTestData('breakdance');
    }

    public function removeTestData($data)
    {
        return TriggerController::removeTestData($data, 'breakdance');
    }
}
