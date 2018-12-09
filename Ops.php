<?php

namespace Neoan3\Apps;

/**
 * Class Ops
 * @package Neoan3\Apps
 */
class Ops {
    /**
     * @param $any
     * @return string
     */
    static function serialize($any){
        return urlencode(base64_encode(json_encode($any)));
    }

    /**
     * @param $length
     * @return string
     */
    static function pin($length){
        $chars = "123456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '';
        while($i < $length)
        {
            $num = rand(0,strlen($chars)-1);
            $tmp = substr($chars, $num, 1);
            $pass .= $tmp;
            $i++;
        }
        return $pass;
    }

    /**
     * @param int $length
     * @param bool $special
     * @return string
     */
    static function hash($length = 10, $special=false){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        if($special){
            $chars .= ")(}{][";
        }
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = 'N';
        while($i < $length)
        {
            $num = rand(0,strlen($chars)-1);
            $tmp = substr($chars, $num, 1);
            $pass .= $tmp;
            $i++;
        }
        return $pass;
    }

    /**
     * @param $message
     * @param $key
     * @return string
     */
    static function encrypt($message, $key){
        $nonceSize = openssl_cipher_iv_length('aes-256-ctr');
        $nonce = openssl_random_pseudo_bytes($nonceSize);

        $ciphertext = openssl_encrypt(
            $message,
            'aes-256-ctr',
            $key,
            OPENSSL_RAW_DATA,
            $nonce
        );
        return base64_encode($nonce.$ciphertext);

    }

    /**
     * @param $message
     * @param $key
     * @return string
     */
    static function decrypt($message, $key) {
        $message = base64_decode($message, true);
        if ($message === false) {
            throw new Exception('Encryption failure');
        }

        $nonceSize = openssl_cipher_iv_length('aes-256-ctr');
        $nonce = mb_substr($message, 0, $nonceSize, '8bit');
        $ciphertext = mb_substr($message, $nonceSize, null, '8bit');

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-ctr',
            $key,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $plaintext;
    }

    /**
     * @param string $input
     * @return string
     */
    static function base64url_to_base64($input=""){
        $padding = strlen($input) % 4;
        if ($padding > 0) {
            $input .= str_repeat("=", 4 - $padding);
        }
        return strtr($input, '-_', '+/');
    }

    /**
     * @param $array
     * @param $objArray
     * @return array
     */
    static function extrude($array, $objArray){
        $return = array();
        foreach ($array as $key){
            if(isset($objArray[$key])){
                $return[$key] = $objArray[$key];
            }
        }
        return $return;
    }

    /**
     * @param $content
     * @param $array
     * @return mixed
     */
    static function embrace($content, $array){
        return str_replace(array_map('self::curlyBraces', array_keys($array)), array_values($array), $content);
    }

    /**
     * @param $content
     * @param $array
     * @return mixed
     */
    static function hardEmbrace($content, $array){
        return str_replace(array_map('self::hardBraces', array_keys($array)), array_values($array), $content);
    }

    /**
     * @param $content
     * @param $array
     * @return mixed
     */
    static function tEmbrace($content, $array){
        return str_replace(array_map('self::tBraces', array_keys($array)), array_values($array), $content);
    }

    /**
     * @param $val
     * @return bool
     */
    static function isJSobj($val){
        if(is_numeric($val)){
            return true;
        }
        if(substr($val,0,1) == '{' && substr($val,-1) == '}'){
            return true;
        }
        if(substr($val,0,1) == '[' && substr($val,-1) == ']'){
            return true;
        }
        return false;
    }

    /**
     * @param $input
     * @return string
     */
    private static function curlyBraces($input) {
        return '{{' . $input . '}}';
    }

    /**
     * @param $input
     * @return string
     */
    private static function hardBraces($input){
        return '[[' . $input .']]';
    }

    /**
     * @param $input
     * @return string
     */
    private static function tBraces($input) {
        return '<t>' . $input . '</t>';
    }
}