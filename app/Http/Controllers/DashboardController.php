<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard');
    }

    /**
     * GET /api/dashboard/stats
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'total_accounts' => Account::count(),
            'total_balance' => (float) Account::sum('balance'),
            'transactions_today' => Transaction::whereDate('created_at', today())->count(),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }
}