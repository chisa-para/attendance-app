<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GeneralOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        // 💡 ログインしているが、管理者(true)だった場合は一般画面から閉め出す
        if (auth()->user()->admin_status) {
            return redirect('/admin/attendance/list'); // 管理者トップへ強制移動
        }
        
        return $next($request);
    }
}
