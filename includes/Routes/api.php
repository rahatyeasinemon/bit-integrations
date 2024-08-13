<?php

// If try to direct access  plugin folder it will Exit

if (!defined('ABSPATH')) {
    exit;
}
use BitCode\FI\Flow\Flow;
use BitCode\FI\Core\Util\API as Route;
use BitCode\FI\Actions\ActionController;

// use BitCode\FI\Triggers\Webhook\WebhookController;

Route::get('redirect/', [new ActionController(), 'handleRedirect'], null, ['state' => ['required' => true]]);

// FLow Execution API
Route::post('flow/execute', [new Flow(), 'flowExecute'], null, ['triggered_entity' => ['required' => true], 'triggered_entity_id' => ['required' => true], 'data' => ['required' => true], 'flows' => ['required' => true]]);
