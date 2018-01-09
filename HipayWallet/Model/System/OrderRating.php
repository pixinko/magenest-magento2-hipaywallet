<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/29/16
 * Time: 16:02
 */

namespace Magenest\HipayWallet\Model\System;

use Magento\Framework\Option\ArrayInterface;

class OrderRating implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => '+12',
                'label' => __('For ages 12 and over'),
            ],
            [
                'value' => '+16',
                'label' => __('For ages 16 and over')
            ],
            [
                'value' => '+18',
                'label' => __('For ages 18 and over')
            ],
            [
                'value' => 'ALL',
                'label' => __('For all ages')
            ],
        ];
    }
}
