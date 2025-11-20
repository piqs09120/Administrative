<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\UserConsent;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePoliciesAccepted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $active = Policy::where('is_active', true)->get(['id', 'version']);
        $accepted = UserConsent::where('user_id', Auth::id())
            ->whereIn('policy_id', $active->pluck('id'))
            ->get()
            ->keyBy('policy_id');

        $missing = $active->first(function ($policy) use ($accepted) {
            return !isset($accepted[$policy->id]) || 
                   $accepted[$policy->id]->version < $policy->version;
        });

        // Expose a flag for the view (we'll open the modal with JS)
        view()->share('mustAcceptPolicies', (bool)$missing);

        return $next($request);
    }
}





