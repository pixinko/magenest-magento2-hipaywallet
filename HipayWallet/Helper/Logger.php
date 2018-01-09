<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 1/2/17
 * Time: 13:25
 */

namespace Magenest\HipayWallet\Helper;

class Logger
{

    protected $logger;
    protected $configHelper;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magenest\HipayWallet\Helper\ConfigHelper $configHelper
    ) {
        $this->logger = $logger;
        $this->configHelper = $configHelper;
    }

    public function debug($message, array $context = array())
    {
        if ($this->configHelper->isLoggerActive()) {
            $msg = "Hipaywallet debug: " . $message;
            $this->logger->debug($msg, $context);
        }
    }
}
