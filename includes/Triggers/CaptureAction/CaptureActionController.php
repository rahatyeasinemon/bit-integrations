<?php

namespace BitCode\FI\Triggers\CaptureAction;

use WP_Error;
use WP_REST_Request;
use BitCode\FI\Flow\Flow;
use BitCode\FI\Core\Util\Hooks;
use BitCode\FI\Core\Util\Helper;

class CaptureActionController
{
    protected $captureActionIntegrationsList = [
        'CaptureAction',
        'KaliForms',
        'Amelia',
        'WPFunnels',
        'Typebot',
        'JetForm',
        'FluentSupport',
        'BitAssist',
    ];

    public static function info()
    {
        return [
            'name' => 'CaptureAction',
            'title' => 'Get callback data through an URL',
            'type' => 'capture_action',
            'is_active' => true
        ];
    }

    // public function getNewHook()
    // {
    //     $hook_id = wp_generate_uuid4();

    //     if (!$hook_id) {
    //         wp_send_json_error(__('Failed to generate new hook', 'bit-integrations'));
    //     }
    //     add_option('btcbi_capture_action_' . $hook_id, [], '', 'no');
    //     wp_send_json_success(['hook_id' => $hook_id]);
    // }

    public function getTestData($data)
    {
        $missing_field = null;

        if (!property_exists($data, 'hook_id')) {
            $missing_field = is_null($missing_field) ? 'CaptureAction ID' : $missing_field . ', CaptureAction ID';
        }
        if (!is_null($missing_field)) {
            wp_send_json_error(sprintf(__('%s can\'t be empty or need to be valid', 'bit-integrations'), $missing_field));
        }

        // if (has_action($data->hook_id)) {
        //     add_action($data->hook_id, [self::class, 'captureActionHandler'], 10, PHP_INT_MAX);
        //     // die;
        // }

        // add_action('forminator_custom_form_submit_before_set_fields', [self::class, 'public static function captureActionHandler'], 10, PHP_INT_MAX);

        $testData = get_option('btcbi_capture_action_test_' . $data->hook_id);
        if ($testData === false) {
            update_option('btcbi_capture_action_test_' . $data->hook_id, []);
        }
        if (!$testData || empty($testData)) {
            wp_send_json_error(new WP_Error('captureAction_test', __('CaptureAction data is empty', 'bit-integrations')));
        }
        wp_send_json_success(['captureAction' => $testData]);
    }

    public static function captureActionHandler(...$args)
    {
        // error_log(print_r(json_encode($args), true));
        // die;
        // $formatedData = self::testDataFormat($args);
        if (get_option('btcbi_capture_action_test_' . current_action()) !== false) {
            update_option('btcbi_capture_action_test_' . current_action(), $args);
        }

        // if ($flows = Flow::exists($this->captureActionIntegrationsList, $hook_id)) {
        //     Flow::execute('CaptureAction', $hook_id, $formatedData, $flows);
        // }
        return rest_ensure_response(['status' => 'success']);
    }

    public function removeTestData($data)
    {
        $missing_field = null;
        if (!property_exists($data, 'hook_id') && !empty($data->hook_id)) {
            $missing_field = is_null($missing_field) ? 'CaptureAction ID' : $missing_field . ', CaptureAction ID';
        }
        if (!is_null($missing_field)) {
            wp_send_json_error(sprintf(__('%s can\'t be empty or need to be valid', 'bit-integrations'), $missing_field));
        }

        if (property_exists($data, 'reset') && $data->reset) {
            $testData = update_option('btcbi_capture_action_test_' . $data->hook_id, []);
        } else {
            $testData = delete_option('btcbi_capture_action_test_' . $data->hook_id);
        }
        if (!$testData) {
            wp_send_json_error(new WP_Error('captureAction_test', __('Failed to remove test data', 'bit-integrations')));
        }
        wp_send_json_success(__('CaptureAction test data removed successfully', 'bit-integrations'));
    }

