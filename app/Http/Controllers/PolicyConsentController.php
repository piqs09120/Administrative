<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\UserConsent;
use Illuminate\Support\Facades\Auth;

class PolicyConsentController extends Controller
{
    /**
     * Get latest active policies and user's acceptance status
     */
    public function latest()
    {
        $policies = Policy::where('is_active', true)
            ->get(['id', 'slug', 'title', 'content', 'version']);

        // Which of these has the user already accepted?
        $accepted = UserConsent::where('user_id', Auth::id())
            ->whereIn('policy_id', $policies->pluck('id'))
            ->get(['policy_id', 'version'])
            ->keyBy('policy_id');

        return response()->json([
            'policies' => $policies,
            'accepted' => $accepted,
        ]);
    }

    /**
     * Store user consent for policies
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'policies' => 'required|array|min:1',
            'policies.*.id' => 'required|exists:policies,id',
            'policies.*.version' => 'required|integer',
        ]);

        foreach ($data['policies'] as $policy) {
            UserConsent::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'policy_id' => $policy['id'],
                    'version' => $policy['version']
                ],
                [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'accepted_at' => now()
                ]
            );
        }

        return response()->json(['ok' => true]);
    }
}





