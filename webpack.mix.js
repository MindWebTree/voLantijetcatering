const mix = require('laravel-mix');

/**
 * Admin assets
 */
mix.js('packages/Webkul/Admin/assets/js/app.js', 'public/vendor/webkul/admin/assets/js')
   .sass('packages/Webkul/Admin/assets/sass/app.scss', 'public/vendor/webkul/admin/assets/css');

/**
 * Shop Front (default theme)
 */
mix.js('packages/Webkul/Shop/assets/js/app.js', 'public/vendor/webkul/shop/assets/js')
   .sass('packages/Webkul/Shop/assets/sass/app.scss', 'public/vendor/webkul/shop/assets/css');

/**
 * Velocity theme assets (if you are using Velocity)
 */
mix.js('packages/Webkul/Velocity/assets/js/app.js', 'public/vendor/webkul/velocity/assets/js')
   .sass('packages/Webkul/Velocity/assets/sass/app.scss', 'public/vendor/webkul/velocity/assets/css');

/**
 * Your custom theme (if any)
 * Uncomment and edit path if needed
 */
// mix.js('themes/your-theme/assets/js/app.js', 'public/themes/your-theme/assets/js')
//    .sass('themes/your-theme/assets/sass/app.scss', 'public/themes/your-theme/assets/css');


/**
 * Source maps in development
 */
if (!mix.inProduction()) {
    mix.sourceMaps();
}

/**
 * Versioning in production
 */
if (mix.inProduction()) {
    mix.version();
}
