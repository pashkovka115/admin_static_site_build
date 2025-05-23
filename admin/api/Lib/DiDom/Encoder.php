<?php

declare(strict_types=1);

namespace Lib\DiDom;

class Encoder
{
    /**
     * @param string $string
     * @param string $encoding
     *
     * @return string
     */
    public static function convertToHtmlEntities(string $string, string $encoding): string
    {
        // handling HTML entities via mbstring is deprecated in PHP 8.2
        if (function_exists('mb_convert_encoding') && PHP_VERSION_ID < 80200) {
            return mb_convert_encoding($string, 'HTML-ENTITIES', $encoding);
        }

        if ('UTF-8' !== $encoding) {
            $string = iconv($encoding, 'UTF-8//IGNORE', $string);
        }

        return preg_replace_callback('/[\x80-\xFF]+/', [__CLASS__, 'htmlEncodingCallback'], $string);
    }

    /**
     * @param string[] $matches
     *
     * @return string
     */
    private static function htmlEncodingCallback(array $matches): string
    {
        $characterIndex = 1;
        $entities = '';

        $codes = unpack('C*', htmlentities($matches[0], ENT_COMPAT, 'UTF-8'));

        while (isset($codes[$characterIndex])) {
            if (0x80 > $codes[$characterIndex]) {
                $entities .= chr($codes[$characterIndex++]);

                continue;
            }

            if (0xF0 <= $codes[$characterIndex]) {
                $code = (($codes[$characterIndex++] - 0xF0) << 18) + (($codes[$characterIndex++] - 0x80) << 12) + (($codes[$characterIndex++] - 0x80) << 6) + $codes[$characterIndex++] - 0x80;
            } elseif (0xE0 <= $codes[$characterIndex]) {
                $code = (($codes[$characterIndex++] - 0xE0) << 12) + (($codes[$characterIndex++] - 0x80) << 6) + $codes[$characterIndex++] - 0x80;
            } else {
                $code = (($codes[$characterIndex++] - 0xC0) << 6) + $codes[$characterIndex++] - 0x80;
            }

            $entities .= '&#' . $code . ';';
        }

        return $entities;
    }
}
