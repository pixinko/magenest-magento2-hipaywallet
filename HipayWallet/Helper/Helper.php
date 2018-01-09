<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 1/6/17
 * Time: 08:52
 */

namespace Magenest\HipayWallet\Helper;

class Helper
{
    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function generateToken()
    {
        $seed = crc32(uniqid(sha1(microtime(true) . getmypid()), true));
        mt_srand($seed);
        $n = mt_rand(1, 200);
        for ($i = 0; $i < $n; $i++) {
            $token = mt_rand();
        }

        return $token;
    }

    public static function getSoapOption()
    {
        $opts = array(
            'http' => array(
                'user_agent' => 'PHPSoapClient'
            )
        );

        $options = array(
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'soap_version' => SOAP_1_1,
            'encoding' => 'UTF-8',
            'exceptions' => true,
            'stream_context' => stream_context_create($opts),
        );

        return $options;
    }

    public static function getTokenName($orderId)
    {
        return "token_" . $orderId;
    }
}
