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

class Accept extends Checkout
{
    public function execute()
    {
        if ($this->checkToken()) {
            $this->_redirect('checkout/onepage/success');
        } else {
            return false;
        }
    }
}
