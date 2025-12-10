<?php

namespace Webkul\MpAuthorizeNet\Payment;

use Webkul\Payment\Payment\Payment;

/**
 * MpAuthorizeNet class
 *
 * @author    Shaiv Roy <shaiv.roy361@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
abstract class MpAuthorizeNet extends Payment
{

    /**
     * To redirect to the stripe payment page
     */
    public function getMpauthorizeNetUrl()
    {
        return route('mpauthorizenet.make.payment');
    }
}