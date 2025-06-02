<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use ACME\paymentProfile\Http\Controllers\Admin\InvoicesController;
use Illuminate\Support\Facades\DB;

class QuickbookRefreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Quickbook:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

          // sandeep get config data and get refresh token 

          try{
                $quickbookRefreshToken = app(InvoicesController::class);
                $configData = $quickbookRefreshToken->getQuickBooksConfig();
                
                $tokenData = DB::table('quickbook_tokens')->where('client_id', $configData['client_id'])->first();

                $refreshToken = $tokenData->refresh_token;

                $tokens = $quickbookRefreshToken->refreshAccessToken($configData['client_id'], $configData['client_secret'], $refreshToken,$configData['company_id']);
                if ($tokens) {
                    $accessToken = $tokens['access_token'];
                    log::info('accessToken');
                } else {
                    return response()->json(['error' => 'Failed to refresh access token'], 401);
                }

          }catch(\Exception){
            file_put_contents('debug.log', 'Error cron generate refresh token: ' . $e->getMessage() . "\n");
            return null;
          }
    }
}
