<?php

namespace BitCode\FI\Core\Util;

use DateTime;
use Groundhogg\DB\Tags;
use BitCode\FI\Flow\Flow;
use WP_Error;

final class TriggerFallback
{
    //PaidMemberShip all functions
    public static function perchesMembershhipLevelByAdministator($level_id, $user_id, $cancel_level)
    {
        if ($level_id == 0) {
            return;
        }
        global $wpdb;
        $levels = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = %d", $level_id));
        $userData = self::paidMembershipProgetUserInfo($user_id);
        $finalData = array_merge($userData, (array)$levels[0]);
        $flows = Flow::exists('PaidMembershipPro', 1);
        if (!$flows) {
            return;
        }
        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedMembershipLevel = !empty($flowDetails->selectedMembershipLevel) ? $flowDetails->selectedMembershipLevel : [];
        if ($level_id === $selectedMembershipLevel || $selectedMembershipLevel === 'any') {
            return ['triggered_entity' => 'PaidMembershipPro', 'triggered_entity_id' => 1, 'data' => $finalData, 'flows' => $flows];
        }
        return ;
    }

    public static function paidMembershipProgetUserInfo($user_id)
    {
        $userInfo = get_userdata($user_id);
        $user = [];
        if ($userInfo) {
            $userData = $userInfo->data;
            $user_meta = get_user_meta($user_id);
            $user = [
                'user_id' => $user_id,
                'first_name' => $user_meta['first_name'][0],
                'last_name' => $user_meta['last_name'][0],
                'user_email' => $userData->user_email,
                'nickname' => $userData->user_nicename,
                'avatar_url' => get_avatar_url($user_id),
            ];
        }
        return $user;
    }

    public static function cancelMembershhipLevel($level_id, $user_id, $cancel_level)
    {
        if (0 !== absint($level_id)) {
            return;
        }
        global $wpdb;
        $levels = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = %d", $cancel_level));
        $userData = self::paidMembershipProgetUserInfo($user_id);
        $finalData = array_merge($userData, (array)$levels[0]);
        $flows = Flow::exists('PaidMembershipPro', 2);
        if (!$flows) {
            return;
        }
        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedMembershipLevel = !empty($flowDetails->selectedMembershipLevel) ? $flowDetails->selectedMembershipLevel : [];
        if (($cancel_level == $selectedMembershipLevel || $selectedMembershipLevel === 'any')) {
            return ['triggered_entity' => 'PaidMembershipPro', 'triggered_entity_id' => 2, 'data' => $finalData, 'flows' => $flows];
        }
        return;
    }

    public static function perchesMembershipLevel($user_id, $morder)
    {
        $user = $morder->getUser();
        $membership = $morder->getMembershipLevel();
        $user_id = $user->ID;
        $membership_id = $membership->id;

        global $wpdb;
        $levels = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = %d", $membership_id));
        $userData = self::paidMembershipProgetUserInfo($user_id);
        $finalData = array_merge($userData, (array)$levels[0]);
        $flows = Flow::exists('PaidMembershipPro', 3);
        if (!$flows) {
            return;
        }
        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedMembershipLevel = !empty($flowDetails->selectedMembershipLevel) ? $flowDetails->selectedMembershipLevel : [];
        if (($membership_id == $selectedMembershipLevel || $selectedMembershipLevel === 'any')) {
            return ['triggered_entity' => 'PaidMembershipPro', 'triggered_entity_id' => 3, 'data' => $finalData, 'flows' => $flows];
        }

        return;
    }

    public static function expiryMembershipLevel($user_id, $membership_id)
    {
        global $wpdb;
        $levels = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = %d", $membership_id));
        $userData = self::paidMembershipProgetUserInfo($user_id);
        $finalData = array_merge($userData, (array)$levels[0]);
        $flows = Flow::exists('PaidMembershipPro', 4);
        if (!$flows) {
            return;
        }
        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedMembershipLevel = !empty($flowDetails->selectedMembershipLevel) ? $flowDetails->selectedMembershipLevel : [];
        if (($membership_id == $selectedMembershipLevel || $selectedMembershipLevel === 'any')) {
            return ['triggered_entity' => 'PaidMembershipPro', 'triggered_entity_id' => 4, 'data' => $finalData, 'flows' => $flows];
        }
        return;
    }

