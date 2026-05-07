<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'      => Order::whereNull('deleted_at')->count(),
            'ordered'    => Order::whereNull('deleted_at')->where('status', 'Ordered')->count(),
            'in_process' => Order::whereNull('deleted_at')->where('status', 'In Process')->count(),
            'in_route'   => Order::whereNull('deleted_at')->where('status', 'In Route')->count(),
            'delivered'  => Order::whereNull('deleted_at')->where('status', 'Delivered')->count(),
        ];

        $recentOrders = Order::with('creator')
            ->whereNull('deleted_at')
            ->latest()
            ->take(8)
            ->get();

        return Inertia::render('Dashboard', [
            'stats'        => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}