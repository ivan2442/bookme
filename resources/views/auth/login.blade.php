<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prihlásenie | BookMe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sand min-h-screen flex items-center justify-center">
    <div class="bg-white/90 border border-slate-100 rounded-3xl shadow-xl shadow-slate-200/60 w-full max-w-md p-8 space-y-6">
        <div class="text-center space-y-1">
            <div class="h-12 w-12 mx-auto rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-300 shadow-lg shadow-emerald-200/60 flex items-center justify-center text-slate-900 font-semibold">B</div>
            <h1 class="font-display text-2xl text-slate-900">Prihlásenie</h1>
            <p class="text-sm text-slate-600">Admin alebo prevádzka</p>
        </div>

        @if(session('error'))
            <div class="card border border-red-200 bg-red-50 text-red-800">
                {{ session('error') }}
            </div>
        @endif
        @if(session('status'))
            <div class="card border border-emerald-200 bg-emerald-50 text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('auth.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="label">Login</label>
                <input type="text" name="email" class="input-control" placeholder="admin" required value="{{ old('email', 'admin') }}">
            </div>
            <div>
                <label class="label">Heslo</label>
                <input type="password" name="password" class="input-control" required>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="remember" name="remember" class="h-4 w-4 border-slate-300 rounded">
                <label for="remember" class="text-sm text-slate-600">Zapamätať</label>
            </div>
            <button type="submit" class="w-full px-4 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold transition shadow-md shadow-slate-300/70">
                Prihlásiť sa
            </button>
        </form>
    </div>
</body>
</html>
