<?php

namespace Neoan3\Apps;

use Exception;

/**
 * Class Ops
 * @package Neoan3\Apps
 */
class Ops
{

    static function __callStatic($name, $arguments)
    {
        if(!method_exists(self::class,$name)){
            // try template
            if(method_exists(Template::class, $name)){
                return Template::$name(...$arguments);
            }
        }
    }

    /**
     * @param $any
     *
     * @return string
     */
    static function serialize($any)
    {
        return urlencode(base64_encode(json_encode($any)));
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    static function deserialize($string)
    {
        return json_decode(base64_decode(urldecode($string)), true);
    }

    /**
     * @param $length
     *
     * @return int
     * @throws Exception
     */
    static function pin($length = 6)
    {
        $from = str_pad(1,$length,0);
        $to = str_pad(9,$length,9);
        return random_int($from, $to);

    }

    /**
     * @param int $length
     *
     * @return bool|string
     * @throws Exception
     */
    static function randomString($length = 16)
    {
        return mb_substr(bin2hex(random_bytes($length)), 0, $length);
    }

    /**
     * @param int  $length
     * @param bool $special
     *
     * @return string
     */
    static function hash($length = 10, $special = false)
    {
        trigger_error('Deprecated function called. Use random($length) instead', E_USER_NOTICE);
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        if ($special) {
            $chars .= ")(}{][";
        }
        srand((double)microtime() * 1000000);
        $i = 0;
        $pass = 'N';
        while ($i < $length) {
            $num = rand(0, strlen($chars) - 1);
            $tmp = substr($chars, $num, 1);
            $pass .= $tmp;
            $i++;
        }
        return $pass;
    }


    /**
     * @param $message
     * @param $key
     *
     * @return string
     */
    static function encrypt($message, $key)
    {
        $nonceSize = openssl_cipher_iv_length('aes-256-ctr');
        $nonce = openssl_random_pseudo_bytes($nonceSize);

        $cipherText = openssl_encrypt(
            $message, 'aes-256-ctr', $key, OPENSSL_RAW_DATA, $nonce
        );
        return base64_encode($nonce . $cipherText);

    }

    /**
     * @param $message
     * @param $key
     *
     * @return string
     * @throws Exception
     */
    static function decrypt($message, $key)
    {
        $message = base64_decode($message, true);
        if ($message === false) {
            throw new Exception('Encryption failure');
        }

        $nonceSize = openssl_cipher_iv_length('aes-256-ctr');
        $nonce = mb_substr($message, 0, $nonceSize, '8bit');
        $cipherText = mb_substr($message, $nonceSize, null, '8bit');

        $plaintext = openssl_decrypt(
            $cipherText, 'aes-256-ctr', $key, OPENSSL_RAW_DATA, $nonce
        );

        return $plaintext;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    static function base64url_to_base64($input = "")
    {
        $padding = strlen($input) % 4;
        if ($padding > 0) {
            $input .= str_repeat("=", 4 - $padding);
        }
        return strtr($input, '-_', '+/');
    }

    /**
     * @param $array
     * @param $objArray
     *
     * @return array
     */
    static function extrude($array, $objArray)
    {
        $return = [];
        foreach ($array as $key) {
            if (array_key_exists($key, $objArray)) {
                $return[$key] = $objArray[$key];
            }
        }
        return $return;
    }





    /**
     * Converts kebab-, camel- and snake-case to PascalCase
     *
     * @param $string
     *
     * @return string
     */
    static function toPascalCase($string)
    {
        $ret = self::caseConverter($string);
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : ucfirst($match);
        }
        return implode('', $ret);
    }

    /**
     * Converts kebab-, camel- and snake-case to camelCase
     *
     * @param $string
     *
     * @return string
     */
    static function toCamelCase($string)
    {
        $ret = self::caseConverter($string);
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : ucfirst($match);
        }
        return lcfirst(implode('', $ret));
    }

    /**
     * Converts kebab-, camel- and pascal-case to snake_case
     *
     * @param $string
     *
     * @return string
     */
    static function toSnakeCase($string)
    {
        $ret = self::caseConverter($string);
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * @param $string
     *
     * @return string
     */
    static function toKebabCase($string)
    {
        $ret = self::caseConverter($string);
        foreach ($ret as &$match) {
            $match = $match == strtolower($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('-', $ret);
    }

    private static function caseConverter($string)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
        return $matches[0];
    }

    /**
     * @param $val
     *
     * @return bool
     */
    static function isJSobj($val)
    {
        if (is_numeric($val)) {
            return true;
        }
        if (substr($val, 0, 1) == '{' && substr($val, -1) == '}') {
            return true;
        }
        if (substr($val, 0, 1) == '[' && substr($val, -1) == ']') {
            return true;
        }
        return false;
    }

}
