<?php

namespace Neoan3\Apps;

use Exception;

/**
 * Class Ops
 * @package Neoan3\Apps
 */
class Ops
{
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
    static function deserialize($string){
        return json_decode(base64_decode(urldecode($string)),true);
    }

    /**
     * @param $length
     *
     * @return string
     */
    static function pin($length)
    {
        $chars = "123456789";
        srand((double)microtime() * 1000000);
        $i = 0;
        $pass = '';
        while ($i < $length) {
            $num = rand(0, strlen($chars) - 1);
            $tmp = substr($chars, $num, 1);
            $pass .= $tmp;
            $i++;
        }
        return $pass;
    }

    /**
     * @param int  $length
     * @param bool $special
     *
     * @return string
     */
    static function hash($length = 10, $special = false)
    {
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
            if (isset($objArray[$key])) {
                $return[$key] = $objArray[$key];
            }
        }
        return $return;
    }

    /**
     * @param      $array
     * @param bool $parentKey
     *
     * @return array
     */
    static function flattenArray($array, $parentKey = false)
    {
        $answer = [];
        foreach ($array as $key => $value) {
            if ($parentKey) {
                $key = $parentKey . '.' . $key;
            }
            if (!is_array($value)) {
                $answer[$key] = $value;
            } else {
                $answer = array_merge($answer, self::flattenArray($value, $key));
            }
        }
        return $answer;
    }

    /**
     * @param $content
     * @param $array
     *
     * @return mixed
     */
    static function embrace($content, $array)
    {
        $flatArray = self::flattenArray($array);
        $templateFunctions = ['nFor'];
        foreach($templateFunctions as $function){
            $content = self::$function($content, $array);
        }
        return str_replace(array_map('self::curlyBraces', array_keys($flatArray)), array_values($flatArray), $content);
    }

    /**
     * @param $content
     * @param $array
     *
     * @return mixed
     */
    static function hardEmbrace($content, $array)
    {
        return str_replace(array_map('self::hardBraces', array_keys($array)), array_values($array), $content);
    }

    /**
     * @param $content
     * @param $array
     *
     * @return mixed
     */
    static function tEmbrace($content, $array)
    {
        return str_replace(array_map('self::tBraces', array_keys($array)), array_values($array), $content);
    }

    /**
     * @param $location
     * @param $array
     *
     * @return mixed
     */
    static function embraceFromFile($location, $array)
    {
        $appRoot = defined('path') ? path : '';
        $file = file_get_contents($appRoot . '/' . $location);
        return self::embrace($file, $array);
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

    /**
     * @param $input
     *
     * @return string
     */
    private static function curlyBraces($input)
    {
        return '{{' . $input . '}}';
    }

    /**
     * @param $input
     *
     * @return string
     */
    private static function hardBraces($input)
    {
        return '[[' . $input . ']]';
    }

    /**
     * @param $input
     *
     * @return string
     */
    private static function tBraces($input)
    {
        return '<t>' . $input . '</t>';
    }

    /*
     * template functions
     * */


    /**
     * @param $content
     * @param $array
     *
     * @return string|string[]|null
     */
    static private function nFor($content, $array){

        $content = preg_replace_callback('/<n-template for="([^"]+)">(.*?)<\/n-template>/ms', function($hit) use ($array){
            $callToAction = explode(' ',$hit[1]);
            $string = '';
            foreach($array[$callToAction[0]] as $key => $value){
                $subArray = [];
                if(isset($callToAction[4])){
                    $subArray[$callToAction[2]] = $key;
                    $subArray[$callToAction[4]] = $value;
                } else {
                    $subArray[$callToAction[2]] = $value;
                }
                $string .= self::embrace($hit[2],$subArray);
            }
            return $string;

        }, $content);
        return $content;
    }
}
