<x-guest-layout>

    <div class="animate-in">
        <div style="margin-bottom:32px;">
            <h2 style="font-family:'Lora',Georgia,serif;font-size:26px;font-weight:600;color:var(--slate-800);margin:0 0 12px;">
                Verify your email
            </h2>
            <p style="font-size:14.5px;color:var(--slate-500);line-height:1.6;margin:0;">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="utm-alert utm-alert-success" style="margin-bottom:28px; display: flex; align-items: flex-start; gap: 10px;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span style="font-size:13.5px;line-height:1.5;">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </span>
            </div>
        @endif

        <div style="display:flex;flex-direction:column;gap:16px;" class="animate-in animate-in-delay-1">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg" style="justify-content:center;width:100%;">
                    {{ __('Resend Verification Email') }}
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.22 0l-2.25 1.5"/>
                    </svg>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="text-align:center;">
                @csrf
                <button type="submit" class="btn btn-outline" style="justify-content:center;width:100%;">
                    {{ __('Log Out') }}
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

</x-guest-layout>
