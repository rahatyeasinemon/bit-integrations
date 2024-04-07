<?php

namespace BitCode\FI\Triggers\MailPoet;

use DateTime;
use BitCode\FI\Flow\Flow;
use MailPoet\Form\FormsRepository;
use MailPoet\DI\ContainerWrapper;

final class MailPoetController
{
    public static function info()
    {
        $plugin_path = 'mailpoet/mailpoet.php';
        return [
            'name' => 'MailPoet',
            'title' => 'mailpoet',
            'slug' => $plugin_path,
            'pro' => 'mailpoet/mailpoet.php',
            'type' => 'form',
            'is_active' => is_plugin_active('mailpoet/mailpoet.php'),
            'activation_url' => wp_nonce_url(self_admin_url('plugins.php?action=activate&amp;plugin=' . $plugin_path . '&amp;plugin_status=all&amp;paged=1&amp;s'), 'activate-plugin_' . $plugin_path),
            'install_url' => wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_path), 'install-plugin_' . $plugin_path),
            'list' => [
                'action' => 'mailPoet/get',
                'method' => 'get',
            ],
            'fields' => [
                'action' => 'mailPoet/get/form',
                'method' => 'post',
                'data' => ['id']
            ],
        ];
    }

    public function getAll()
    {
        if (!is_plugin_active('mailpoet/mailpoet.php')) {
            wp_send_json_error(__('MailPoet is not installed or activated', 'bit-integrations'));
        }

        $formsRepository = ContainerWrapper::getInstance()->get(FormsRepository::class);
        $forms           = $formsRepository->findBy(['deletedAt' => null], ['name' => 'asc']);
        $all_forms       = [];

        if ($forms) {
            foreach ($forms as $form) {
                if ($form->getStatus() === 'enabled') {
                    $all_forms[] = (object)[
                        'id'    => $form->getId(),
                        'title' => $form->getName(),
                    ];
                }
            }
        }

        wp_send_json_success($all_forms);
    }

    public function get_a_form($data)
    {
        if (!class_exists('Forminator')) {
            wp_send_json_error(__('Forminator is not installed or activated', 'bit-integrations'));
        }
        if (empty($data->id)) {
            wp_send_json_error(__('Form doesn\'t exists', 'bit-integrations'));
        }

        $fields = self::fields($data->id);

        if (empty($fields)) {
            wp_send_json_error(__('Form doesn\'t exists any field', 'bit-integrations'));
        }

        $responseData['fields'] = $fields;
        wp_send_json_success($responseData);
    }

    public static function fields($form_id)
    {
        $formsRepository = ContainerWrapper::getInstance()->get(FormsRepository::class);
        $form            = $formsRepository->findOneById($form_id);
        $formBody        = (!empty($form->getBody()) ? $form->getBody() : []);

        if (empty($formBody)) {
            return $formBody;
        }

        $fields = [];

        foreach ($formBody as $item) {
            if ($item['type'] === 'columns') {
                $columnData = [];
                self::extractColumnData($item, $columnData);
                $fields[] = $columnData;
            } else if (isset($item['name']) && isset($item['id']) && $item['type'] !== 'submit' && $item['type'] !== 'divider' && $item['type'] !== 'segment') {
                $fields[] = [
                    'name'  => $item['id'],
                    'type'  => $item['type'],
                    'label' => $item['name'],
                ];
            }
        }

        return self::flattenArray($fields);
    }

    //forminator didn't return any kind of type of value..
    public static function handle_mailpoet_submit($data, $segmentIds, $form)
    {
        error_log(print_r($data, true));
        error_log(print_r($segmentIds, true));
        error_log(print_r($form, true));
    }
    // public static function handle_mailpoet_submit($entry, $form_id, $form_data)
    // {
    //     $post_id = url_to_postid($_SERVER['HTTP_REFERER']);

    //     if (!empty($form_id)) {
    //         $data = [];
    //         if ($post_id) {
    //             $data['post_id'] = $post_id;
    //         }
    //         foreach ($form_data as $fldDetail) {
    //             if (is_array($fldDetail['value'])) {
    //                 if (array_key_exists('file', $fldDetail['value'])) {
    //                     $data[$fldDetail['name']] = [$fldDetail['value']['file']['file_path']];
    //                 } elseif (explode("-", $fldDetail['name'])[0] == 'name') {
    //                     if ($fldDetail['name']) {
    //                         $last_dash_position = strrpos($fldDetail['name'], "-");
    //                         $index = substr($fldDetail['name'], $last_dash_position + 1);
    //                     }
    //                     foreach ($fldDetail['value'] as $nameKey => $nameVal) {
    //                         $data[$nameKey . '-' . $index] = $nameVal;
    //                     }
    //                 } elseif (explode("-", $fldDetail['name'])[0] == 'address') {
    //                     if ($fldDetail['name']) {
    //                         $last_dash_position = strrpos($fldDetail['name'], "-");
    //                         $index = substr($fldDetail['name'], $last_dash_position + 1);
    //                     }
    //                     foreach ($fldDetail['value'] as $nameKey => $nameVal) {
    //                         $data[$nameKey . '-' . $index] = $nameVal;
    //                     }
    //                 } else {
    //                     $val = $fldDetail['value'];
    //                     if (array_key_exists('ampm', $val)) {
    //                         $time = $val['hours'] . ':' . $val['minutes'] . ' ' . $val['ampm'];
    //                         $data[$fldDetail['name']] = $time;
    //                     } elseif (array_key_exists('year', $val)) {
    //                         $date = $val['year'] . '-' . $val['month'] . '-' . $val['day'];
    //                         $data[$fldDetail['name']] = $date;
    //                     } elseif (array_key_exists('formatting_result', $val)) {
    //                         $data[$fldDetail['name']] = $fldDetail['value']['formatting_result'];
    //                     } else {
    //                         $data[$fldDetail['name']] = $fldDetail['value'];
    //                     }
    //                 }
    //             } else {
    //                 if (self::isValidDate($fldDetail['value'])) {
    //                     $dateTmp = new DateTime($fldDetail['value']);
    //                     $dateFinal = date_format($dateTmp, 'Y-m-d');
    //                     $data[$fldDetail['name']] = $dateFinal;
    //                 } else {
    //                     $data[$fldDetail['name']] = $fldDetail['value'];
    //                 }
    //             }
    //         }

    //         if (!empty($form_id) && $flows = Flow::exists('Forminator', $form_id)) {
    //             Flow::execute('Forminator', $form_id, $data, $flows);
    //         }
    //     }
    // }

    public static function isValidDate($date, $format = 'd/m/Y')
    {
        $dateTime = DateTime::createFromFormat($format, $date);
        return $dateTime && $dateTime->format($format) === $date;
    }

    public static function extractColumnData($array, &$result)
    {
        foreach ($array['body'] as $item) {
            if ($item['type'] === 'column' && isset($item['body'])) {
                foreach ($item['body'] as $nestedItem) {
                    if (isset($nestedItem['name']) && isset($nestedItem['id'])) {
                        $result[] = array(
                            'name'  => $nestedItem['id'],
                            'type'  => $item['type'],
                            'label' => $nestedItem['name'],
                        );
                    }
                    if (isset($nestedItem['type']) && $nestedItem['type'] === 'columns') {
                        self::extractColumnData($nestedItem, $result);
                    }
                }
            }
        }
    }

    public static function flattenArray($array)
    {
        $result = [];
        foreach ($array as $item) {
            if (array_key_exists(0, $item) && is_array($item[0])) {
                foreach ($item as $itm) {
                    $result[] = $itm;
                }
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }
}