    //PiotnetAddon all functions
    public static function handlePiotnetAddonSubmit($form_submission)
    {

        $form_id = $form_submission['form']['id'];

        $flows = Flow::exists('PiotnetAddon', $form_id);
        if (!$flows) {
            return;
        }

        $data = [];
        $fields = $form_submission['fields'];
        foreach ($fields as $key => $field) {
            $data[$key] = $field['value'];
        }

        return ['triggered_entity' => 'PiotnetAddon', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];

    }

    //PiotnetAddonForm all functions
    public static function handlePiotnetAddonFormSubmit($form_submission)
    {
        $form_id = $form_submission['form']['id'];


        $flows = Flow::exists('PiotnetAddonForm', $form_id);
        if (!$flows) {
            return;
        }

        $data = [];
        $fields = $form_submission['fields'];
        foreach ($fields as $key => $field) {
            $data[$key] = $field['value'];
        }

        return ['triggered_entity' => 'PiotnetAddonForm', 'triggered_entity_id' => $form_id, 'data' => $data, 'flows' => $flows];

    }

    //PiotnetForms all functions
    public static function handlePiotnetSubmit($fields)
    {
        $post_id = $_REQUEST['post_id'];

        $flows = Flow::exists('PiotnetForms', $post_id);
        if (!$flows) {
            return;
        }

        $data = [];
        foreach ($fields as $field) {
            if ((key_exists('type', $field) && ($field['type'] == 'file' || $field['type'] == 'signature')) || (key_exists('image_upload', $field) && $field['image_upload'] > 0)) {
                $field['value'] = Common::filePath($field['value']);
            }
            $data[$field['name']] = $field['value'];
        }

        return ['triggered_entity' => 'PiotnetAddonForm', 'triggered_entity_id' => $post_id, 'data' => $data, 'flows' => $flows];

    }

    //Post all functions
    public static function createPost($postId, $newPostData, $update, $beforePostData)
    {
        if ('publish' !== $newPostData->post_status || 'revision' === $newPostData->post_type || (!empty($beforePostData->post_status) && 'publish' === $beforePostData->post_status)) {
            return false;
        }

        $postCreateFlow = Flow::exists('Post', 1);
        if (!$postCreateFlow) {
            return;
        }
        if ($postCreateFlow) {
            $flowDetails = $postCreateFlow[0]->flow_details;

            if (is_string($postCreateFlow[0]->flow_details)) {
                $flowDetails = json_decode($postCreateFlow[0]->flow_details);
            }

            if (isset($newPostData->post_content)) {
                $newPostData->post_content = trim(strip_tags($newPostData->post_content));
                $newPostData->post_permalink = get_permalink($newPostData);
            }

            if (isset($flowDetails->selectedPostType) && ($flowDetails->selectedPostType == 'any-post-type' || $flowDetails->selectedPostType == $newPostData->post_type)) {
                if (has_post_thumbnail($postId)) {
                    $featured_image_url = get_the_post_thumbnail_url($postId, 'full');
                    $newPostData->featured_image = $featured_image_url;
                }
                if (!$update) {
                    return ['triggered_entity' => 'Post', 'triggered_entity_id' => 1, 'data' => (array) $newPostData, 'flows' => $postCreateFlow];
                } else {
                    return ['triggered_entity' => 'Post', 'triggered_entity_id' => 1, 'data' => (array) $newPostData, 'flows' => $postCreateFlow];
                }
                return;
            }
        }
    }

    public static function postComment($cmntId, $status, $cmntData)
    {
        $cmntTrigger = Flow::exists('Post', 5);
        if (!$cmntTrigger) {
            return;
        }
        if ($cmntTrigger) {
            $flowDetails = $cmntTrigger[0]->flow_details;

            if (is_string($cmntTrigger[0]->flow_details)) {
                $flowDetails = json_decode($cmntTrigger[0]->flow_details);
            }

            if (isset($flowDetails->selectedPostId) && $flowDetails->selectedPostId == 'any-post' || $flowDetails->selectedPostId == $cmntData['comment_post_ID']) {
                $cmntData['comment_id'] = $cmntId;
                return ['triggered_entity' => 'Post', 'triggered_entity_id' => 5, 'data' => (array) $cmntData, 'flows' => $cmntTrigger];
            }
        }
    }

