<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AgentsPolicyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $active = false;
        try {
            $path = base_path('AGENTS.md');
            $active = file_exists($path) && trim(@file_get_contents($path)) !== '';
        } catch (\Throwable $e) {
            $active = false;
        }
        view()->share('agentsPoliciesActive', $active);
        return $next($request);
    }
}

