<?php

namespace BitApps\BTCBI\Providers\Hooks;

use BitApps\BTCBI\Routes\AdminAjax;
use FilesystemIterator;
use BitApps\BTCBI\Util\Hooks;
use BitApps\BTCBI\Util\Request;
use BitApps\BTCBI\Util\StoreInCache;

class HookService
{
    private $_servicesPath;
    public function __construct()
    {
        $this->_servicesPath = BTCBI_PLUGIN_BASEDIR
        . 'backend' . DIRECTORY_SEPARATOR
        . 'app' . DIRECTORY_SEPARATOR
        . 'Http' . DIRECTORY_SEPARATOR
        . 'Services' . DIRECTORY_SEPARATOR;


        $this->loadTriggersHooks();
        $this->loadTriggersRoutes();
        $this->loadAppHooks();
        $this->loadActionsHooks();
        $this->loadAdminAjax();

        Hooks::add('rest_api_init', [$this, 'loadApi']);
    }

    /**
     * Helps to register admin side ajax
     *
     * @return null
     */
    public function loadAdminAjax()
    {
        (new AdminAjax())->register();
    }

    /**
     * Helps to register App hooks
     *
     * @return null
     */
    protected function loadAppHooks()
    {

        // echo('loadAppHooks');
        // die;
        if (Request::Check('ajax') && is_readable(BTCBI_PLUGIN_BASEDIR . 'backend' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'ajax.php')) {
            include BTCBI_PLUGIN_BASEDIR . 'backend' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'ajax.php';
        }
        if (is_readable(BTCBI_PLUGIN_BASEDIR . 'backend' . DIRECTORY_SEPARATOR . 'hooks.php')) {
            include BTCBI_PLUGIN_BASEDIR . 'backend' . DIRECTORY_SEPARATOR . 'hooks.php';
        }
    }

    /**
     * Helps to register Triggers ajax
     *
     * @return null
     */
    protected function loadTriggersHooks()
    {
        $storeInCacheInstance = new StoreInCache();
        $activeTrigger = $storeInCacheInstance::getTransientData('activeCurrentTrigger');
        if (empty($activeTrigger)) {
            $activeTrigger = $storeInCacheInstance::getActiveFlow();
        }

        if (!$activeTrigger) {
            $activeTrigger = [];
        }
        $activeTrigger[] = 'CustomTrigger';
        $activeTrigger[] = 'ActionHook';
        $activeTrigger[] = 'Spectra';
        $activeTrigger[] = 'EssentialBlocks';
        if (empty($activeTrigger) || !is_array($activeTrigger)) {
            return;
        }
        foreach ($activeTrigger as $key => $triggerName) {
            $this->_includeTriggerTaskHooks($triggerName);
        }
    }

    /**
     * Helps to register integration ajax
     *
     * @return void
     */
    public function loadActionsHooks()
    {
        $this->_includeActionTaskHooks('Actions');
    }

    /**
     * Includes Routes and Hooks
     *
     * @param string $task_name Triggers|Actions
     *
     * @return void
     */
    private function _includeTriggerTaskHooks($task_name)
    {
        $task_dir = $this->_servicesPath;

        $task_path = $task_dir . 'Triggers' . DIRECTORY_SEPARATOR . $task_name . DIRECTORY_SEPARATOR;

        if (is_readable($task_path . 'Hooks.php')) {
            include $task_path . 'Hooks.php';
        }
    }

    private function loadTriggersRoutes()
    {
        $task_dir = $this->_servicesPath
        . 'Triggers';
        $dirs = new FilesystemIterator($task_dir);
        foreach ($dirs as $dirInfo) {
            if ($dirInfo->isDir()) {
                $task_name = basename($dirInfo);
                $task_path = $task_dir . DIRECTORY_SEPARATOR . $task_name . DIRECTORY_SEPARATOR;
                if (is_readable($task_path . 'Routes.php') && Request::Check('ajax') && Request::Check('admin')) {
                    include $task_path . 'Routes.php';
                }
            }
        }
    }

    private function _includeActionTaskHooks($task_name)
    {
        // error_log(print_r($task_name));
        // error_log('sadjlkf-------------------------');
        $task_dir = BTCBI_PLUGIN_BASEDIR . 'backend' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . $task_name;
        $dirs = new FilesystemIterator($task_dir);
        foreach ($dirs as $dirInfo) {
            if ($dirInfo->isDir()) {
                $task_name = basename($dirInfo);
                $task_path = $task_dir . DIRECTORY_SEPARATOR . $task_name . DIRECTORY_SEPARATOR;

                // error_log(print_r(is_readable($task_path . 'Routes.php')));
                if (is_readable($task_path . 'Routes.php') && Request::Check('ajax') && Request::Check('admin')) {
                    include $task_path . 'Routes.php';
                }
                if (is_readable($task_path . 'Hooks.php')) {
                    include $task_path . 'Hooks.php';
                }
            }
        }
    }

    /**
     * Loads API routes
     *
     * @return null
     */
    public function loadApi()
    {
        if (is_readable(BTCBI_PLUGIN_BASEDIR . 'backend' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'api.php')) {
            include BTCBI_PLUGIN_BASEDIR . 'backend' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'api.php';
        }
    }
}
