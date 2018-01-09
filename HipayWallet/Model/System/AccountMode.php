<?php

namespace Magenest\HipayWallet\Model\System;

class AccountMode implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Test')],
            ['value' => 0, 'label' => __('Live')]
        ];
    }
}
