<?php

/**
 * JetEngine Record Api
 */

namespace BitCode\FI\Actions\JetEngine;

use BitCode\FI\Core\Util\Common;
use BitCode\FI\Core\Util\Helper;
use BitCode\FI\Log\LogHandler;
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
            return ['success' => false, 'message' => 'Request parameters are empty!', 'code' => 400];
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
            return ['success' => false, 'message' => 'Failed to add post type!', 'code' => 400];
        }

        return ['success' => true, 'message' => 'Post type created successfully.'];
    }

    public function createContentType($finalData, $createCPTSelectedOptions, $actions)
    {
        if (!jet_engine()->modules->is_module_active('custom-content-types')) {
            return ['success' => false, 'message' => 'Module - Custom Content Type is not active!', 'code' => 400];
        }

        if (empty($finalData['name'])) {
            return ['success' => false, 'message' => 'Request parameters are empty!', 'code' => 400];
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
            return ['success' => false, 'message' => 'Failed to add custom content type!', 'code' => 400];
        }

        return ['success' => true, 'message' => 'Custom content type created successfully.'];
    }

    public function createTaxonomy($finalData, $taxOptions, $actions)
    {
        if (empty($finalData['name']) || empty($taxOptions['selectedTaxPostTypes'])) {
            return ['success' => false, 'message' => 'Request parameters are empty!', 'code' => 400];
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
            return ['success' => false, 'message' => 'Failed to add taxonomy!', 'code' => 400];
        }

        return ['success' => true, 'message' => 'Taxonomy added successfully.'];
    }

    public function createRelation($finalData, $relOptions, $actions)
    {
        if (empty($relOptions) || empty($relOptions['parentObject'])
        || empty($relOptions['childObject']) || empty($relOptions['selectedRelationType'])) {
            return ['success' => false, 'message' => 'Request parameters are empty!', 'code' => 400];
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
            return ['success' => false, 'message' => 'Failed to add new relation!', 'code' => 400];
        }

        return ['success' => true, 'message' => 'Relation added successfully.'];
    }

    public function updatePostType($finalData, $createCPTSelectedOptions, $actions)
    {
        if (empty($createCPTSelectedOptions) || empty($createCPTSelectedOptions['selectedCPT'])) {
            return ['success' => false, 'message' => 'Request parameters are empty!', 'code' => 400];
        }

        $id = $createCPTSelectedOptions['selectedCPT'];

        if (empty($id)) {
            return ['success' => false, 'message' => 'Custom post type id not found in request!', 'code' => 400];
        }

        $initialPostType = jet_engine()->cpt->data->get_item_for_edit($id);
        $initialSlug = $initialPostType['general_settings']['slug'];
        $initialName = $initialPostType['general_settings']['name'];

        $finalData['id'] = $id;

        if (!empty($finalData['name'])) {
            $finalData['slug'] = str_replace(' ', '-', strtolower($finalData['name']));
        }

        if (Helper::proActionFeatExists('JetEngine', 'createPostTypeActions')) {
            $filterResponse = apply_filters('btcbi_jet_engine_create_post_type_actions', 'updatePostType', $createCPTSelectedOptions, $actions);

            if ($filterResponse !== 'updatePostType' && !empty($filterResponse)) {
                $finalData = array_merge($finalData, $filterResponse);
            }
        }

        if (empty($finalData['name'])) {
            $finalData['name'] = $initialName;
        }

        if (empty($finalData['slug'])) {
            $finalData['slug'] = $initialSlug;
        }

        jet_engine()->cpt->data->set_request($finalData);

        $updated = jet_engine()->cpt->data->edit_item(false);

        if (empty($updated) || is_wp_error($updated)) {
            return ['success' => false, 'message' => 'Failed to update post type!', 'code' => 400];
        }

        if ($updated && !empty($initialSlug) && !empty($finalData['slug']) && $initialSlug !== $finalData['slug']) {
            global $wpdb;

            $wpdb->update(
                $wpdb->posts,
                [
                    'post_type' => $finalData['slug'],
                ],
                [
                    'post_type' => $initialSlug,
                ]
            );
        }

        return ['success' => true, 'message' => 'Post type updated successfully.'];
    }

    public function updateContentType($finalData, $createCPTSelectedOptions, $actions)
    {
        if (!jet_engine()->modules->is_module_active('custom-content-types')) {
            return ['success' => false, 'message' => 'Module - Custom Content Type is not active!', 'code' => 400];
        }

        $id = $createCPTSelectedOptions['selectedCCT'];

        if (empty($id)) {
            return ['success' => false, 'message' => 'Custom Content type id not found in request!', 'code' => 400];
        }

        $ctcData['id'] = $id;

        $initialContentType = Module::instance()->manager->data->get_item_for_edit($id);
        $initialSlug = $initialContentType['slug'];
        $initialName = $initialContentType['name'];

        if (!empty($finalData['name'])) {
            $ctcData['name'] = $finalData['name'];
            $ctcData['slug'] = str_replace(' ', '_', strtolower($finalData['name']));
        } else {
            $ctcData['name'] = $initialName;
            $ctcData['slug'] = $initialSlug;
        }

        $args = $ctcData;

        if (isset($finalData['capability'])) {
            $args['capability'] = $finalData['capability'];
        }

        if (Helper::proActionFeatExists('JetEngine', 'createContentTypeActions')) {
            $filterResponse = apply_filters('btcbi_jet_engine_create_content_type_actions', 'updateContentType', $createCPTSelectedOptions, $actions);

            if ($filterResponse !== 'updateContentType' && !empty($filterResponse)) {
                $args = array_merge($args, $filterResponse);
            }
        }

        $ctcData['args'] = $args;
        $ctcData['meta_fields'] = [];

        Module::instance()->manager->data->set_request($ctcData);

        $updated = Module::instance()->manager->data->edit_item(false);

        if (!$updated || is_wp_error($updated)) {
            return ['success' => false, 'message' => 'Failed to update content type!', 'code' => 400];
        }

        return ['success' => true, 'message' => 'Content type updated successfully.'];
    }

    public function updateTaxonomy($finalData, $taxOptions, $actions)
    {
        $id = $taxOptions['selectedTaxForEdit'];

        if (empty($id)) {
            return ['success' => false, 'message' => 'Taxonomy id not found in request!', 'code' => 400];
        }

        $initialTaxonomy = jet_engine()->taxonomies->data->get_item_for_edit($id);
        $initialName = $initialTaxonomy['general_settings']['name'];
        $initialSlug = $initialTaxonomy['general_settings']['slug'];

        $finalData['id'] = $id;

        if (!empty($taxOptions['selectedTaxPostTypes'])) {
            $finalData['object_type'] = explode(',', $taxOptions['selectedTaxPostTypes']);
        }

        if (!empty($finalData['name'])) {
            $finalData['slug'] = str_replace(' ', '-', strtolower($finalData['name']));
        } else {
            $finalData['name'] = $initialName;
            $finalData['slug'] = $initialSlug;
        }

        if (Helper::proActionFeatExists('JetEngine', 'createTaxonomyActions')) {
            $filterResponse = apply_filters('btcbi_jet_engine_create_taxonomy_actions', 'updateTaxonomy', $taxOptions, $actions);

            if ($filterResponse !== 'updateTaxonomy' && !empty($filterResponse)) {
                $finalData = array_merge($finalData, $filterResponse);
            }
        }

        jet_engine()->taxonomies->data->set_request($finalData);

        $updated = jet_engine()->taxonomies->data->edit_item(false);

        if (!$updated || is_wp_error($updated)) {
            return ['success' => false, 'message' => 'Failed to update taxonomy!', 'code' => 400];
        }

        return ['success' => true, 'message' => 'Taxonomy updated successfully.'];
    }

    public function updateRelation($finalData, $relOptions, $actions)
    {
        if (empty($relOptions) || empty($relOptions['parentObject'])
        || empty($relOptions['childObject']) || empty($relOptions['selectedRelationType'])) {
            return ['success' => false, 'message' => 'Request parameters are empty!', 'code' => 400];
        }

        $args['parent_object'] = $relOptions['parentObject'];
        $args['child_object'] = $relOptions['childObject'];
        $args['type'] = $relOptions['selectedRelationType'];

        if (empty($relOptions['selectedRelationForEdit'])) {
            return ['success' => false, 'message' => 'Relation id not found in request!', 'code' => 400];
        }

        $id = $relOptions['selectedRelationForEdit'];
        $initialRelation = jet_engine()->relations->data->get_item_for_edit($id);
        $labels = maybe_unserialize($initialRelation['labels']);
        $initialName = $labels['name'];
        $args['id'] = $id;

        if (empty($finalData['name'])) {
            $finalData['name'] = $initialName;
        }

        $args['labels'] = $finalData;

        if (Helper::proActionFeatExists('JetEngine', 'createRelationActions')) {
            $filterResponse = apply_filters('btcbi_jet_engine_create_relation_actions', 'updateRelation', $relOptions, $actions);

            if ($filterResponse !== 'updateRelation' && !empty($filterResponse)) {
                $args = array_merge($args, $filterResponse);
            }
        }

        jet_engine()->relations->data->set_request($args);

        $updated = jet_engine()->relations->data->edit_item(false);

        if (!$updated || is_wp_error($updated)) {
            return ['success' => false, 'message' => 'Failed to update relation!', 'code' => 400];
        }

        return ['success' => true, 'message' => 'Relation updated successfully.'];
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
        } elseif ($selectedTask === 'updatePostType') {
            $response = $this->updatePostType($finalData, $createCPTSelectedOptions, $actions);
            $type = 'Post Type';
            $typeName = 'Update Post Type';
        } elseif ($selectedTask === 'updateContentType') {
            $response = $this->updateContentType($finalData, $createCPTSelectedOptions, $actions);
            $type = 'Content Type';
            $typeName = 'Update Content Type';
        } elseif ($selectedTask === 'updateTaxonomy') {
            $response = $this->updateTaxonomy($finalData, $taxOptions, $actions);
            $type = 'Taxonomy';
            $typeName = 'Update Taxonomy';
        } elseif ($selectedTask === 'updateRelation') {
            $response = $this->updateRelation($finalData, $relOptions, $actions);
            $type = 'Relation';
            $typeName = 'Update Relation';
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
