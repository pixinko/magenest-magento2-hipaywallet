<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 1/8/17
 * Time: 23:59
 */

namespace Magenest\HipayWallet\Model\System;

use Magento\Framework\Option\ArrayInterface;

class Locale implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'fr_FR',
                'label' => __('French - France'),
            ],
            [
                'value' => 'fr_BE',
                'label' => __('French - Belgium')
            ],
            [
                'value' => 'en_GB',
                'label' => __('English - Great Britain')
            ],
            [
                'value' => 'en_US',
                'label' => __('English - United States')
            ],
            [
                'value' => 'es_ES',
                'label' => __('Spanish - Spain')
            ],
            [
                'value' => 'de_DE',
                'label' => __('Deutsch - Germany')
            ],
            [
                'value' => 'pt_PT',
                'label' => __('Portuguese - Portugal')
            ],
            [
                'value' => 'pt_BR',
                'label' => __('Portuguese - Brazil')
            ],
            [
                'value' => 'nl_NL',
                'label' => __('Dutch - Holland')
            ],
            [
                'value' => 'nl_BE',
                'label' => __('Dutch - Belgium')
            ],
        ];
    }
}
