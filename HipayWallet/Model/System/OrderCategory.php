<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 12/29/16
 * Time: 15:58
 */

namespace Magenest\HipayWallet\Model\System;

use Magento\Framework\Option\ArrayInterface;

class OrderCategory implements ArrayInterface
{
    public function __construct(
        \Magenest\HipayWallet\Helper\Logger $logger,
        \Magenest\HipayWallet\Helper\ConfigHelper $configHelper
    ) {
        $this->logger = $logger;
        $this->configHelper = $configHelper;
    }

    public function toOptionArray()
    {
        $categoryUrl = $this->configHelper->getCategoryUrl();
        $categoryLists = [];
        $arrayLists = [];
        $this->getCategoryList($categoryUrl, $categoryLists);
        foreach ($categoryLists as $category) {
            $arrayLists[] = [
                'value' => (string)$category['value'],
                'label' => (string)$category['label'],
            ];
        }

        if (!!$arrayLists) {
            return $arrayLists;
        } else {
            return [
                'value' => '',
                'label' => 'Error, please check log file'
            ];
        }
    }

    public function getCategoryList($url, & $optionList)
    {
        try {
            $obj = new \SimpleXMLElement($url, null, true);
        } catch (\Exception $e) {
            return false;
        }
        if (isset($obj->categoriesList)) {
            foreach ($obj->categoriesList as $category) {
                foreach ($category as $item) {
                    $this->logger->debug(var_export($item, true));
                    $label = $item[0];
                    $value = $item[0]->attributes();

                    $optionList[] = array('value' => $value, 'label' => $label);
                }
            }

            return true;
        }
        if (isset($obj->result[0]->message)) {
            $this->logger->debug($obj->result[0]->message);
        }

        return false;
    }
}
