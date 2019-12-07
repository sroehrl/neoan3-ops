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
        $templateFunctions = ['nFor', 'nIf'];
        foreach ($templateFunctions as $function) {
            $content = self::enforceEmbraceInAttributes(self::$function($content, $array));
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
    static private function nFor($content, $array)
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xPath = new \DOMXPath($doc);
        $hits = $xPath->query("//*[@n-for]");
        if ($hits->length < 1) {
            return $content;
        }
        foreach ($hits as $hit){
            // extract attribute
            $parts = explode(' ', $hit->getAttribute('n-for'));
            // remove attribute
            $hit->removeAttribute('n-for');
            // while string
            $template = self::nodeStringify($hit);

            // clean
            foreach ($parts as $i=>$part){
                if(empty(trim($part))){
                    unset($parts[$i]);
                }
            }
            $parts = array_values($parts);
            $newContent = '';
            if(isset($array[$parts[0]]) && !empty($array[$parts[0]])){
                $subArray = [];
                foreach ($array[$parts[0]] as $key => $value){

                    if (isset($parts[4])) {
                        $subArray[$parts[2]] = $key;
                        $subArray[$parts[4]] = $value;
                    } else {
                        $subArray[$parts[2]] = $value;
                    }
                    $newContent .= self::embrace($template, $subArray);
                }
                self::clone($doc, $hit, $newContent);

            }
        }
        return $doc->saveHTML();
    }

    /**
     * @param $content
     * @param $array
     *
     * @return string
     */
    static private function nIf($content, $array)
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xPath = new \DOMXPath($doc);
        $hits = $xPath->query("//*[@n-if]");
        if ($hits->length < 1) {
            return $content;
        }

        foreach ($hits as $hit) {
            $expression = $hit->getAttribute('n-if');
            $bool = true;
            foreach ($array as $key => $value) {
                if (strpos($expression, $key) !== false) {
                    $expression = str_replace($key, $array[$key], $expression);
                    $bool = eval("return $expression;");
                }
            }

            if (!$bool) {
                $hit->parentNode->removeChild($hit);
            } else {
                $hit->removeAttribute('n-if');
            }
        }
        return $doc->saveHTML();
    }

    /**
     * @param $parentDoc
     * @param $hitNode
     * @param $stringContent
     */
    private static function clone(\DOMDocument $parentDoc, \DOMElement $hitNode, string $stringContent){
        $newDD =  new \DOMDocument();
        @$newDD->loadHTML('<root>' .$stringContent . '</root>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS);
        foreach ($newDD->firstChild->childNodes as $subNode){

            if($subNode->hasChildNodes() > 0 && $subNode->childNodes->length>0){
                $isNode = $parentDoc->importNode($subNode, true);
                $hitNode->parentNode->appendChild($isNode);
            }
        }
        $hitNode->parentNode->removeChild($hitNode);
    }

    /**
     * @param $content
     *
     * @return string|string[]|null
     */
    private static function enforceEmbraceInAttributes($content){
        return preg_replace('/="(.*)(%7B%7B)(.*)(%7D%7D)(.*)"/','="$1{{$3}}$5"', $content);
    }

    /**
     * @param \DOMElement $domNode
     *
     * @return string
     */
    private static function nodeStringify(\DOMElement $domNode){
        $string = '<' . $domNode->tagName;
        foreach ($domNode->attributes as $attribute){
            $string .= ' ' .$attribute->name .'="' . $attribute->value .'"';
        }
        $string .= '>';
        if($domNode->hasChildNodes()){

            foreach ($domNode->childNodes as $node){
                $string .= $domNode->ownerDocument->saveHTML($node);
            }
        }
        $string .= '</'. $domNode->tagName .'>';
        return $string;
    }
}
