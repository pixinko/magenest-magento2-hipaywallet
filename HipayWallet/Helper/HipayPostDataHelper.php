<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/30/16
 * Time: 08:46
 */

namespace Magenest\HipayWallet\Helper;

use Magento\Framework\Config\Data\ConfigData;
use Magento\Sales\Model\Order;

class HipayPostDataHelper
{
    protected $configData;
    private $url;
    protected $storeManager;
    public $baseUrl;
    protected $logger;
    protected $quoteRepository;
    protected $quoteManagement;

    protected $checkoutSession;
    protected $orderFactory;

    public function __construct(
        ConfigHelper $configData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magenest\HipayWallet\Helper\Logger $logger,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->configData = $configData;
        $this->url = $this->configData->getServiceUrl();
        $this->storeManager = $storeManager;
        $this->baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
        $this->quoteManagement = $quoteManagement;
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return mixed
     */
    public function requestOrder($order)
    {
        $sessionTokenGenerate = Helper::generateToken();
        $tokenName = Helper::getTokenName($order->getIncrementId());
        $this->checkoutSession->setData($tokenName, $sessionTokenGenerate);
        $secretKey = $this->genSecretKey($order->getIncrementId());
        $payment = $order->getPayment();
        $payment->setAdditionalInformation(Constant::ADDITIONAL_SECRET_KEY, $secretKey);
        $postUrl = $this->configData->getServiceUrl();
        $postUrl .= Constant::URL_PAYMENT;
        $opts = array(
            'http' => array(
                'user_agent' => 'PHPSoapClient'
            )
        );

        $context = stream_context_create($opts);
        $options = array(
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'soap_version' => SOAP_1_1,
            'encoding' => 'UTF-8',
            'exceptions' => true,
            'stream_context' => $context
        );
        $client = new \Zend_Soap_Client($postUrl, $options);
        $arrayPost = [
            'parameters' => array(
                'wsLogin' => $this->configData->getWsLogin(),
                'wsPassword' => $this->configData->getWsPassword(),
                'websiteId' => $this->configData->getWebsiteId(),
                'categoryId' => $this->configData->getCategoryId(),
                'currency' => strtoupper($order->getBaseCurrencyCode()),
                'amount' => $order->getBaseGrandTotal(),
                'rating' => $this->configData->getRating(),
                'locale' => $this->configData->getLocale(),
                'customerIpAddress' => $order->getRemoteIp(),
                'description' => $this->getPaymentDescription($order),
                'merchantReference' => $order->getIncrementId(),
                'freeData' => array(
                    array(
                        'key' => 'secret_key',
                        'value' => $secretKey,
                    ),
                ),
                'executionDate' => date("Y-m-d\TH:i:s"),
                'manualCapture' => $this->configData->getPaymentAction(),
                'customerEmail' => $order->getCustomerEmail(),
                'emailCallback' => $this->configData->getEmailCallbackAddress(),
                'urlCallback' => UrlHelper::getUrlCallBack($this->baseUrl),
                'urlAccept' => UrlHelper::getUrlAccept($this->baseUrl, $sessionTokenGenerate),
                'urlDecline' => UrlHelper::getUrlDecline($this->baseUrl, $sessionTokenGenerate),
                'urlCancel' => UrlHelper::getUrlCancel($this->baseUrl, $sessionTokenGenerate),
                'urlLogo' => $this->configData->getUrlLogo()
            )
        ];
        $this->logger->debug(var_export($arrayPost, true));

        $result = $client->generate($arrayPost);

        return $result;
    }

    public function validateSecretKey($xml)
    {
        try {
            if (!!$xml) {
                $obj = new \SimpleXMLElement(trim($xml));
                if (isset($obj->result->merchantDatas->_aKey_secret_key)) {
                    $secretKey = $obj->result->merchantDatas->_aKey_secret_key;
                    $orderId = $obj->result->idForMerchant;
                    $order = $this->orderFactory->create()->loadByIncrementId($orderId);
                    $payment = $order->getPayment();
                    $dataSecretKey = $payment->getAdditionalInformation(Constant::ADDITIONAL_SECRET_KEY);
                    if ($secretKey == $dataSecretKey) {
                        return true;
                    }
                    $this->logger->debug('Invalid response secret key: ' . $xml);

                    return false;
                } else {
                    $this->logger->debug("secret key or order id not set");

                    return false;
                }
            } else {
                $this->logger->debug("XML response not found");
            }
        } catch (\Exception $e) {
            $this->logger->debug('validate secret key exception, Message : ' . $e->getMessage());
        }

        return false;
    }

    private function genSecretKey($orderId)
    {
        $secretWordConfig = "";
        $secretWord = time() . $orderId . $secretWordConfig;

        return md5($secretWord);
    }

    public function validateDataResponse($xml)
    {
        try {
            if (!!$xml) {
                $obj = new \SimpleXMLElement(trim($xml));
                if (isset($obj->result[0])) {
                    if (isset($obj->md5content)) {
                        $md5content = $obj->md5content;
                        $startPos = strrpos($xml, "<result>");
                        $endPos = strrpos($xml, "</result>") + 9;
                        $data = substr($xml, $startPos, ($endPos - $startPos));
                        $md5 = hash('md5', trim($data));
                    }
                }

                if (isset($md5) || isset($md5content) || ($md5 == $md5content)) {
                    return true;
                }

                $this->logger->debug('Received data could not be validated (invalid hash key!): ' . $xml);

                return false;
            }
            $this->logger->debug('no xml data found.');
        } catch (\Exception $e) {
            $this->logger->debug('validate Data Response handle exception');
        }

        return false;
    }

    public function handleDataResponse($xml)
    {
        try {
            $obj = new \SimpleXMLElement(trim($xml));
            if (isset($obj->result) && ($obj->result->status == 'ok')) {
                $operation = $obj->result->operation;
                $status = $obj->result->status;
                $date = $obj->result->date;
                $time = $obj->result->time;
                $transid = $obj->result->transid;
                $origAmount = $obj->result->origAmount;
                $origCurrency = $obj->result->origCurrency;
                $emailClient = $obj->result->emailClient;
                $idForMerchant = $obj->result->idForMerchant;
                $paymentMethod = $obj->result->paymentMethod;
                $cardCountry = $obj->result->cardCountry;
                $ipcountry = $obj->result->ipCountry;
                $is3ds = $obj->result->is3ds;
                $customerCountry = $obj->result->customerCountry;
                $order = $this->orderFactory->create()->loadByIncrementId($idForMerchant);
                /** @var \Magento\Sales\Model\Order\Payment $payment */
                $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                $payment = $order->getPayment();
                $payment->setAdditionalInformation(Constant::ADDITIONAL_TRANS_ID, (string)$transid);

                if (isset($order)) {
                    $historyComment = __('Hipay payment notification') . " - "
                        . "OPERATION: '" . $operation . "', "
                        . "STATUS: '" . $status . "', "
                        . "DATE: '" . $date . " " . $time . "', "
                        . "TRANSACTION ID: '" . $transid . "', "
                        . "PAID AMOUNT: '" . $origAmount . " " . $origCurrency . "', "
                        . "CUSTOMER EMAIL: '" . $emailClient . "'"
                        . "3d SECURE: " . $is3ds;
                    $this->logger->debug($historyComment);
                    $order->addStatusHistoryComment($historyComment);
                    $totalDue = $order->getTotalDue();
                    $baseTotalDue = $order->getBaseTotalDue();
                    if ($operation == Constant::RESPONSE_TYPE_CAPTURE) {
                        $isCapture = $payment->getAdditionalInformation("is_capture");
                        if ($isCapture == "1") {
                        } else {
                            $payment->setAmountAuthorized($totalDue);
                            $payment->setBaseAmountAuthorized($baseTotalDue);
                            $payment->capture(null);
                        }
                    }
                    if ($operation == Constant::RESPONSE_TYPE_AUTH) {
                        $payment->authorize(true, $baseTotalDue);
                        $payment->setAmountAuthorized($totalDue);
                    }
                    if ($operation == Constant::RESPONSE_TYPE_REFUND) {
                    }
                    if ($operation == Constant::RESPONSE_TYPE_CANCEL) {
                    }
                    $order->save();
                }
            } else {
                $this->logger->debug("response missing result or response status fail");
            }
        } catch (\Exception $e) {
            $this->logger->debug('handleDataResponse exception, Message : ' . $e->getMessage());
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function getPaymentDescription($order)
    {
        $storeName = $this->storeManager->getStore()->getName();

        return "Order " . $order->getIncrementId() . " at " . $storeName;
    }
}
