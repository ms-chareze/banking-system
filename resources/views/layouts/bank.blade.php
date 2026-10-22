<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Simple Bank')</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        nav { background: #14365d; padding: 12px 24px; display: flex; gap: 20px; }
        nav a { color: #fff; text-decoration: none; font-weight: 600; }
        main { max-width: 900px; margin: 24px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.1); margin-bottom: 16px; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .stat .value { font-size: 1.8rem; font-weight: 700; }
        .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 12px; }
        .alert-warning { background: #fff4e5; border: 1px solid #f5a623; }
        .alert-success { background: #e6f7ec; border: 1px solid #34a853; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #e5e7eb; }
        label { display: block; margin-top: 12px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; }
        button { margin-top: 16px; padding: 10px 18px; background: #14365d; color: #fff; border: 0; border-radius: 6px; cursor: pointer; }
        .text-danger { color: #d93025; font-size: .9rem; }
    </style>
</head>
<body>
<nav>
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('transactions.index') }}">Transactions</a>
    <a href="{{ route('transactions.create') }}">New Transaction</a>
</nav>

<main>
    @auth
        @foreach (auth()->user()->unreadNotifications as $notification)
            <div class="alert alert-warning">{{ $notification->data['message'] ?? 'Notification' }}</div>
        @endforeach
    @endauth

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>