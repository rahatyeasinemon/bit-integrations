<?php

namespace BitCode\FI\Core\Util;

final class FallbackHooks
{
    public static $triggerHookList = [
        ['hookName' => 'academy/course/after_enroll', 'functionToAdd' => 'academyHandleCourseEnroll', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'academy_quizzes/api/after_quiz_attempt_finished', 'functionToAdd' => 'academyHandleQuizAttempt', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'academy/frontend/after_mark_topic_complete', 'functionToAdd' => 'academyHandleLessonComplete', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'academy/admin/course_complete_after', 'functionToAdd' => 'academyHandleCourseComplete', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'academy_quizzes/api/after_quiz_attempt_finished', 'functionToAdd' => 'academyHandleQuizTarget', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'affwp_set_affiliate_status', 'functionToAdd' => 'affwpNewAffiliateApproved', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'affwp_set_affiliate_status', 'functionToAdd' => 'affwpUserBecomesAffiliate', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'affwp_insert_referral', 'functionToAdd' => 'affwpAffiliateMakesReferral', 'priority' => 20, 'acceptedArgs' => 1],
        ['hookName' => 'affwp_set_referral_status', 'functionToAdd' => 'affwpAffiliatesReferralSpecificTypeRejected', 'priority' => 99, 'acceptedArgs' => 3],
        ['hookName' => 'affwp_set_referral_status', 'functionToAdd' => 'affwpAffiliatesReferralSpecificTypePaid', 'priority' => 99, 'acceptedArgs' => 3],

        ['hookName' => 'arfliteentryexecute', 'functionToAdd' => 'handleArFormSubmit', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'arfentryexecute', 'functionToAdd' => 'handleArFormSubmit', 'priority' => 10, 'acceptedArgs' => 4],

        ['hookName' => 'arm_after_add_new_user', 'functionToAdd' => 'handleRegisterForm', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'arm_member_update_meta', 'functionToAdd' => 'handleUpdateUserByForm', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'arm_after_add_new_user', 'functionToAdd' => 'handleMemberAddByAdmin', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'arm_cancel_subscription', 'functionToAdd' => 'handleCancelSubscription', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'arm_after_user_plan_change_by_admin', 'functionToAdd' => 'handlePlanChangeAdmin', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'arm_after_user_plan_renew', 'functionToAdd' => 'handleRenewSubscriptionPlan', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'fl_module_contact_form_after_send', 'functionToAdd' => 'beaverContactFormSubmitted', 'priority' => 10, 'acceptedArgs' => 6],
        ['hookName' => 'fl_builder_login_form_submission_complete', 'functionToAdd' => 'beaverLoginFormSubmitted', 'priority' => 10, 'acceptedArgs' => 5],
        ['hookName' => 'fl_builder_subscribe_form_submission_complete', 'functionToAdd' => 'beaverSubscribeFormSubmitted', 'priority' => 10, 'acceptedArgs' => 6],

        ['hookName' => 'bricks/form/custom_action', 'functionToAdd' => 'handleBricksSubmit', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'brizy_form_submit_data', 'functionToAdd' => 'handleBrizySubmit', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'friends_friendship_accepted', 'functionToAdd' => 'buddyBossHandleAcceptFriendRequest', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'friends_friendship_requested', 'functionToAdd' => 'buddyBossHandleSendsFriendRequest', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'bbp_new_topic', 'functionToAdd' => 'buddyBossHandleCreateTopic', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'groups_join_group', 'functionToAdd' => 'buddyBossHandleJoinPublicGroup', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'groups_membership_accepted', 'functionToAdd' => 'buddyBossHandleJoinPrivateGroup', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'groups_accept_invite', 'functionToAdd' => 'buddyBossHandleJoinPrivateGroup', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'groups_leave_group', 'functionToAdd' => 'buddyBossHandleLeavesGroup', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'groups_remove_member', 'functionToAdd' => 'buddyBossHandleLeavesGroup', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'bp_groups_posted_update', 'functionToAdd' => 'buddyBossHandlePostGroupActivity', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'bbp_new_reply', 'functionToAdd' => 'buddyBossHandleRepliesTopic', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'groups_membership_requested', 'functionToAdd' => 'buddyBossHandleRequestPrivateGroup', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'bp_member_invite_submit', 'functionToAdd' => 'buddyBossHandleSendEmailInvites', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'xprofile_avatar_uploaded', 'functionToAdd' => 'buddyBossHandleUpdateAvatar', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'xprofile_updated_profile', 'functionToAdd' => 'buddyBossHandleUpdateProfile', 'priority' => 10, 'acceptedArgs' => 5],
        ['hookName' => 'bp_core_activated_user', 'functionToAdd' => 'buddyBossHandleAccountActive', 'priority' => 10, 'acceptedArgs' => 5],
        ['hookName' => 'bp_invites_member_invite_activate_user', 'functionToAdd' => 'buddyBossHandleInviteeActiveAccount', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'bp_invites_member_invite_mark_register_user', 'functionToAdd' => 'buddyBossHandleInviteeRegisterAccount', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'woocommerce_checkout_order_processed', 'functionToAdd' => 'handleOrderCreateWc', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'et_pb_contact_form_submit', 'functionToAdd' => 'handleDiviSubmit', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'edd_complete_purchase', 'functionToAdd' => 'eddHandlePurchaseProduct', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'edd_complete_purchase', 'functionToAdd' => 'eddHandlePurchaseProductDiscountCode', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'edds_payment_refunded', 'functionToAdd' => 'eddHandleOrderRefunded', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'eb_form_submit_before_email', 'functionToAdd' => 'essentialBlocksHandler', 'priority' => 10, 'acceptedArgs' => PHP_INT_MAX],

        ['hookName' => 'everest_forms_complete_entry_save', 'functionToAdd' => 'evfHandleSubmission', 'priority' => 10, 'acceptedArgs' => 5],

        ['hookName' => 'fluentform_submission_inserted', 'functionToAdd' => 'ffHandleSubmit', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'fluentcrm_contact_added_to_tags', 'functionToAdd' => 'fluentcrmHandleAddTag', 'priority' => 20, 'acceptedArgs' => 2],
        ['hookName' => 'fluentcrm_contact_removed_from_tags', 'functionToAdd' => 'fluentcrmHandleRemoveTag', 'priority' => 20, 'acceptedArgs' => 2],
        ['hookName' => 'fluentcrm_contact_added_to_lists', 'functionToAdd' => 'fluentcrmHandleAddList', 'priority' => 20, 'acceptedArgs' => 2],
        ['hookName' => 'fluentcrm_contact_removed_from_lists', 'functionToAdd' => 'fluentcrmHandleRemoveList', 'priority' => 20, 'acceptedArgs' => 2],
        ['hookName' => 'fluentcrm_contact_created', 'functionToAdd' => 'fluentcrmHandleContactCreate', 'priority' => 20, 'acceptedArgs' => 1],
        ['hookName' => 'fluentcrm_subscriber_status_to_subscribed', 'functionToAdd' => 'fluentcrmHandleChangeStatus', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'fluentcrm_subscriber_status_to_pending', 'functionToAdd' => 'fluentcrmHandleChangeStatus', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'fluentcrm_subscriber_status_to_unsubscribed', 'functionToAdd' => 'fluentcrmHandleChangeStatus', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'fluentcrm_subscriber_status_to_bounced', 'functionToAdd' => 'fluentcrmHandleChangeStatus', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'fluentcrm_subscriber_status_to_complained', 'functionToAdd' => 'fluentcrmHandleChangeStatus', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'formcraft_after_save', 'functionToAdd' => 'handleFormcraftSubmit', 'priority' => 10, 'acceptedArgs' => 4],

        ['hookName' => 'frm_success_action', 'functionToAdd' => 'handleFormidableSubmit', 'priority' => 10, 'acceptedArgs' => 5],

        ['hookName' => 'forminator_custom_form_submit_before_set_fields', 'functionToAdd' => 'handleForminatorSubmit', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'gamipress_update_user_rank', 'functionToAdd' => 'gamipressHandleUserEarnRank', 'priority' => 10, 'acceptedArgs' => 5],
        ['hookName' => 'gamipress_award_achievement', 'functionToAdd' => 'gamipressHandleAwardAchievement', 'priority' => 10, 'acceptedArgs' => 5],
        ['hookName' => 'gamipress_award_achievement', 'functionToAdd' => 'gamipressHandleGainAchievementType', 'priority' => 10, 'acceptedArgs' => 5],
        ['hookName' => 'gamipress_revoke_achievement_to_user', 'functionToAdd' => 'gamipressHandleRevokeAchieve', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'gamipress_update_user_points', 'functionToAdd' => 'gamipressHandleEarnPoints', 'priority' => 10, 'acceptedArgs' => 8],

        ['hookName' => 'gform_after_submission', 'functionToAdd' => 'gformAfterSubmission', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'give_update_payment_status', 'functionToAdd' => 'giveHandleUserDonation', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'give_subscription_cancelled', 'functionToAdd' => 'giveHandleSubscriptionDonationCancel', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'give_subscription_updated', 'functionToAdd' => 'giveHandleRecurringDonation', 'priority' => 10, 'acceptedArgs' => 4],

        ['hookName' => 'groundhogg/contact/post_create', 'functionToAdd' => 'groundhoggHandleSubmit', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'groundhogg/contact/tag_applied', 'functionToAdd' => 'groundhoggTagApplied', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'groundhogg/contact/tag_removed', 'functionToAdd' => 'groundhoggTagRemove', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'happyforms_submission_success', 'functionToAdd' => 'handleHappySubmit', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'updated_post_meta', 'functionToAdd' => 'jetEnginePostMetaData', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'updated_post_meta', 'functionToAdd' => 'jetEnginePostMetaValueCheck', 'priority' => 10, 'acceptedArgs' => 4],

        ['hookName' => 'kadence_blocks_form_submission', 'functionToAdd' => 'handleKadenceFormSubmit', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'kadence_blocks_advanced_form_submission', 'functionToAdd' => 'handleKadenceFormSubmit', 'priority' => 10, 'acceptedArgs' => 4],

        ['hookName' => 'learndash_update_course_access', 'functionToAdd' => 'learndashHandleCourseEnroll', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'learndash_course_completed', 'functionToAdd' => 'learndashHandleCourseCompleted', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'learndash_lesson_completed', 'functionToAdd' => 'learndashHandleLessonCompleted', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'learndash_topic_completed', 'functionToAdd' => 'learndashHandleTopicCompleted', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'learndash_quiz_submitted', 'functionToAdd' => 'learndashHandleQuizAttempt', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'ld_added_group_access', 'functionToAdd' => 'learndashHandleAddedGroup', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'ld_removed_group_access', 'functionToAdd' => 'learndashHandleRemovedGroup', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'learndash_assignment_uploaded', 'functionToAdd' => 'learndashHandleAssignmentSubmit', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'lifterlms_quiz_completed', 'functionToAdd' => 'lifterLmsHandleAttemptQuiz', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'lifterlms_quiz_passed', 'functionToAdd' => 'lifterLmsHandleQuizPass', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'lifterlms_quiz_failed', 'functionToAdd' => 'lifterLmsHandleQuizFail', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'lifterlms_lesson_completed', 'functionToAdd' => 'lifterLmsHandleLessonComplete', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'lifterlms_course_completed', 'functionToAdd' => 'lifterLmsHandleCourseComplete', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'llms_user_enrolled_in_course', 'functionToAdd' => 'lifterLmsHandleCourseEnroll', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'llms_user_removed_from_course', 'functionToAdd' => 'lifterLmsHandleCourseUnEnroll', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'llms_subscription_cancelled_by_student', 'functionToAdd' => 'lifterLmsHandleMembershipCancel', 'priority' => 10, 'acceptedArgs' => 4],

        ['hookName' => 'mailpoet_subscription_before_subscribe', 'functionToAdd' => 'handleMailpoetSubmit', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'stm_lms_progress_updated', 'functionToAdd' => 'stmLmsHandleCourseComplete', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'course_enrolled', 'functionToAdd' => 'stmLmsHandleCourseEnroll', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'lesson_completed', 'functionToAdd' => 'stmLmsHandleLessonComplete', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'stm_lms_quiz_passed', 'functionToAdd' => 'stmLmsHandleQuizComplete', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'stm_lms_quiz_failed', 'functionToAdd' => 'stmLmsHandleQuizFailed', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'mepr-event-transaction-completed', 'functionToAdd' => 'meprOneTimeMembershipSubscribe', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'mepr-event-transaction-completed', 'functionToAdd' => 'meprRecurringMembershipSubscribe', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'mepr_subscription_transition_status', 'functionToAdd' => 'meprMembershipSubscribeCancel', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'mepr-event-transaction-expired', 'functionToAdd' => 'meprMembershipSubscribeExpire', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'mepr_subscription_transition_status', 'functionToAdd' => 'meprMembershipSubscribePaused', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'metform_pro_form_data_for_pro_integrations', 'functionToAdd' => 'handleMetformProSubmit', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'metform_after_store_form_data', 'functionToAdd' => 'handleMetformSubmit', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'rwmb_frontend_after_save_post', 'functionToAdd' => 'handleMetaboxSubmit', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'pmpro_after_change_membership_level', 'functionToAdd' => 'perchesMembershhipLevelByAdministator', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'pmpro_after_change_membership_level', 'functionToAdd' => 'cancelMembershhipLevel', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'pmpro_after_checkout', 'functionToAdd' => 'perchesMembershipLevel', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'pmpro_membership_post_membership_expiry', 'functionToAdd' => 'expiryMembershipLevel', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'pafe/form_builder/new_record_v2', 'functionToAdd' => 'handlePiotnetAddonSubmit', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'pafe/form_builder/new_record_v2', 'functionToAdd' => 'handlePiotnetAddonFormSubmit', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'piotnetforms/form_builder/new_record', 'functionToAdd' => 'handlePiotnetSubmit', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'wp_after_insert_post', 'functionToAdd' => 'createPost', 'priority' => 10, 'acceptedArgs' => 4],
        ['hookName' => 'comment_post', 'functionToAdd' => 'postComment', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'post_updated', 'functionToAdd' => 'postUpdated', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'the_content', 'functionToAdd' => 'viewPost', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'delete_post', 'functionToAdd' => 'deletePost', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'transition_post_status', 'functionToAdd' => 'changePostStatus', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'trash_comment', 'functionToAdd' => 'trashComment', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'edit_comment', 'functionToAdd' => 'updateComment', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'wp_trash_post', 'functionToAdd' => 'trashPost', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'user_register', 'functionToAdd' => 'userCreate', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'profile_update', 'functionToAdd' => 'profileUpdate', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'wp_login', 'functionToAdd' => 'wpLogin', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'password_reset', 'functionToAdd' => 'wpResetPassword', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'delete_user', 'functionToAdd' => 'wpUserDeleted', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'rcp_membership_post_activate', 'functionToAdd' => 'rcpPurchasesMembershipLevel', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'rcp_transition_membership_status_cancelled', 'functionToAdd' => 'rcpMembershipStatusCancelled', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'rcp_transition_membership_status_expired', 'functionToAdd' => 'rcpMembershipStatusExpired', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'slicewp_insert_affiliate', 'functionToAdd' => 'slicewpNewAffiliateCreated', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'slicewp_insert_commission', 'functionToAdd' => 'slicewpUserEarnCommission', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'data_model_solid_affiliate_affiliates_save', 'functionToAdd' => 'newSolidAffiliateCreated', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'data_model_solid_affiliate_referrals_save', 'functionToAdd' => 'newSolidAffiliateReferralCreated', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'uagb_form_success', 'functionToAdd' => 'spectraHandler', 'priority' => 10, 'acceptedArgs' => PHP_INT_MAX],

        ['hookName' => 'sc_order_complete', 'functionToAdd' => 'studiocartNewOrderCreated', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'surecart/purchase_created', 'functionToAdd' => 'surecartPurchaseProduct', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'surecart/purchase_revoked', 'functionToAdd' => 'surecartPurchaseRevoked', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'surecart/purchase_invoked', 'functionToAdd' => 'surecartPurchaseUnrevoked', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'themify_builder_after_template_content_render', 'functionToAdd' => 'handleThemifySubmit', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'thrive_apprentice_course_finish', 'functionToAdd' => 'thriveApprenticeHandleCourseComplete', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'thrive_apprentice_lesson_complete', 'functionToAdd' => 'thriveApprenticeHandleLessonComplete', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'thrive_apprentice_module_finish', 'functionToAdd' => 'thriveApprenticeHandleModuleComplete', 'priority' => 10, 'acceptedArgs' => 2],

        ['hookName' => 'tutor_after_enroll', 'functionToAdd' => 'TutorLmsHandleCourseEnroll', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'tutor_quiz/attempt_ended', 'functionToAdd' => 'TutorLmsHandleQuizAttempt', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'tutor_lesson_completed_after', 'functionToAdd' => 'TutorLmsHandleLessonComplete', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'tutor_course_complete_after', 'functionToAdd' => 'TutorLmsHandleCourseComplete', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'tutor_quiz/attempt_ended', 'functionToAdd' => 'TutorLmsHandleQuizTarget', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'um_user_login', 'functionToAdd' => 'ultimateMemberHandleUserLogViaForm', 'priority' => 10, 'acceptedArgs' => 1],
        ['hookName' => 'um_registration_complete', 'functionToAdd' => 'ultimateMemberHandleUserRegisViaForm', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'set_user_role', 'functionToAdd' => 'ultimateMemberHandleUserRoleChange', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'set_user_role', 'functionToAdd' => 'ultimateMemberHandleUserSpecificRoleChange', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'weforms_entry_submission', 'functionToAdd' => 'handleWeformsSubmit', 'priority' => 10, 'acceptedArgs' => 4],

        ['hookName' => 'wpcw_enroll_user', 'functionToAdd' => 'wpcwUserEnrolledCourse', 'priority' => 10, 'acceptedArgs' => 2],
        ['hookName' => 'wpcw_user_completed_course', 'functionToAdd' => 'wpcwCourseCompleted', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'wpcw_user_completed_module', 'functionToAdd' => 'wpcwModuleCompleted', 'priority' => 10, 'acceptedArgs' => 3],
        ['hookName' => 'wpcw_user_completed_unit', 'functionToAdd' => 'wpcwUnitCompleted', 'priority' => 10, 'acceptedArgs' => 3],

        ['hookName' => 'ipt_fsqm_hook_save_success', 'functionToAdd' => 'wpefHandleSubmission', 'priority' => 10, 'acceptedArgs' => 1],

        ['hookName' => 'ws_form_action_for_bi', 'functionToAdd' => 'handleWsFormSubmit', 'priority' => 9999, 'acceptedArgs' => 4],
    ];
}
