<?php

namespace AmjitK\GlobalNotification\Services;

class ContentParser
{
    public static function parse($content, array $data)
    {
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $content = str_replace('{{'.$key.'}}', $value, $content);
            }
        }
        return $content;
    }
}
