<?php

namespace Webkul\MpAuthorizeNet\Payment;

/**
 * MpAuthorizeNetPayment method class
 *
 * @author    Shaiv Roy <shaiv.roy361@webkul.com> 
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class MpAuthorizeNetPayment extends MpAuthorizeNet
{
    protected $code = 'mpauthorizenet';

    /**
     * Get the redirect url for redirecting to
     */
    public function getRedirectUrl()
    {
        return route('mpauthorizenet.make.payment');
    }

    /**
     * Mp authorize Net web URL generic getter
     *
     * @param array $params
     * @return string
     */
    public function getMpauthorizeNetUrl($params = [])
    {
        $this->getRedirectUrl();
    }
}