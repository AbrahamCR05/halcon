<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'ordersCount' => $request->user()
                ? Order::whereNull('deleted_at')->count()
                : 0,
            'flash' => [
    'success'        => fn () => $request->session()->get('success'),
    'error'          => fn () => $request->session()->get('error'),
    'lookup_result'  => fn () => $request->session()->get('lookup_result'),
    'lookup_error'   => fn () => $request->session()->get('lookup_error'),
],
        ]);
    }
}