<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\Level;
use Monolog\LogRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DatabaseErrorHandler extends AbstractProcessingHandler
{
    /**
     * Constructor - only handle ERROR level and above
     */
    public function __construct()
    {
        parent::__construct(Level::Warning);
    }

    /**
     * Write the error log record to the database
     */
    protected function write(LogRecord $record): void
    {
        // Extract basic information
        $message = $record->message;
        $level = $record->level->getName();
        $url = request()->fullUrl();
        $path = request()->path();
        
        // Skip logging for asset files and theme-related URLs
        if (
            preg_match('/(\.js|\.css|\.png|\.jpg|\.jpeg|\.gif|\.svg|\.map)$/i', $path) || 
            stripos($path, 'themes/') !== false ||
            stripos($path, 'assets/') !== false
        ) {
            return;
        }


        
        // Insert error log entry into database
        DB::table('logs')->insert([
            'message' => $message,
            'message_type' => $level,
            'page_url' => $url,
            'customer_id' => auth()->check() ? auth()->id() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
