<?php

/**
 * WhatsApp Integration
 */

namespace BitApps\BTCBI\Http\Services\Actions\WhatsApp;

use WP_Error;
use BTCBI\Deps\BitApps\WPKit\Http\Client\Http;

/**
 * Provide functionality for Trello integration
 */
class WhatsAppController
{
    private $baseUrl = 'https://api.trello.com/1/';
    private $_integrationID;
    private $accessToken;

    // public function fetchAllBoards($queryParams)
    // {
    //     if (
    //         empty($queryParams->accessToken)
    //         || empty($queryParams->clientId)
    //     ) {
    //         return Response::error(
    //             __(
    //                 'Requested parameter is empty',
    //                 'bit-integrations'
    //             ),
    //             400
    //         );
    //     }
    //     $response = [];
    //     $apiEndpoint = $this->baseUrl . 'members/me?key=' . $queryParams->clientId . '&token=' . $queryParams->accessToken;
    //     $getUserInfoResponse = Http::request($apiEndpoint, 'Get', null);
    //     $apiEndpoint = $this->baseUrl . 'members/' . $getUserInfoResponse->username . '/boards?key=' . $queryParams->clientId . '&token=' . $queryParams->accessToken;
    //     $allBoardResponse = Http::request($apiEndpoint, 'Get', null);

    //     $allList = [];
    //     if (!is_wp_error($allBoardResponse) && empty($allBoardResponse->response->error)) {
    //         $boardLists = $allBoardResponse;
    //         foreach ($boardLists as $boardList) {
    //             $allList[] = (object) [
    //                 'boardId' => $boardList->id,
    //                 'boardName' => $boardList->name
    //             ];
    //         }
    //         uksort($allList, 'strnatcasecmp');
    //         $response['allBoardlist'] = $allList;
    //     } else {
    //         return Response::error(
    //             $allBoardResponse->response->error->message,
    //             400
    //         );
    //     }
    //     return Response::success($response);
    // }

    // public function fetchAllLists($queryParams)
    // {
    //     if (
    //         empty($queryParams->accessToken)
    //         || empty($queryParams->clientId)
    //     ) {
    //         return Response::error(
    //             __(
    //                 'Requested parameter is empty',
    //                 'bit-integrations'
    //             ),
    //             400
    //         );
    //     }
    //     $response = [];

    //     $apiEndpoint = $this->baseUrl . 'boards/' . $queryParams->boardId . '/lists?key=' . $queryParams->clientId . '&token=' . $queryParams->accessToken;
    //     $getListsResponse = Http::request($apiEndpoint, 'Get', null);

    //     $allList = [];
    //     if (!is_wp_error($getListsResponse) && empty($getListsResponse->response->error)) {
    //         $singleBoardLists = $getListsResponse;
    //         foreach ($singleBoardLists as $singleBoardList) {
    //             $allList[] = (object) [
    //                 'listId' => $singleBoardList->id,
    //                 'listName' => $singleBoardList->name
    //             ];
    //         }
    //         uksort($allList, 'strnatcasecmp');
    //         $response['alllists'] = $allList;
    //     } else {
    //         return Response::error(
    //             $allBoardResponse->response->error->message,
    //             400
    //         );
    //     }
    //     return Response::success($response);
    // }

    /**
     * Save updated access_token to avoid unnecessary token generation
     *
     * @param Object $integrationData Details of flow
     * @param Array  $fieldValues     Data to send Mail Chimp
     *
     * @return null
     */
    public function execute($integrationData, $fieldValues)
    {
        $integrationDetails = $integrationData->flow_details;
        $integId = $integrationData->id;
        $messageTypeId = $integrationDetails->messageTypeId;
        $fieldMap = $integrationDetails->field_map;
        $actions = $integrationDetails->actions;

        if (
            empty($messageTypeId) ||
            empty($fieldMap)
        ) {
            return new WP_Error('REQ_FIELD_EMPTY', __('module, fields are required for WhatsApp api', 'bit-integrations'));
        }
        $recordApiHelper = new RecordApiHelper($integrationDetails, $integId);
        $whatsAppApiResponse = $recordApiHelper->execute(
            $fieldValues,
            $fieldMap,
            $messageTypeId,
            $actions
        );

        if (is_wp_error($whatsAppApiResponse)) {
            return $whatsAppApiResponse;
        }
        return $whatsAppApiResponse;
    }
}