    public static function postUpdated($postId, $updatedPostData)
    {
        $postUpdateFlow = Flow::exists('Post', 2);
        if (!$postUpdateFlow) {
            return;
        }
        if ($postUpdateFlow) {
            $flowDetails = $postUpdateFlow[0]->flow_details;
            if (is_string($postUpdateFlow[0]->flow_details)) {
                $flowDetails = json_decode($postUpdateFlow[0]->flow_details);
            }
            if (isset($updatedPostData->post_content)) {
                $updatedPostData->post_content = trim(strip_tags($updatedPostData->post_content));
                $updatedPostData->post_permalink = get_permalink($updatedPostData);
            }

            if (isset($flowDetails->selectedPostType) && $flowDetails->selectedPostType == 'any-post-type' || $flowDetails->selectedPostType == $updatedPostData->post_type) {
                if (has_post_thumbnail($postId)) {
                    $featured_image_url = get_the_post_thumbnail_url($postId, 'full');
                    $updatedPostData->featured_image = $featured_image_url;
                }
                return ['triggered_entity' => 'Post', 'triggered_entity_id' => 2, 'data' => (array) $updatedPostData, 'flows' => $postUpdateFlow];
            }
            return;
        }
    }


    public static function viewPost($content)
    {
        $postViewTrigger = Flow::exists('Post', 4);
        if(!$postViewTrigger) {
            return $content;
        }
        if (is_single() && !empty($GLOBALS['post'])) {
            if (isset($postViewTrigger[0]->selectedPostId) && $postViewTrigger[0]->selectedPostId == 'any-post' || $GLOBALS['post']->ID == get_the_ID()) {
                return ['triggered_entity' => 'Post', 'triggered_entity_id' => 5, 'data' => (array) $GLOBALS['post'], 'flows' => $postViewTrigger,'content' => $content];
            }
        }

        return $content;
    }

    public static function deletePost($postId, $deletedPost)
    {
        $postDeleteTrigger = Flow::exists('Post', 3);
        if(!$postDeleteTrigger) {
            return;
        }

        if ($postDeleteTrigger) {
            $flowDetails = $postDeleteTrigger[0]->flow_details;

            if (is_string($postDeleteTrigger[0]->flow_details)) {
                $flowDetails = json_decode($postDeleteTrigger[0]->flow_details);
            }

            if (isset($deletedPost->post_content)) {
                $deletedPost->post_content = trim(strip_tags($deletedPost->post_content));
                $deletedPost->post_permalink = get_permalink($deletedPost);
            }

            if (isset($flowDetails->selectedPostType) && $flowDetails->selectedPostType == 'any-post-type' || $flowDetails->selectedPostType == $deletedPost->post_type) {
                return ['triggered_entity' => 'Post', 'triggered_entity_id' => 5, 'data' => (array) $deletedPost, 'flows' => $postDeleteTrigger];
            }
            return;
        }
    }

    public static function changePostStatus($newStatus, $oldStatus, $post)
    {
        $statusChangeTrigger = Flow::exists('Post', 6);
        if(!$statusChangeTrigger) {
            return;
        }
        if ($statusChangeTrigger) {
            $flowDetails = $statusChangeTrigger[0]->flow_details;

            if (is_string($statusChangeTrigger[0]->flow_details)) {
                $flowDetails = json_decode($statusChangeTrigger[0]->flow_details);
            }

            if (isset($post->post_content)) {
                $post->post_content = trim(strip_tags($post->post_content));
                $post->post_permalink = get_permalink($post);
            }
            if (has_post_thumbnail($post->id)) {
                $post->featured_image = get_the_post_thumbnail_url($post->id, 'full');
            }

            if (isset($flowDetails->selectedPostType) && $flowDetails->selectedPostType == 'any-post-type' || $flowDetails->selectedPostType == $post->post_type && $newStatus != $oldStatus) {
                return ['triggered_entity' => 'Post', 'triggered_entity_id' => 6, 'data' => (array) $post, 'flows' => $statusChangeTrigger];
            }
            return;
        }
    }

    public static function trashComment($cmntId, $cmntData)
    {
        $cmntTrigger = Flow::exists('Post', 7);

        if(!$cmntTrigger) {
            return;
        }

        if ($cmntTrigger) {
            $flowDetails = $cmntTrigger[0]->flow_details;

            if (is_string($cmntTrigger[0]->flow_details)) {
                $flowDetails = json_decode($cmntTrigger[0]->flow_details);
            }

            $cmntData = (array)$cmntData;
            if (isset($flowDetails->selectedPostId) && $flowDetails->selectedPostId == 'any-post' || $flowDetails->selectedPostId == $cmntData['comment_post_ID']) {
                $cmntData['comment_id'] = $cmntId;
                return ['triggered_entity' => 'Post', 'triggered_entity_id' => 7, 'data' => (array) $cmntData, 'flows' => $cmntTrigger];
            }
            return;
        }
    }

