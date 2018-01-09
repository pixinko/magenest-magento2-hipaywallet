<?php

namespace Magenest\HipayWallet\Model;

use Magento\Payment\Model\CcGenericConfigProvider;
use Magenest\HipayWallet\Model\HipayWalletPayment;

class ConfigProvider extends CcGenericConfigProvider
{
    protected $methodCodes = [
        HipayWalletPayment::CODE,
    ];
}
