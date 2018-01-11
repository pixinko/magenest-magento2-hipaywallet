<?php
/**
 * Created by PhpStorm.
 * User: magenest
 * Date: 14/06/2017
 * Time: 15:36
 */

namespace Magenest\HipayWallet\Controller\Checkout;

use Magenest\HipayWallet\Controller\Checkout;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;

class Redirect extends Checkout
{
    public function execute()
    {
        $result = $this->jsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            try {
                $order = $this->checkoutSession->getLastRealOrder();
                /** @var \Magento\Sales\Model\Order\Payment $payment */
                $payment = $order->getPayment();
                $url = $payment->getAdditionalInformation("redirect_url");
                if (!!$url) {
                    return $result->setData([
                        'success' => true,
                        'error' => false,
                        'url' => $url
                    ]);
                } else {
                    return $result->setData([
                        'success' => false,
                        'error' => true,
                        'message' => _('Payment error, please try again')
                    ]);
                }
            } catch (\Exception $e) {
                return $result->setData([
                    'error' => true,
                    'message' => _('Payment exception')
                ]);
            }
        }

        return false;
    }
}
