<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Volentile Jet catering'),


    // // sandeep Add code 
    'client_id' => env('QUICKBOOKS_CLIENT_ID', 'ABIqeFgZKCl7HU8Nn2R7l4H1DxKzVSTdCxA8HqmJHTmAT6i3tH'),
    'client_secret' => env('QUICKBOOKS_CLIENT_SECRET', 'CWUfVRwsC7ywFJM6bQuU84EfAgTbvAlSXEZEKkXQ'),
    'redirect_uri' => env('QUICKBOOKS_REDIRECT_URI', 'https://catering.volantiscottsdale.com/callback'),
    'scope' => env('SCOPE', 'com.intuit.quickbooks.accounting com.intuit.quickbooks.payment'),
    'baseUrl' => env('QUICKBOOKS_ENVIRONMENT', 'production'),
    'company_id' => env('QUICKBOOKS_REALM_ID', '123146565838279'),



    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://127.0.0.1:8000'),

    /*
    |--------------------------------------------------------------------------
    | Application Admin URL
    |--------------------------------------------------------------------------
    |
    | This URL suffix is used to define the admin url for example
    | admin/ or backend/
    |
    */

    'admin_url' => env('APP_ADMIN_URL', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'America/Los_Angeles'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Default Country
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default country by country code.
    | Ensure it is uppercase and reflects the 'code' column of the
    | countries table.
    |
    | for example: DE EN FR
    | (use capital letters!)
    */

    'default_country' => null,

    /*
    |--------------------------------------------------------------------------
    | Base Currency Code
    |--------------------------------------------------------------------------
    |
    | Here you may specify the base currency code for your application.
    |
    */

    'currency' => env('APP_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Default channel Code
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default channel code for your application.
    |
    */

    'channel' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',
    'company_reference_no' => env('company_reference_no', 'C552312132' ),
    'company_vat_no' => env('company_vat_no', 'C552312132' ),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        /**
         * Package service providers.
         */
        Astrotomic\Translatable\TranslatableServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,
        Konekt\Concord\ConcordServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Prettus\Repository\Providers\RepositoryServiceProvider::class,

        /**
         * Application service providers.
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        /**
         * Webkul package service providers.
         */
        Webkul\Theme\Providers\ThemeServiceProvider::class,
        Webkul\User\Providers\UserServiceProvider::class,
        Webkul\Admin\Providers\AdminServiceProvider::class,
        Webkul\Ui\Providers\UiServiceProvider::class,
        Webkul\Category\Providers\CategoryServiceProvider::class,
        Webkul\Attribute\Providers\AttributeServiceProvider::class,
        Webkul\Core\Providers\CoreServiceProvider::class,
        Webkul\Core\Providers\EnvValidatorServiceProvider::class,
        Webkul\Shop\Providers\ShopServiceProvider::class,
        Webkul\Customer\Providers\CustomerServiceProvider::class,
        Webkul\Inventory\Providers\InventoryServiceProvider::class,
        Webkul\Product\Providers\ProductServiceProvider::class,
        Webkul\Checkout\Providers\CheckoutServiceProvider::class,
        Webkul\Shipping\Providers\ShippingServiceProvider::class,
        Webkul\Payment\Providers\PaymentServiceProvider::class,
        Webkul\Paypal\Providers\PaypalServiceProvider::class,
        Webkul\Sales\Providers\SalesServiceProvider::class,
        Webkul\Tax\Providers\TaxServiceProvider::class,
        Webkul\CatalogRule\Providers\CatalogRuleServiceProvider::class,
        Webkul\CartRule\Providers\CartRuleServiceProvider::class,
        Webkul\Rule\Providers\RuleServiceProvider::class,
        Webkul\CMS\Providers\CMSServiceProvider::class,
        Webkul\Velocity\Providers\VelocityServiceProvider::class,
        Webkul\BookingProduct\Providers\BookingProductServiceProvider::class,
        Webkul\SocialLogin\Providers\SocialLoginServiceProvider::class,
        Webkul\DebugBar\Providers\DebugBarServiceProvider::class,
        Webkul\Marketing\Providers\MarketingServiceProvider::class,
        Webkul\Notification\Providers\NotificationServiceProvider::class,
        Webkul\Sitemap\Providers\SitemapServiceProvider::class,
        //umesh
        ACME\CateringPackage\Providers\CateringPackageServiceProvider::class,
        L5Swagger\L5SwaggerServiceProvider::class,
        ACME\testpackage\Providers\testpackageServiceProvider::class,
        //shyam


        //Tanish||08/11/2023
        Webkul\MpAuthorizeNet\Providers\MpAuthorizeNetServiceProvider::class,   
        
        ACME\paymentProfile\Providers\paymentProfileServiceProvider::class,

        Webkul\RKREZA\Contact\Providers\ContactServiceProvider::class

    ])->toArray(),
    
    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        'Captcha' => Webkul\Customer\Facades\Captcha::class,
        'Cart' => Webkul\Checkout\Facades\Cart::class,
        'Concord' => Konekt\Concord\Facades\Concord::class,
        'Core' => Webkul\Core\Facades\Core::class,
        'Datagrid' => Webkul\Ui\DataGrid\Facades\DataGrid::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'Helper' => Konekt\Concord\Facades\Helper::class,
        'Image' => Intervention\Image\Facades\Image::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,
        'ProductImage' => Webkul\Product\Facades\ProductImage::class,
        'ProductVideo' => Webkul\Product\Facades\ProductVideo::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
'DB' => Illuminate\Support\Facades\DB::class,
    ])->toArray(),
];
