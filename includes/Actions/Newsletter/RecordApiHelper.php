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

    public function __construct($integId)
    {
        $this->_integrationID = $integId;
    }

    public function addSubscriber($finalData, $selectedLists)
    {
        if (empty($finalData['email'])) {
            return ['success' => false, 'message' => 'Required field email is empty', 'code' => 400];
        }

        if (!empty($selectedLists)) {
            $finalData['lists'] = explode(',', $selectedLists);
        }

        return TNP::add_subscriber($finalData);
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

    public function execute($fieldValues, $fieldMap, $selectedLists)
    {
        $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        $response = $this->addSubscriber($finalData, $selectedLists);

        if (isset($response->id)) {
            $res = ['message' => 'Subscriber added successfully'];
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => 'subscriber', 'type_name' => 'Subscriber add']), 'success', wp_json_encode($res));
        } else {
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => '', 'type_name' => 'Adding subscriber']), 'error', wp_json_encode($response));
        }

        return $response;
    }
}
