<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlugMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Project::where('slug', $request->slug)->exists()) {
            DB::setDefaultConnection($request->slug);
            return $next($request);
        } else {
            return response([
                'message' => 'Slug Not Found'
            ], 500);
        }
    }
}
