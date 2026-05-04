<?php

namespace App\Http\Middleware\Public;
use App\Models\BulletinBoardIpModel;
use App\Models\BulletinBoardModel;
use Closure;
use Carbon\Carbon;

class CheckThreadView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $prefix;
    function __construct()
    {
    }
    public function handle($request, Closure $next)
    {
        $code = $request->code;
        $thread = BulletinBoardModel::where('code',$code)->first();
        if(!$thread) {
            return redirect(rrt_route('public/home/index'));
        }

        $threadId = $thread->id ;
        $ip = $request->ip();
        $today = Carbon::today();

        $viewExists = BulletinBoardIpModel::where('thread_id', $threadId)
                                ->where('ip_address', $ip)
                                ->whereDate('created_at', $today)
                                ->exists();
       

        if (!$viewExists) {
            BulletinBoardModel::find($threadId)->increment('view');
            BulletinBoardIpModel::create([
                'thread_id' => $threadId,
                'ip_address' => $ip,
            ]);
        }
        return $next($request);
    }
}
