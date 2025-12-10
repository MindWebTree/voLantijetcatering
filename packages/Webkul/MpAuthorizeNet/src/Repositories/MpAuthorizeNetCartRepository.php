<?php

namespace Webkul\MpAuthorizeNet\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * Marketplace Authorize Net Payment
 *
 * @author    Shaiv Roy <shaiv.roy361@webkul.com> 
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class MpAuthorizeNetCartRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\MpAuthorizeNet\Contracts\MpAuthorizeNetCart';
    }
}