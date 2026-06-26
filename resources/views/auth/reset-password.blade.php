{{-- resources/views/auth/reset-password.blade.php --}}
<x-guest-layout>

    <div class="animate-in">
        <div style="margin-bottom:32px;">
            <h2 style="font-family:'Lora',Georgia,serif;font-size:26px;font-weight:600;color:var(--slate-800);margin:0 0 8px;">
                Set new password
            </h2>
            <p style="font-size:14px;color:var(--slate-400);margin:0;line-height:1.6;">
                Choose a strong password for your UTM Sports Facilities account.
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            {{-- Hidden token --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email Address (read-only pre-fill) --}}
            <div class="utm-form-group animate-in animate-in-delay-1">
                <label for="email" class="utm-label">Email Address</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email', $request->email) }}"
                       required autofocus autocomplete="username"
                       class="utm-input {{ $errors->has('email') ? 'error' : '' }}"
                       placeholder="your@email.com">
                @error('email')
                    <div class="utm-error-msg">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- New Password --}}
            <div class="utm-form-group animate-in animate-in-delay-2">
                <label for="password" class="utm-label">New Password</label>
                <input id="password" type="password" name="password"
                       required autocomplete="new-password"
                       placeholder="Enter new password"
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

            {{-- Confirm Password --}}
            <div class="utm-form-group animate-in animate-in-delay-3">
                <label for="password_confirmation" class="utm-label">Confirm New Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       required autocomplete="new-password"
                       placeholder="Confirm your new password"
                       class="utm-input {{ $errors->has('password_confirmation') ? 'error' : '' }}">
                @error('password_confirmation')
                    <div class="utm-error-msg">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-lg animate-in animate-in-delay-4"
                    style="justify-content:center;width:100%;">
                Reset Password
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </form>

        <p style="text-align:center;font-size:13px;color:var(--slate-400);margin-top:24px;" class="animate-in animate-in-delay-4">
            Remember your password?
            <a href="{{ route('login') }}" style="color:var(--utm-maroon);font-weight:600;text-decoration:none;">
                Back to sign in
            </a>
        </p>
    </div>

</x-guest-layout>
