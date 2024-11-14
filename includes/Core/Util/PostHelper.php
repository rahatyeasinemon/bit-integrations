<?php

namespace BitCode\FI\Core\Util;

use WP_Post;

/**
 * bit-integration Post helper class
 *
 * @since 1.0.0
 */
final class PostHelper
{
    public static function getPostDataById($id)
    {
        return (array) WP_Post::get_instance($id);
    }

    public static function getPostMetaById($id)
    {
        return get_metadata('post', $id);
    }
}
