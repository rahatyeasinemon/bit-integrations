<?php

namespace BitApps\BTCBI\Http\Services\Actions\CustomAction;

use BitApps\BTCBI\Util\Common;
use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;
use BitApps\BTCBI\Http\Services\Log\LogHandler;
use BTCBI\Deps\BitApps\WPKit\Http\Response;

class CustomActionController
{
    public static function functionValidateHandler($data)
    {
        $temp_file = tmpfile();
        fwrite($temp_file, $data);
        $filePath = stream_get_meta_data($temp_file)['uri'];
        if (function_exists('exec') === false) {
            fclose($temp_file);
            return Response::success('Exec function not found in your server, So we can\'t validate your function. But you can run your custom action.');
        }
        $response = exec(escapeshellcmd("php -l $filePath"), $output, $return);
        if (empty($response)) {
            fclose($temp_file);
            return Response::success('Exec function not found in your server, So we can\'t validate your function. But you can run your custom action.');
        }

        $msg = str_replace($filePath, 'your function', $response);
        fclose($temp_file);
        if (str_contains($response, 'No syntax errors detected')) {
            return Response::success("Congrats, $msg");
        }
        return Response::error($msg);
    }

    public function execute($integrationData, $fieldValues)
    {
        $funcFileLocation = $integrationData->flow_details->funcFileLocation;
        $integId = $integrationData->id;
        $isExits = file_exists($funcFileLocation);
        $isSuccessfullyRun = true;
        if ($isExits) {
            $trigger = (array) $fieldValues;
            try {
                include "$funcFileLocation";
            } catch (\Throwable $th) {
                $isSuccessfullyRun = false;
                LogHandler::save($integId, $th->getMessage(), 'error', 'Custom action Failed');
            }
        } else {
            LogHandler::save($integId, wp_json_encode(['type' => 'custom_action', 'type_name' => 'custom action']), 'error', wp_json_encode('Custom action file not found'));
            return;
        }
        if ($isSuccessfullyRun) {
            LogHandler::save($integId, wp_json_encode(['type' => 'custom_action', 'type_name' => 'custom action']), 'success', wp_json_encode('Custom action successfully run'));
        }

        return true;
    }
}
