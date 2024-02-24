<?php

if (!defined('ABSPATH')) {
    exit;
}

use BTCBI\Deps\BitApps\WPKit\Hooks\Hooks;
use BitApps\BTCBI\Http\Services\Triggers\Post\PostController;

// Hooks::addAction('save_post', [PostController::class, 'createPost'], 10, 3);
Hooks::addAction('wp_after_insert_post', [PostController::class, 'createPost'], 10, 4);
Hooks::addAction('comment_post', [PostController::class, 'postComment'], 10, 3);
Hooks::addAction('post_updated', [PostController::class, 'postUpdated'], 10, 2);
Hooks::addFilter('the_content', [PostController::class, 'viewPost'], 10, 1);
Hooks::addAction('delete_post', [PostController::class, 'deletePost'], 10, 2);
Hooks::addAction('transition_post_status', [PostController::class, 'changePostStatus'], 10, 3);
Hooks::addAction('trash_comment', [PostController::class, 'trashComment'], 10, 2);
Hooks::addAction('edit_comment', [PostController::class, 'updateComment'], 10, 2);
Hooks::addAction('wp_trash_post', [PostController::class, 'trashPost'], 10, 1);
