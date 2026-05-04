<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\MusicDistributionModel;

class CheckStatus
{
    public function handle(Request $request, Closure $next)
    {
        $code = $request->route('code');
        $release = MusicDistributionModel::where('code', $code)->first();
        
        if (!$release || $release->status === 'denied') {
            return redirect()->back()->with('error', 'Access denied');
        }

        return $next($request);
    }
} 