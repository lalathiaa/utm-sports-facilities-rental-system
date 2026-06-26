<x-guest-layout>

    <div class="animate-in">
        <div style="margin-bottom:36px;">
            <h2 style="font-family:'Lora',Georgia,serif;font-size:26px;font-weight:600;color:var(--slate-800);margin:0 0 8px;">
                Welcome back
            </h2>
            <p style="font-size:14px;color:var(--slate-400);margin:0;">
                Sign in to your UTM Sports Facilities account
            </p>
        </div>

        {{-- Session Status --}}
        @if(session('status'))
            <div class="utm-alert utm-alert-success" style="margin-bottom:24px;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:0;">
            @csrf

            <div class="utm-form-group animate-in animate-in-delay-1">
                <label for="username" class="utm-label">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                       autocomplete="username"
                       placeholder="Enter your username"
                       class="utm-input {{ $errors->has('username') ? 'error' : '' }}">
                @error('username')
                    <div class="utm-error-msg">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="utm-form-group animate-in animate-in-delay-2">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <label for="password" class="utm-label" style="margin:0;">Password</label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           style="font-size:12px;color:var(--utm-maroon);font-weight:500;text-decoration:none;">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required
                       autocomplete="current-password"
                       placeholder="Enter your password"
                       class="utm-input {{ $errors->has('password') ? 'error' : '' }}">
                @error('password')
                    <div class="utm-error-msg">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div style="display:flex;align-items:center;gap:8px;margin-bottom:28px;" class="animate-in animate-in-delay-2">
                <input id="remember_me" type="checkbox" name="remember"
                       style="width:16px;height:16px;accent-color:var(--utm-maroon);cursor:pointer;">
                <label for="remember_me" style="font-size:13px;color:var(--slate-500);cursor:pointer;">
                    Keep me signed in
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg animate-in animate-in-delay-3"
                    style="justify-content:center;width:100%;">
                Sign In
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>

        @if(Route::has('register'))
            <p style="text-align:center;font-size:13.5px;color:var(--slate-400);margin-top:28px;" class="animate-in animate-in-delay-4">
                Don't have an account?
                <a href="{{ route('register') }}"
                   style="color:var(--utm-maroon);font-weight:600;text-decoration:none;">
                    Register here
                </a>
            </p>
        @endif
    </div>

</x-guest-layout>