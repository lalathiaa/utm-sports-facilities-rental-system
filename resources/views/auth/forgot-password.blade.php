{{-- resources/views/auth/forgot-password.blade.php --}}
<x-guest-layout>

    <div class="animate-in">
        <div style="margin-bottom:32px;">
            <a href="{{ route('login') }}"
               style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;margin-bottom:24px;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to sign in
            </a>
            <h2 style="font-family:'Lora',Georgia,serif;font-size:26px;font-weight:600;color:var(--slate-800);margin:0 0 8px;">
                Reset your password
            </h2>
            <p style="font-size:14px;color:var(--slate-400);margin:0;line-height:1.6;">
                Enter your registered email address and we'll send you a link to reset your password.
            </p>
        </div>

        @if(session('status'))
            <div class="utm-alert utm-alert-success">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="utm-form-group animate-in animate-in-delay-1">
                <label for="email" class="utm-label">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="your@email.com"
                       class="utm-input {{ $errors->has('email') ? 'error' : '' }}">
                @error('email')
                    <div class="utm-error-msg">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-lg animate-in animate-in-delay-2"
                    style="justify-content:center;width:100%;">
                Send Reset Link
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </button>
        </form>
    </div>

</x-guest-layout>