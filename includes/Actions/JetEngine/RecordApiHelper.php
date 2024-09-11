<?php

/**
 * JetEngine Record Api
 */

namespace BitCode\FI\Actions\JetEngine;

use BitCode\FI\Log\LogHandler;
use BitCode\FI\Core\Util\Common;
use BitCode\FI\Core\Util\Helper;
use Jet_Engine\Modules\Custom_Content_Types\Module;

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

    public function createPostType($finalData, $createCPTSelectedOptions, $actions)
    {
        if (empty($finalData['name'])) {
            return ['success' => false, 'message' => __('Request parameters are empty!', 'bit-integrations'), 'code' => 400];
        }

        $finalData['slug'] = str_replace(' ', '-', strtolower($finalData['name']));

        if (Helper::proActionFeatExists('JetEngine', 'createPostTypeActions')) {
            $filterResponse = apply_filters('btcbi_jet_engine_create_post_type_actions', 'createPostType', $createCPTSelectedOptions, $actions);

            if ($filterResponse !== 'createPostType' && !empty($filterResponse)) {
                $finalData = array_merge($finalData, $filterResponse);
            }
        }

        jet_engine()->cpt->data->set_request($finalData);

        $postTypeId = jet_engine()->cpt->data->create_item(false);

        if (empty($postTypeId) || is_wp_error($postTypeId)) {
            return ['success' => false, 'message' => __('Failed to add post type!', 'bit-integrations'), 'code' => 400];
        }

        return ['success' => true, 'message' => __('Post type created successfully', 'bit-integrations')];
    }

    public function createContentType($finalData, $createCPTSelectedOptions, $actions)
    {
        if (!jet_engine()->modules->is_module_active('custom-content-types')) {
            return ['success' => false, 'message' => __('Module - Custom Content Type is not active!', 'bit-integrations'), 'code' => 400];
        }

        if (empty($finalData['name'])) {
            return ['success' => false, 'message' => __('Request parameters are empty!', 'bit-integrations'), 'code' => 400];
        }

        $ctcData['name'] = $finalData['name'];
        $ctcData['slug'] = str_replace(' ', '_', strtolower($finalData['name']));
        $args = $ctcData;

        if (isset($finalData['capability'])) {
            $args['capability'] = $finalData['capability'];
        }

        if (Helper::proActionFeatExists('JetEngine', 'createContentTypeActions')) {
            $filterResponse = apply_filters('btcbi_jet_engine_create_content_type_actions', 'createContentType', $createCPTSelectedOptions, $actions);

            if ($filterResponse !== 'createContentType' && !empty($filterResponse)) {
                $args = array_merge($args, $filterResponse);
            }
        }

        $ctcData['args'] = $args;
        $ctcData['meta_fields'] = [];

        Module::instance()->manager->data->set_request($ctcData);

        $itemId = Module::instance()->manager->data->create_item(false);

        if (empty($itemId) || is_wp_error($itemId)) {
            return ['success' => false, 'message' => __('Failed to add custom content type!', 'bit-integrations'), 'code' => 400];
        }

        return ['success' => true, 'message' => __('Custom content type created successfully', 'bit-integrations')];
    }

    public function createTaxonomy($finalData, $taxOptions, $actions)
    {
        if (empty($finalData['name']) || empty($taxOptions['selectedTaxPostTypes'])) {
            return ['success' => false, 'message' => __('Request parameters are empty!', 'bit-integrations'), 'code' => 400];
        }

        $finalData['slug'] = str_replace(' ', '-', strtolower($finalData['name']));
        $finalData['object_type'] = explode(',', $taxOptions['selectedTaxPostTypes']);

        if (Helper::proActionFeatExists('JetEngine', 'createTaxonomyActions')) {
            $filterResponse = apply_filters('btcbi_jet_engine_create_taxonomy_actions', 'createTaxonomy', $taxOptions, $actions);

            if ($filterResponse !== 'createTaxonomy' && !empty($filterResponse)) {
                $finalData = array_merge($finalData, $filterResponse);
            }
        }

        jet_engine()->taxonomies->data->set_request($finalData);

        $taxId = jet_engine()->taxonomies->data->create_item(false);

        if (empty($taxId) || is_wp_error($taxId)) {
            return ['success' => false, 'message' => __('Failed to add taxonomy!', 'bit-integrations'), 'code' => 400];
        }

        return ['success' => true, 'message' => __('Taxonomy added successfully', 'bit-integrations')];
    }

    public function createRelation($finalData, $relOptions, $actions)
    {
        if (empty($relOptions) || empty($relOptions['parentObject'])
        || empty($relOptions['childObject']) || empty($relOptions['selectedRelationType'])) {
            return ['success' => false, 'message' => __('Request parameters are empty!', 'bit-integrations'), 'code' => 400];
        }

        $args['parent_object'] = $relOptions['parentObject'];
        $args['child_object'] = $relOptions['childObject'];
        $args['type'] = $relOptions['selectedRelationType'];
        $args['labels'] = $finalData;

        if (Helper::proActionFeatExists('JetEngine', 'createRelationActions')) {
            $filterResponse = apply_filters('btcbi_jet_engine_create_relation_actions', 'createRelation', $relOptions, $actions);

            if ($filterResponse !== 'createRelation' && !empty($filterResponse)) {
                $args = array_merge($args, $filterResponse);
            }
        }

        jet_engine()->relations->data->set_request($args);

        $itemId = jet_engine()->relations->data->create_item(false);

        if (empty($itemId) || is_wp_error($itemId)) {
            return ['success' => false, 'message' => __('Failed to add new relation!', 'bit-integrations'), 'code' => 400];
        }

        return ['success' => true, 'message' => __('Relation added successfully', 'bit-integrations')];
    }

    public function generateReqDataFromFieldMap($data, $fieldMap)
    {
        $dataFinal = [];
        foreach ($fieldMap as $value) {
            $triggerValue = $value->formField;
            $actionValue = $value->jetEngineField;
            if ($triggerValue === 'custom') {
                $dataFinal[$actionValue] = Common::replaceFieldWithValue($value->customValue, $data);
            } elseif (!\is_null($data[$triggerValue])) {
                $dataFinal[$actionValue] = $data[$triggerValue];
            }
        }

        return $dataFinal;
    }

    public function execute($fieldValues, $fieldMap, $selectedTask, $actions, $createCPTSelectedOptions, $taxOptions, $relOptions)
    {
        if (isset($fieldMap[0]) && empty($fieldMap[0]->formField)) {
            $finalData = [];
        } else {
            $finalData = $this->generateReqDataFromFieldMap($fieldValues, $fieldMap);
        }

        $type = $typeName = '';

        if ($selectedTask === 'createPostType') {
            $response = $this->createPostType($finalData, $createCPTSelectedOptions, $actions);
            $type = 'Post Type';
            $typeName = 'Create Post Type';
        } elseif ($selectedTask === 'createContentType') {
            $response = $this->createContentType($finalData, $createCPTSelectedOptions, $actions);
            $type = 'Content Type';
            $typeName = 'Create Content Type';
        } elseif ($selectedTask === 'createTaxonomy') {
            $response = $this->createTaxonomy($finalData, $taxOptions, $actions);
            $type = 'Taxonomy';
            $typeName = 'Create Taxonomy';
        } elseif ($selectedTask === 'createRelation') {
            $response = $this->createRelation($finalData, $relOptions, $actions);
            $type = 'Relation';
            $typeName = 'Create Relation';
        }

        if ($response['success']) {
            $res = ['message' => $response['message']];
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => $type, 'type_name' => $typeName]), 'success', wp_json_encode($res));
        } else {
            LogHandler::save($this->_integrationID, wp_json_encode(['type' => $type, 'type_name' => $typeName]), 'error', wp_json_encode($response));
        }

        return $response;
    }
}
