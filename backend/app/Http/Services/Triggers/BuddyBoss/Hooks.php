<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\BuddyBoss\BuddyBossController;

Hooks::addAction('friends_friendship_accepted', [BuddyBossController::class, 'handle_accept_friend_request'], 10, 4);
Hooks::addAction('friends_friendship_requested', [BuddyBossController::class, 'handle_sends_friend_request'], 10, 4);
Hooks::addAction('bbp_new_topic', [BuddyBossController::class, 'handle_create_topic'], 10, 4);
Hooks::addAction('groups_join_group', [BuddyBossController::class, 'handle_join_public_group'], 10, 2);
Hooks::addAction('groups_membership_accepted', [BuddyBossController::class, 'handle_join_private_group'], 10, 2);
Hooks::addAction('groups_accept_invite', [BuddyBossController::class, 'handle_join_private_group'], 10, 2);
Hooks::addAction('groups_leave_group', [BuddyBossController::class, 'handle_leaves_group'], 10, 2);
Hooks::addAction('groups_remove_member', [BuddyBossController::class, 'handle_leaves_group'], 10, 2);
Hooks::addAction('bp_groups_posted_update', [BuddyBossController::class, 'handle_post_group_activity'], 10, 4);
Hooks::addAction('bbp_new_reply', [BuddyBossController::class, 'handle_replies_topic'], 10, 3);
Hooks::addAction('groups_membership_requested', [BuddyBossController::class, 'handle_request_private_group'], 10, 4);
Hooks::addAction('bp_member_invite_submit', [BuddyBossController::class, 'handle_send_email_invites'], 10, 2);
Hooks::addAction('xprofile_avatar_uploaded', [BuddyBossController::class, 'handle_update_avatar'], 10, 3);
Hooks::addAction('xprofile_updated_profile', [BuddyBossController::class, 'handle_update_profile'], 10, 5);
Hooks::addAction('bp_core_activated_user', [BuddyBossController::class, 'handle_account_active'], 10, 5);
Hooks::addAction('bp_invites_member_invite_activate_user', [BuddyBossController::class, 'handle_invitee_active_account'], 10, 3);
Hooks::addAction('bp_invites_member_invite_mark_register_user', [BuddyBossController::class, 'handle_invitee_register_account'], 10, 3);
