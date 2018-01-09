<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/29/16
 * Time: 15:32
 */

namespace Magenest\HipayWallet\Controller;

use Magenest\HipayWallet\Helper\Helper;

abstract class Checkout extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    protected $resultJsonFactory;
    protected $orderFactory;
    protected $jsonFactory;
    protected $paymentConfig;
    protected $logger;
    protected $orderManagement;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magenest\HipayWallet\Helper\ConfigHelper $paymentConfig,
        \Magenest\HipayWallet\Helper\Logger $logger,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        $params = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->orderSender = $orderSender;
        $this->jsonFactory = $resultJsonFactory;
        $this->paymentConfig = $paymentConfig;
        $this->logger = $logger;
        $this->orderManagement = $orderManagement;
        parent::__construct($context);
    }

    protected function checkToken($removeToken = true)
    {
        $order = $this->checkoutSession->getLastRealOrder();
        $sessionTokenName = Helper::getTokenName($order->getIncrementId());
        $token = $this->getRequest()->getParam("token");
        $sessionToken = $this->checkoutSession->getData($sessionTokenName);
        if (($sessionToken == $token) && (!!$token) && (!!$sessionToken)) {
            if ($removeToken) {
                $this->checkoutSession->unsetData($sessionTokenName);
            }

            return true;
        } else {
            return false;
        }
    }

    protected function cancelOrder()
    {
        try {
            $orderId = $this->checkoutSession->getLastRealOrder()->getId();
            $this->orderManagement->cancel($orderId);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
