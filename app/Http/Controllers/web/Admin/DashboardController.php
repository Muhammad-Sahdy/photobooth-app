<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Protect dashboard with auth middleware (handled by routes now)
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function index(Request $request)
    {
        $from = $request->input('date_from', now()->subDays(7)->toDateString());
        $to   = $request->input('date_to', now()->toDateString());

        $query = Transaction::with('customer')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $transactions = $query->orderByDesc('created_at')->paginate(20);

        $summary = Transaction::where('status', 'paid')
            ->whereBetween('paid_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->selectRaw('COUNT(*) as count, SUM(amount) as total')
            ->first();

        return view('admin.dashboard', [
            'transactions' => $transactions,
            'from'         => $from,
            'to'           => $to,
            'summary'      => $summary,
        ]);
    }
}
