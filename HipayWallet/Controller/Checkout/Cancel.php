<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/30/16
 * Time: 13:54
 */

namespace Magenest\HipayWallet\Controller\Checkout;

use Magenest\HipayWallet\Controller\Checkout;
use Magenest\HipayWallet\Helper\Helper;

class Cancel extends Checkout
{
    public function execute()
    {
        if ($this->checkToken()) {
            $this->messageManager->addErrorMessage(__("Your order has been canceled because you have canceled the payment process."));
            $this->cancelOrder();
            $this->checkoutSession->restoreQuote();
            $this->_redirect('checkout/cart');
        }
    }
}