    public static function updateComment($cmntId, $cmntData)
    {
        $cmntTrigger = Flow::exists('Post', 8);

        if(!$cmntTrigger) {
            return;
        }

        if ($cmntTrigger) {
            $flowDetails = $cmntTrigger[0]->flow_details;

            if (is_string($cmntTrigger[0]->flow_details)) {
                $flowDetails = json_decode($cmntTrigger[0]->flow_details);
            }

            $cmntData = (array)$cmntData;
            if (isset($flowDetails->selectedPostId) && $flowDetails->selectedPostId == 'any-post' || $flowDetails->selectedPostId == $cmntData['comment_post_ID']) {
                $cmntData['comment_id'] = $cmntId;
                return ['triggered_entity' => 'Post', 'triggered_entity_id' => 8, 'data' => (array) $cmntData, 'flows' => $cmntTrigger];
            }
            return;
        }
    }

    public static function trashPost($trashPostId)
    {
        $postUpdateFlow = Flow::exists('Post', 9);

        if(!$postUpdateFlow) {
            return;
        }

        $postData = get_post($trashPostId);
        $postData->post_permalink = get_permalink($postData);

        if ($postUpdateFlow) {
            $flowDetails = $postUpdateFlow[0]->flow_details;

            if (is_string($postUpdateFlow[0]->flow_details)) {
                $flowDetails = json_decode($postUpdateFlow[0]->flow_details);
            }
            $postData = (array)$postData;
            if (isset($flowDetails->selectedPostType) && $flowDetails->selectedPostType == 'any-post-type' || $flowDetails->selectedPostType == $postData['ID']) {
                return ['triggered_entity' => 'Post', 'triggered_entity_id' => 9, 'data' => (array) $postData, 'flows' => $postUpdateFlow];
            }
            return;
        }
    }

    //WordPress User Registration all functions
    public static function userCreate()
    {
        $newUserData = func_get_args()[1];

        $userCreateFlow = Flow::exists('Registration', 1);

        if ($userCreateFlow) {
            return ['triggered_entity' => 'Registration', 'triggered_entity_id' => 1, 'data' => $newUserData, 'flows' => $userCreateFlow];
        }
        return;
    }

    public static function profileUpdate()
    {
        $userdata = func_get_args()[2];

        $userUpdateFlow = Flow::exists('Registration', 2);

        if ($userUpdateFlow) {
            return ['triggered_entity' => 'Registration', 'triggered_entity_id' => 2, 'data' => $userdata, 'flows' => $userUpdateFlow];
        }
        return;
    }

    public static function wpLogin($userId, $data)
    {
        $userLoginFlow = Flow::exists('Registration', 3);

        if ($userLoginFlow) {
            $user = [];

            if (isset($data->data)) {
                $user['user_id'] = $userId;
                $user['user_login'] = $data->data->user_login;
                $user['user_email'] = $data->data->user_email;
                $user['user_url'] = $data->data->user_url;
                $user['nickname'] = $data->data->user_nicename;
                $user['display_name'] = $data->data->display_name;
            }
            return ['triggered_entity' => 'Registration', 'triggered_entity_id' => 3, 'data' => $user, 'flows' => $userLoginFlow];
        }
        return;
    }

    public static function wpResetPassword($data)
    {
        $userResetPassFlow = Flow::exists('Registration', 4);

        if ($userResetPassFlow) {
            $user = [];
            if (isset($data->data)) {
                $user['user_id'] = $data->data->ID;
                $user['user_login'] = $data->data->user_login;
                $user['user_email'] = $data->data->user_email;
                $user['user_url'] = $data->data->user_url;
                $user['nickname'] = $data->data->user_nicename;
                $user['display_name'] = $data->data->display_name;
            }

            return ['triggered_entity' => 'Registration', 'triggered_entity_id' => 4, 'data' => $user, 'flows' => $userResetPassFlow];
        }
        return;
    }

