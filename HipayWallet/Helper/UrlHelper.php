<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/30/16
 * Time: 13:56
 */

namespace Magenest\HipayWallet\Helper;

class UrlHelper
{
    public static function getUrlCallBack($baseUrl)
    {
        return $baseUrl . "hipaywallet/checkout/response";
    }

    public static function getUrlAccept($baseUrl, $token = "")
    {
        return $baseUrl . "hipaywallet/checkout/accept/token/" . $token;
    }

    public static function getUrlDecline($baseUrl, $token = "")
    {
        return $baseUrl . "hipaywallet/checkout/decline/token/" . $token;
    }

    public static function getUrlCancel($baseUrl, $token = "")
    {
        return $baseUrl . "hipaywallet/checkout/cancel/token/" . $token;
    }
}
