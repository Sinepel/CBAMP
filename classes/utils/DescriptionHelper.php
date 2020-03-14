<?php

class DescriptionHelper
{
    public static function cleanDescription($originalDesc)
    {
        $cleanDesc = null;

        $cleanDesc = preg_replace(
            '/(<[^>]+) style=".*?"/i',
            '$1',
            $originalDesc
        );
        $cleanDesc = preg_replace(
            '/(<[^>]+) xml\:lang=".*?"/i',
            '$1',
            $cleanDesc
        );
        $cleanDesc = preg_replace(
            '/(<[^>]+) lang=".*?"/i',
            '$1',
            $cleanDesc
        );
        $cleanDesc = preg_replace(
            '/<img[^>]+\>/i',
            '',
            $cleanDesc
        );
        $cleanDesc = preg_replace(
            '/<iframe.*?\/iframe>/i',
            '',
            $cleanDesc
        );
        $cleanDesc = preg_replace(
            '/<video.*?\/video>/i',
            '',
            $cleanDesc
        );

        return $cleanDesc;
    }
}
