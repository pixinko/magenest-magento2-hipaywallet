<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/30/16
 * Time: 09:16
 */

namespace Magenest\HipayWallet\Controller\Checkout;

use Magenest\HipayWallet\Controller\Checkout;
use Magenest\HipayWallet\Helper\Constant;

class Response extends Checkout
{
    public function execute()
    {
        $xml = $this->getRequest()->getParam('xml');
        $this->logger->debug(serialize($xml));
        /** @var \Magenest\HipayWallet\Helper\HipayPostDataHelper $hipayPostDataHelper */
        $hipayPostDataHelper = $this->_objectManager->create("\\Magenest\\HipayWallet\\Helper\\HipayPostDataHelper");
        if ($hipayPostDataHelper->validateDataResponse($xml)) {
            if ($hipayPostDataHelper->validateSecretKey($xml)) {
                $hipayPostDataHelper->handleDataResponse($xml);
            }
        }
    }
}
