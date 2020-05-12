<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // I cannot get route to work in this place with relative path
            // return route('login', [], false);

            // therefore we find the external url from the database
            $tenant = \DB::table('tenants')->where('subdomain', $_SERVER['SERVER_NAME'])->first();
            if ($tenant)
            {
                return ($tenant->external_url.'/login');
            }

            return route('login');
        }
    }
}
