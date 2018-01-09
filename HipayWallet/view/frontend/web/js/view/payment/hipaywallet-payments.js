/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'magenest_hipaywallet',
                component: 'Magenest_HipayWallet/js/view/payment/method-renderer/hipaywallet-payments-method'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    }
);