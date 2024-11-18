<?php

namespace BitCode\FI\Core\Util;

use WP_Post;

final class Post
{
    public static function get($id)
    {
        return (array) WP_Post::get_instance($id);
    }

    public static function getMeta($id)
    {
        return get_metadata('post', $id);
    }
}
