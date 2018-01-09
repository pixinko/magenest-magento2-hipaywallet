<?php

namespace Magenest\HipayWallet\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class RefundAction implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'account',
                'label' => __('Refund on customers’ HiPay Wallet account')
            ],
            [
                'value' => 'card',
                'label' => __('Refund on credit card'),
            ]
        ];
    }
}
