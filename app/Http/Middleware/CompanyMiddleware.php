<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set default company if not set
        if (!session()->has('selected_company')) {
            session(['selected_company' => 'SCS']);
        }

        // Share company data with all views
        view()->share('selectedCompany', session('selected_company'));
        view()->share('companies', ['CIS', 'SCS']);

        return $next($request);
    }
}
