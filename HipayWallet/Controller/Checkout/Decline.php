<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/30/16
 * Time: 13:53
 */

namespace Magenest\HipayWallet\Controller\Checkout;

use Magenest\HipayWallet\Controller\Checkout;
use Magenest\HipayWallet\Helper\Helper;

class Decline extends Checkout
{
    public function execute()
    {
        if ($this->checkToken()) {
            $this->messageManager->addErrorMessage(__("Your order has been canceled because payment decline."));
            //$this->cancelOrder();
            $this->checkoutSession->restoreQuote();
            $this->_redirect('checkout/cart');
        }
    }
}
