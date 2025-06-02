<?php

namespace App\Logging;

use Monolog\Logger;

class DatabaseErrorLogger
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config)
    {
        $logger = new Logger('database_errors');
        $logger->pushHandler(new DatabaseErrorHandler());

        return $logger; 
 
    }
}
