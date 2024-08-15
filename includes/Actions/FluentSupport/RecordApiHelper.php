<?php

/**
 * Freshdesk Record Api
 */

namespace BitCode\FI\Actions\FluentSupport;

use BitCode\FI\Core\Util\Common;
use BitCode\FI\Log\LogHandler;
use Exception;
use FluentSupport\App\Models\Customer;
use FluentSupport\App\Models\Ticket;
use FluentSupport\App\Services\Helper;

/**
 * Provide functionality for Record insert, upsert
 */
class RecordApiHelper
{
    private $_integrationID;

    public function __construct($integrationId)
    {
        $this->_integrationID = $integrationId;
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];

        foreach ($fieldMap as $key => $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->fluentSupportFormField;
            if ($triggerValue === 'custom' && str_starts_with($actionValue, 'cf_')) {
                $dataFinal['custom_fields'][$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (str_starts_with($actionValue, 'cf_')) {
                $dataFinal['custom_fields'][$actionValue] = $data[$triggerValue];
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }

        return $dataFinal;
    }

    public function createCustomer($finalData, $attachments = null)
    {
        $customer = Customer::maybeCreateCustomer($finalData);

        if (isset($customer->id)) {
            $finalData['customer_id'] = $customer->id;

            return $this->createTicketByExistCustomer($finalData, $customer, $attachments);
        }
        wp_send_json_error(
            __(
                'Create Customer Failed!',
                'bit-integrations'
            ),
            400
        );
    }

    public function getCustomerExits($customer_email)
    {
        $customer = Customer::where('email', $customer_email)->first();

        return isset($customer->id) ? $customer->id : null;
    }

    public function createTicketByExistCustomer($finalData, $customer, $attachments = null)
    {
        if (!isset($finalData['mailbox_id']) || empty($finalData['mailbox_id'])) {
            $mailbox = Helper::getDefaultMailBox();
            $finalData['mailbox_id'] = $mailbox->id ?? null;
        }

        $ticket = Ticket::create($finalData);

        if (isset($ticket->id)) {
            if (isset($finalData['custom_fields']) && \is_array($finalData['custom_fields'])) {
                $ticket->syncCustomFields([$finalData['custom_fields']]);
            }

            if (!empty($attachments) && class_exists(\FluentSupport\App\Services\Tickets\TicketService::class) && class_exists(\FluentSupport\App\Models\Attachment::class)) {
                $finalData['attachments'] = static::uploadTicketFiles($attachments, $finalData['customer_id'], $this->_integrationID);
                \FluentSupport\App\Services\Tickets\TicketService::addTicketAttachments($finalData, [], $ticket, $customer);
            }

            return $ticket;
        }
        wp_send_json_error(
            __(
                'Create Ticket Failed!',
                'bit-integrations'
            ),
            400
        );
    }

    public function execute(
        $fieldValues,
        $fieldMap,
        $integrationDetails
    ) {
        $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        $customerExits = $this->getCustomerExits($finalData['email']);
        $finalData['client_priority'] = !empty($integrationDetails->actions->client_priority) ? $integrationDetails->actions->client_priority : 'normal';
        $finalData['agent_id'] = $integrationDetails->actions->support_staff;

        if (isset($integrationDetails->actions->business_inbox) && !empty($integrationDetails->actions->business_inbox)) {
            $finalData['mailbox_id'] = $integrationDetails->actions->business_inbox;
        }
        if (isset($integrationDetails->actions->attachment) && !empty($integrationDetails->actions->attachment)) {
            $attachments = $fieldValues[$integrationDetails->actions->attachment];
        }

        if ($customerExits) {
            $finalData['customer_id'] = $customerExits;
            $apiResponse = $this->createTicketByExistCustomer($finalData, $customerExits, $attachments);
        } else {
            $apiResponse = $this->createCustomer($finalData, $attachments);
        }

        if (isset($apiResponse->errors)) {
            LogHandler::save($this->_integrationID, ['type' => 'Ticket', 'type_name' => 'add-Ticket'], 'error', $apiResponse);
        } else {
            LogHandler::save($this->_integrationID, ['type' => 'Ticket', 'type_name' => 'add-Ticket'], 'success', $apiResponse);
        }

        return $apiResponse;
    }

    private static function uploadTicketFiles($files, $customerId, $flowId)
    {
        $attachments = [];
        $files = static::prepareAttachments($files, $flowId);

        foreach ($files as $file) {
            if (empty($file['file_path'])) {
                continue;
            }

            $fileData = [
                'ticket_id' => null,
                'person_id' => (int) $customerId,
                'file_type' => $file['type'],
                'file_path' => $file['file_path'],
                'full_url'  => esc_url($file['url']),
                'title'     => sanitize_file_name($file['name']),
                'driver'    => 'local',
                'status'    => 'in-active',
                'settings'  => [
                    'local_temp_path' => $file['file_path'],
                ]
            ];

            try {
                $attachment = \FluentSupport\App\Models\Attachment::create($fileData);
                $attachments[] = $attachment->file_hash;
            } catch (Exception $exception) {
                error_log($exception->getMessage());

                continue;
            }
        }

        return $attachments;
    }

    private static function prepareAttachments($files, $flowId)
    {
        $attachments = [];

        foreach ((array) $files as $file) {
            if (\is_array($file)) {
                $attachments = array_merge($attachments, static::prepareAttachments($file, $flowId));
            } else {
                $path = Common::filePath($file);
                $attachments[] = [
                    'file_path' => $path,
                    'url'       => Common::fileUrl($path),
                    'name'      => basename($path),
                    'type'      => mime_content_type($path),
                    'size'      => filesize($path),
                ];
            }
        }

        return $attachments;
    }
}
