<?php

namespace App\Http\Middleware;

use Closure;

class SecurityAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->guard('security')->check()) {
            session()->flash('error', 'Sesi anda sudah berakhir, silahkan login kembali.');
            return route('login');
            // return redirect(route('login'))->with('error', 'Sesi anda sudah berakhir, silahkan login kembali.');
        }

        return $next($request);
    }
}
