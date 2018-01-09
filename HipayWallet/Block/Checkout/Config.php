<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/30/16
 * Time: 14:33
 */

namespace Magenest\HipayWallet\Block\Checkout;

class Config extends \Magento\Framework\View\Element\Template
{
    protected $_config;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magenest\HipayWallet\Helper\ConfigHelper $config,
        array $data
    ) {
        $this->_config = $config;
        parent::__construct($context, $data);
    }

    public function getConfig()
    {
        return $this->_config;
    }
}
