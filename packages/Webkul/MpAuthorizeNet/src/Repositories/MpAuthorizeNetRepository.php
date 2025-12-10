<?php

namespace Webkul\MpAuthorizeNet\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;


/**
 * MpAuthorizeNet Reposotory
 *
 * @author    Shaiv Roy <shaiv.roy361@webkul.com> 
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class MpAuthorizeNetRepository extends Repository
{  

    public function __construct(App $app)
    {  
        parent::__construct($app); 
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\MpAuthorizeNet\Models\MpAuthorizeNet';
    }

   
    public function getAllChannels()
    {
        $allChannels = core()->getAllChannels();

        foreach($allChannels as $channel) {
            $channels[$channel->id] = $channel->name;
        }

        return $channels;
    }
}
