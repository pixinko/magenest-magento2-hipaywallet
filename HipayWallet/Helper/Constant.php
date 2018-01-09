<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/29/16
 * Time: 14:45
 */

namespace Magenest\HipayWallet\Helper;

class Constant
{
    const ENDPOINT_URL_TEST = "https://test-ws.hipay.com/";
    const ENDPOINT_URL_PRODUCTION = "https://ws.hipay.com/";

    const SERVICE_URL_TEST = "https://test-payment.hipay.com/";
    const SERVICE_URL_PRODUCTION = "https://payment.hipay.com/";

    const URL_PAYMENT = "/soap/payment-v2?wsdl";
    const URL_TRANSACTION = "/soap/transaction-v2?wsdl";
    const URL_REFUND = "/soap/refund-v2?wsdl";

    const PREORDER_OPERATION_NEW = "New";
    const PREORDER_OPERATION_AUTHORIZED = "Authorized";
    const PREORDER_OPERATION_CAPTURED = "Completed";
    const PREORDER_STATUS_OPEN = 1;
    const PREORDER_STATUS_CLOSE = 0;

    const PAYMENT_METHOD = "magenest_hipaywallet";

    const RESPONSE_TYPE_AUTH = "authorization";
    const RESPONSE_TYPE_REFUND = "refund";
    const RESPONSE_TYPE_CAPTURE = "capture";
    const RESPONSE_TYPE_CANCEL = "cancellation";

    const HIPAY_TOKEN = "hipay_token_";

    const ADDITIONAL_PAYMENT_METHOD = "hipay_payment_method";
    const ADDITIONAL_SECRET_KEY = "hipay_secret_key";
    const ADDITIONAL_URL_LINK = "hipay_url_link";
    const ADDITIONAL_TRANS_ID = "hipay_trans_id";
}