    public static function wpUserDeleted()
    {
        $data = func_get_args()[2];

        $userDeleteFlow = Flow::exists('Registration', 5);

        if ($userDeleteFlow) {
            $user = [];
            if (isset($data->data)) {
                $user['user_id'] = $data->data->ID;
                $user['user_login'] = $data->data->user_login;
                $user['user_email'] = $data->data->user_email;
                $user['user_url'] = $data->data->user_url;
                $user['nickname'] = $data->data->user_nicename;
                $user['display_name'] = $data->data->display_name;
            }

            return ['triggered_entity' => 'Registration', 'triggered_entity_id' => 5, 'data' => $user, 'flows' => $userDeleteFlow];
        }
        return;
    }

    //RestrictContent all functions
    public static function rcpPurchasesMembershipLevel($membership_id, \RCP_Membership $RCP_Membership)
    {
        $flows = Flow::exists('RestrictContent', 1);
        if (!$flows) {
            return;
        }
        $user_id = $RCP_Membership->get_user_id();

        if (!$user_id) {
            return;
        }
        $level_id = $RCP_Membership->get_object_id();

        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
                $flowDetails = $flow->flow_details;
            }
        }

        if ($level_id == $flowDetails->selectedMembership || 'any' == $flowDetails->selectedMembership) {
            $organizedData = [];
            if ($membership_id) {
                $membership = rcp_get_membership($membership_id);
                if (false !== $membership) {
                    $organizedData = [
                        'membership_level' => $membership->get_membership_level_name(),
                        'membership_payment' => $membership->get_initial_amount(),
                        'membership_recurring_payment' => $membership->get_recurring_amount(),
                    ];
                }
            }

            return ['triggered_entity' => 'RestrictContent', 'triggered_entity_id' => 1, 'data' => $organizedData, 'flows' => $flows];
        }

    }

    public static function rcpMembershipStatusExpired($old_status, $membership_id)
    {
        $flows = Flow::exists('RestrictContent', 2);
        if (!$flows) {
            return;
        }
        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
                $flowDetails = $flow->flow_details;
            }
        }
        $membership = rcp_get_membership($membership_id);
        $membership_level = rcp_get_membership_level($membership->get_object_id());
        $level_id = (string)$membership_level->get_id();

        if ($level_id == $flowDetails->selectedMembership || 'any' == $flowDetails->selectedMembership) {
            $organizedData = [];

            if ($membership_id) {
                $membership = rcp_get_membership($membership_id);

                if (false !== $membership) {
                    $organizedData = [
                        'membership_level' => $membership->get_membership_level_name(),
                        'membership_payment' => $membership->get_initial_amount(),
                        'membership_recurring_payment' => $membership->get_recurring_amount(),
                    ];
                }
            }

            return ['triggered_entity' => 'RestrictContent', 'triggered_entity_id' => 2, 'data' => $organizedData, 'flows' => $flows];
        }
    }

    public static function rcpMembershipStatusCancelled($old_status, $membership_id)
    {
        $flows = Flow::exists('RestrictContent', 3);
        if (!$flows) {
            return;
        }

        $organizedData = [];
        $membership = rcp_get_membership($membership_id);
        $membership_level = rcp_get_membership_level($membership->get_object_id());
        $level_id = $membership_level->get_id();

        foreach ($flows as $flow) {
            if (is_string($flow->flow_details)) {
                $flow->flow_details = json_decode($flow->flow_details);
                $flowDetails = $flow->flow_details;
            }
        }

        if ($level_id == $flowDetails->selectedMembership || 'any' == $flowDetails->selectedMembership) {
            if ($membership_id) {
                $membership = rcp_get_membership($membership_id);

                if (false !== $membership) {
                    $organizedData = [
                        'membership_level' => $membership->get_membership_level_name(),
                        'membership_payment' => $membership->get_initial_amount(),
                        'membership_recurring_payment' => $membership->get_recurring_amount(),
                    ];
                }
            }

            return ['triggered_entity' => 'RestrictContent', 'triggered_entity_id' => 3, 'data' => $organizedData, 'flows' => $flows];
        }
    }

    //SliceWp all functions
    public static function newAffiliateCreated($affiliate_id, $affiliate_data)
    {
        $userData = self::sliceWpgetUserInfo($affiliate_data['user_id']);
        $finalData = $affiliate_data + $userData + ['affiliate_id' => $affiliate_id];

        $flows = Flow::exists('SliceWp', 1);
        if(!$flows) {
            return;
        }

        if (!$affiliate_data['user_id'] || !$flows) {
            return;
        }
        return ['triggered_entity' => 'SliceWp', 'triggered_entity_id' => 1, 'data' => $finalData, 'flows' => $flows];
    }

    public static function userEarnCommission($commission_id, $commission_data)
    {
        $finalData = $commission_data + ['commission_id' => $commission_id];
        $flows = Flow::exists('SliceWp', 2);
        if(!$flows) {
            return;
        }
        $flowDetails = json_decode($flows[0]->flow_details);
        $selectedCommissionType = !empty($flowDetails->selectedCommissionType) ? $flowDetails->selectedCommissionType : [];

        if (($commission_data['type'] == $selectedCommissionType || $selectedCommissionType === 'any')) {
            return ['triggered_entity' => 'SliceWp', 'triggered_entity_id' => 2, 'data' => $finalData, 'flows' => $flows];
        }
        return;
    }

    public static function sliceWpgetUserInfo($user_id)
    {
        $userInfo = get_userdata($user_id);
        $user = [];
        if ($userInfo) {
            $userData = $userInfo->data;
            $user_meta = get_user_meta($user_id);
            $user = [
                'user_id' => $user_id,
                'first_name' => $user_meta['first_name'][0],
                'last_name' => $user_meta['last_name'][0],
                'user_email' => $userData->user_email,
                'nickname' => $userData->user_nicename,
                'avatar_url' => get_avatar_url($user_id),
            ];
        }
        return $user;
    }

    //NewSolidAffiliate all functions
    public static function newSolidAffiliateCreated($affiliate)
    {
        $attributes = $affiliate->__get('attributes');

        $flows = Flow::exists('SolidAffiliate', 1);
        if (!$flows) {
            return;
        }

        return ['triggered_entity' => 'SolidAffiliate', 'triggered_entity_id' => 1, 'data' => $attributes, 'flows' => $flows];
    }

    public static function newSolidAffiliateReferralCreated($referral_accepted)
    {
        $affiliateReferralData = $referral_accepted->__get('attributes');
        $flows = Flow::exists('SolidAffiliate', 2);
        if (!$flows) {
            return;
        }
        return ['triggered_entity' => 'SolidAffiliate', 'triggered_entity_id' => 2, 'data' => $affiliateReferralData, 'flows' => $flows];
    }

    //Spectra all functions
    public static function spectraHandler(...$args)
    {
        if (get_option('btcbi_test_uagb_form_success') !== false) {
            update_option('btcbi_test_uagb_form_success', $args);
        }

        if ($flows = Flow::exists('Spectra', current_action())) {

            foreach ($flows as $flow) {
                $flowDetails = json_decode($flow->flow_details);
                if (!isset($flowDetails->primaryKey)) {
                    continue;
                }

                $primaryKeyValue = self::extractValueFromPath($args, $flowDetails->primaryKey->key);
                if ($flowDetails->primaryKey->value === $primaryKeyValue) {
                    $fieldKeys      = [];
                    $formatedData   = [];

                    if ($flowDetails->body->data && is_array($flowDetails->body->data)) {
                        $fieldKeys = array_map(function ($field) use ($args) {
                            return $field->key;
                        }, $flowDetails->body->data);
                    } elseif (isset($flowDetails->field_map) && is_array($flowDetails->field_map)) {
                        $fieldKeys = array_map(function ($field) use ($args) {
                            return $field->formField;
                        }, $flowDetails->field_map);
                    }

                    foreach ($fieldKeys as $key) {
                        $formatedData[$key] = self::extractValueFromPath($args, $key);
                    }
                    return ['triggered_entity' => 'Spectra', 'triggered_entity_id' => current_action(), 'data' => $formatedData, 'flows' => array($flow)];
                }
            }
        }

        return rest_ensure_response(['status' => 'success']);
    }

    private static function extractValueFromPath($data, $path)
    {
        $parts = is_array($path) ? $path : explode('.', $path);
        if (count($parts) === 0) {
            return $data;
        }

        $currentPart = array_shift($parts);

        if (is_array($data)) {
            if (!isset($data[$currentPart])) {
                wp_send_json_error(new WP_Error('Spectra', __('Index out of bounds or invalid', 'bit-integrations')));
            }
            return self::extractValueFromPath($data[$currentPart], $parts);
        }

        if (is_object($data)) {
            if (!property_exists($data, $currentPart)) {
                wp_send_json_error(new WP_Error('Spectra', __('Invalid path', 'bit-integrations')));
            }
            return self::extractValueFromPath($data->$currentPart, $parts);
        }

        wp_send_json_error(new WP_Error('Spectra', __('Invalid path', 'bit-integrations')));
    }
}
