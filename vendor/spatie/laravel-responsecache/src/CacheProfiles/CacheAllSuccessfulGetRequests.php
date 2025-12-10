<?php

namespace Spatie\ResponseCache\CacheProfiles;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CacheAllSuccessfulGetRequests extends BaseCacheProfile
{
    public function shouldCacheRequest(Request $request): bool
    {
        if ($this->isRunningInConsole()) {
            return false;
        }

        // Only this request should be cached
        if ($request->type === 'address_search') {
            return true;
        }

        // Paths that must NOT be cached
        $denyPaths = [
            'wine',
            'search',
            'airport',
            'mini-cart',
            'checkout',
            'admin',
            'customer',
            'signIn',
            'custom-order-enquiry',
            'contact-us',
        ];

        foreach ($denyPaths as $path) {
            if (Str::contains($request->path(), $path)) {
                return false;
            }
        }

        // Specific conditions
        if ($request->get('type') === 'airport_fbo_detail') {
            return false;
        }

        if ($request->is('airport/fbo-detail/store')) {
            return false;
        }

        // Homepage without query should not be cached
        if (($request->is('/') || $request->path() === '') && empty($request->query())) {
            return false;
        }

        // Finally, only cache GET requests
        return $request->isMethod('get');
    }

    public function shouldCacheResponse(Response $response): bool
    {
        if (!$this->hasCacheableResponseCode($response)) {
            return false;
        }

        if (!$this->hasCacheableContentType($response)) {
            return false;
        }

        return true;
    }

    public function hasCacheableResponseCode(Response $response): bool
    {
        if ($response->isSuccessful()) {
            return true;
        }

        if ($response->isRedirection()) {
            return true;
        }

        return false;
    }

    public function hasCacheableContentType(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');

        if (str_starts_with($contentType, 'text/')) {
            return true;
        }

        if (Str::contains($contentType, ['/json', '+json'])) {
            return true;
        }

        return false;
    }
}
