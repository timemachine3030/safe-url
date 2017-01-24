<?php

/**
 * This short class will turn user entered titles into URLs
 * that are keyword rich and human readable.  For use with
 * Apache's mod rewrite.
 *
 * @author scottayy@gmail.com
 * @author Daniel Lopretto (http://daniellopretto.com)
 * @author Adrián Granado (https://github.com/playmono)
 *
 */
class SafeUrl {
    /**
     * decode html entities in string?
     * @var boolean
     */
    static $decode = true;
    /**
     * charset to use if $decode is set to true
     * @var string
     */
    static $decode_charset = 'UTF-8';
    /**
     * turns string into all lowercase letters
     * @var boolean
     */
    static $lowercase = true;
    /**
     * strip out html tags from string?
     * @var boolean
     */
    static $strip = true;
    /**
     * maximum length of resulting title
     * @var int
     */
    static $maxlength = 50;
    /**
     * if maxlength is reached, chop at nearest whole word? or hard chop?
     * @var boolean
     */
    static $whole_word = true;
    /**
     * what title to use if no alphanumeric characters can be found
     * @var string
     */
    static $blank = 'no-title';
    /**
     * Allow a differnt character to be used as the separator.
     * @var string
     */
    static $separator = '-';
    /**
     * A table of UTF-8 characters and what to make them.
     * @link http://www.php.net/manual/en/function.strtr.php#90925
     * @var array
     */
    static $translation_table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj','Ð'=>'Dj','đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
        /**
         * Special characters:
         */
        "'"    => '',       // Single quote
        '&'    => ' and ',  // Amperstand
        "\r\n" => ' ',      // Newline
        "\n"   => ' '       // Newline

    );

    /**
     * Helper method that uses the translation table to convert 
     * non-ascii characters to a resonalbe alternative.
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    static public function convertCharacters($text) {
        $text = html_entity_decode($text, ENT_QUOTES, static::$decode_charset);
        $text = strtr($text, static::$translation_table);
        return $text;
    }

    /**
     * the worker function
     *
     * @param string $text
     * @return string
     */
    static public function makeUrl($text, $options = null) {
        if (is_array($options)) {
            foreach($options as $property => $value) {
                static::$$property = $value;
            }
        }

        //Shortcut
        $s = static::$separator;
        //prepare the string according to our options
        if (static::$decode) {
            $text = static::convertCharacters($text);
        }

        if (static::$lowercase) {
            $text = strtolower($text);
        }
        if (static::$strip) {
            $text = strip_tags($text);
        }

        //filter
        $text = preg_replace("/[^&a-z0-9_-\s']/i", '', $text);
        $text = str_replace(' ', $s, $text);
        $text = trim(preg_replace("/{$s}{2,}/", $s, $text), $s);

        //chop?
        if (strlen($text) > static::$maxlength) {
            $text = substr($text, 0, static::$maxlength);

            if (static::$whole_word) {
                /**
                 * If maxlength is small and leaves us with only part of one
                 * word ignore the "whole_word" filtering.
                 */
                $words = explode($s, $text);
                $temp  = implode($s, array_diff($words, array(array_pop($words))));
                if ($temp != '') {
                    $text = $temp;
                }
            }
        }
        //return =]
        if ($text == '') {
            return null;
        }

        return $text;
    }
}
