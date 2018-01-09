<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/29/16
 * Time: 15:33
 */

namespace Magenest\HipayWallet\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magenest\HipayWallet\Helper\Constant;

class ConfigHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
        $this->_encryptor = $encryptor;
    }


    public function getWsLogin()
    {
        return $this->_encryptor->decrypt($this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/ws_login',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
    }

    public function getWsPassword()
    {
        return $this->_encryptor->decrypt($this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/ws_password',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
    }

    public function getWebsiteId()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/website_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCategoryId()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/category_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getRating()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/rating',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getLocale()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/locale',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getServiceUrl()
    {
        $isTest = $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/test',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($isTest) {
            return Constant::ENDPOINT_URL_TEST;
        } else {
            return Constant::ENDPOINT_URL_PRODUCTION;
        }
    }

    public function getPaymentAction()
    {
        $paymentAction = $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/payment_action',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($paymentAction == 'authorize') {
            return 1;
        } else {
            return 0;
        }
    }

    public function isLoggerActive()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/logging',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCategoryUrl($websiteId = null)
    {
        if (!$websiteId) {
            $websiteId = $this->getWebsiteId();
        }
        $isTest = $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/test',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($isTest) {
            return Constant::SERVICE_URL_TEST . "order/list-categories/id/" . $websiteId;
        } else {
            return Constant::SERVICE_URL_PRODUCTION . "order/list-categories/id/" . $websiteId;
        }
    }

    public function getEmailCallbackAddress()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/email_callback',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getUrlLogo()
    {
        return $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/url_logo',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPaymentUrl()
    {
        $isTest = $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/test',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($isTest) {
            return Constant::SERVICE_URL_TEST . "order/";
        } else {
            return Constant::SERVICE_URL_PRODUCTION . "order/";
        }
    }

    public function getPaymentInstructions()
    {
        return preg_replace('/\s+|\n+|\r/', ' ', $this->scopeConfig->getValue(
            'payment/magenest_hipaywallet/instructions',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
    }
}
