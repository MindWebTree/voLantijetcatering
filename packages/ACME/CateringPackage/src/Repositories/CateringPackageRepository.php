<?php

namespace ACME\CateringPackage\Repositories;

use Webkul\Core\Eloquent\Repository;


class CateringPackageRepository extends Repository
{   
    
    /**
    * Specify Model class name
    *
    * @return mixed
    */
    
    function model()
    {
        return 'ACME\CateringPackage\Contracts\Delivery_location_airport';
    }
   

}