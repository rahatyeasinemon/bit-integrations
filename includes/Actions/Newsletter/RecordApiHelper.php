<?php

/**
 * Newsletter Record Api
 */

namespace BitCode\FI\Actions\Newsletter;

use BitCode\FI\Core\Util\Common;
use BitCode\FI\Log\LogHandler;
use TNP;

/**
 * Provide functionality for Record insert, update
 */
class RecordApiHelper
{
    private $_integrationID;

    private $_responseType;

    public function __construct($integrationDetails, $integId)
    {
        $this->_integrationDetails = $integrationDetails;
        $this->_integrationID = $integId;
    }

    public function addSubscriber($finalData)
    {
        if (empty($finalData['email'])) {
            return ['success' => false, 'message' => 'Required field email is empty', 'code' => 400];
        }

        $try = TNP::add_subscriber($finalData);

        error_log(print_r(['try' => $try], true));
        exit;
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->newsletterFormField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }

        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap)
    {
        $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        $apiResponse = $this->addSubscriber($finalData);

        if (empty($apiResponse)) {
            $res = ['message' => 'Contact ' . $this->_responseType . ' successfully'];
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => 'contact', 'type_name' => 'Contact ' . $this->_responseType]), 'success', wp_json_encode($res));
        } else {
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => '', 'type_name' => 'Adding contact']), 'error', wp_json_encode($apiResponse));
        }

        return $apiResponse;
    }
}
