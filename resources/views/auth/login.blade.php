<x-guest-layout>
    <!-- Session Status -->
    @if(session('status'))
        <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs rounded-xl text-center font-medium">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Alamat Email</label>
            <input 
                id="email" 
                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                placeholder="admin@barberbook.test"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-400 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">Kata Sandi</label>
            <input 
                id="password" 
                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition" 
                type="password" 
                name="password" 
                required 
                placeholder="••••••••"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-400 text-xs" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded bg-slate-950 border-slate-800 text-amber-500 focus:ring-amber-500/20 focus:ring-offset-slate-900 w-4 h-4 transition" name="remember">
                <span class="ml-2 text-xs font-medium text-slate-400 group-hover:text-slate-300">Ingat Saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs font-medium text-slate-400 hover:text-amber-400 transition" href="{{ route('password.request') }}">
                    Lupa Sandi?
                </a>
            @endif
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 transition shadow-lg shadow-amber-500/25">
                Masuk ke Dashboard
            </button>
        </div>
    </form>
</x-guest-layout>
