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
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        $params = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->orderSender = $orderSender;
        $this->jsonFactory = $resultJsonFactory;
        $this->paymentConfig = $paymentConfig;
        $this->logger = $logger;
        $this->orderManagement = $orderManagement;
        $this->stockRegistry = $stockRegistry;
        $this->productRepository = $productRepository;
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
            
            $order = $this->checkoutSession->getLastRealOrder();
            $productsToFix = array();
            foreach ($order->getAllItems() as $item) {
            
                $product = $item->getProduct();

                $this->logger->debug('Product name : ' .
                    $item->getProduct()->getName());

                $stockItem = $this->stockRegistry->getStockItem($product->getId());
                $this->logger->debug('Product quantity before : ' . $stockItem->getQty());
                
                $this->logger->debug('Product quantity ordered : ' . $item->getQtyOrdered());
                
                // Decreasing two times the stock will lead the product to be
                // out of stock even if it was in stock. Prevent such an issue.
                // Fix: https://github.com/magento/magento2/issues/8624
                $isInStock = $stockItem->getIsInStock();
                
                $productsToFix[] = array($product->getId() => true);
                
                if ($isInStock) {
                    $this->logger->debug($item->getProduct()->getName() . ' is in stock');
                } else {
                    $this->logger->debug($item->getProduct()->getName() . ' is NOT in stock');
                }

                $stockItem->setQty($stockItem->getQty() - (2 * $item->getQtyOrdered()));

                if ($stockItem->setIsInStock($isInStock)) {
                    $this->logger->debug('Set In Stock debug: TRUE');
                } else {
                    $this->logger->debug('Set In Stock debug: FALSE');
                }
                $product = $this->productRepository->getById($product->getId());
                $returnValue = $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                if ($returnValue) {
                    $this->logger->debug('setStatus return value true');
                } else {
                    $this->logger->debug('setStatus return value false');
                }
                $this->productRepository->save($product);
                // If $this->orderManagement->cancel($orderId); is placed
                // before, the save() statement is needed.
                //$stockItem->save();
                
                $this->logger->debug('Product quantity after : ' . $stockItem->getQty());
            }
            $this->orderManagement->cancel($orderId);
            
            foreach($productsToFix as $productId => $productInStock) {
                
                $product = $this->productRepository->getById($productId);
                
                if ($productInStock) {
                    $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                } else {
                    $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                }
                $this->productRepository->save($product);
            }
            
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