    // public static function makeNonNestedRecursive(array &$out, $key, array $in)
    // {
    //     foreach ($in as $k => $v) {
    //         if (is_array($v) || is_object($v)) {
    //             self::makeNonNestedRecursive($out, $key . $k . '_', (array) $v);
    //         } else {
    //             $out[$key . $k] = $v;
    //         }
    //     }
    // }

    // public static function makeNonNested(array $in)
    // {
    //     $out = [];
    //     self::makeNonNestedRecursive($out, '', $in);
    //     return $out;
    // }

    // public static function testDataFormat($data)
    // {
    //     if (!is_array($data)) {
    //         return $data;
    //     }

    //     $out = [];
    //     foreach ($data as $k => $v) {
    //         $rootAraVal = [];
    //         $flatAra = [];
    //         if (is_array($v) || is_object($v)) {
    //             $rootAraVal[$k] = json_encode($v);
    //             $flatAra = self::makeNonNested([$k => (array) $v]);
    //             $rootAraVal = array_merge($flatAra, $rootAraVal);
    //         } else {
    //             $rootAraVal[$k] = $v;
    //         }
    //         $out = array_merge($out, $rootAraVal);
    //     }
    //     return $out;
    // }

    public static function handle(...$args)
    {
        // $data = (array) $request->get_headers();
        // $content_type = is_array($request->get_content_type()) ? $request->get_content_type()['value'] : '';
        // if (Helper::isJson($request->get_body())) {
        //     $data = $data + (array) json_decode($request->get_body(), true);
        // } else {
        //     $data = array_merge($data, (array) $request->get_body_params());
        // }

        // $data = (array) $data + $request->get_query_params();

        // if (is_array($request->get_file_params())) {
        //     $data = (array) $data + $request->get_file_params();
        // }
        // $hook_id = $request->get_params()['hook_id'];
        // unset($data['hook_id']);

        // $formatedData = self::testDataFormat($data);
        // if (get_option('btcbi_capture_action_' . $hook_id) !== false) {
        //     update_option('btcbi_capture_action_' . $hook_id, $formatedData);
        // }

        // if ($flows = Flow::exists($this->captureActionIntegrationsList, $hook_id)) {
        //     Flow::execute('CaptureAction', $hook_id, $formatedData, $flows);
        // }
        // return rest_ensure_response(['status' => 'success']);

        if ($flows = Flow::exists('CaptureAction', current_action())) {
            $fieldKeys = [];
            foreach (json_decode($flows[0]->flow_details)->body->data as $field) {
                $fieldKeys[] = $field->key;
            }
            $formatedData = [];
            foreach ($fieldKeys as $key) {
                $formatedData[$key] = self::extractValueFromPath($args, $key);
            }
            // error_log(print_r([$formatedData, $fieldKeys, $flows, $args], true));
            // die;
            Flow::execute('CaptureAction', current_action(), $formatedData, $flows);
        }

        return rest_ensure_response(['status' => 'success']);
    }

    private static function extractValueFromPath($json, $path)
    {
        // Check if path is already an array or convert it from string
        $parts = is_array($path) ? $path : explode('.', $path);

        // Base case: if there are no more parts left, return the current JSON part
        if (count($parts) === 0) {
            return $json;
        }

        // Take the first part of the path
        $currentPart = array_shift($parts);

        // If the current JSON part is an array, currentPart should be an index
        if (is_array($json)) {
            // Validate the index
            if (!isset($json[$currentPart])) {
                return 'Index out of bounds or invalid';
            }
            return self::extractValueFromPath($json[$currentPart], $parts);
        }

        // If the current JSON part is an object, currentPart should be a property or key
        if (is_object($json)) {
            // Validate the property
            if (!property_exists($json, $currentPart)) {
                return 'Invalid path';
            }
            return self::extractValueFromPath($json->$currentPart, $parts);
        }

        // If we reach here, it means the path is invalid
        return 'Invalid path';
    }
}
