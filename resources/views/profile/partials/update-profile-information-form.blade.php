<section>
    <div class="utm-card animate-in">
        <div class="utm-card-header" style="background:rgba(139,0,0,.04);border-color:rgba(139,0,0,.10);">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:26px;height:26px;border-radius:50%;background:var(--utm-maroon);color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                <div>
                    <div class="utm-card-title">Profile Information</div>
                    <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Update your account's profile information and email address</div>
                </div>
            </div>
        </div>
        <div class="utm-card-body">

            <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div style="display:flex;flex-direction:column;gap:16px;">

                    {{-- Full Name --}}
                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label for="fullname" class="utm-label">Full Name</label>
                        <input id="fullname" type="text" name="name"
                               value="{{ old('name', $user->fullname) }}"
                               required autofocus autocomplete="name"
                               class="utm-input {{ $errors->has('name') ? 'error' : '' }}">
                        @error('name')
                            <div class="utm-error-msg">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Username (read-only) --}}
                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label class="utm-label">Username</label>
                        <input type="text" value="{{ $user->username }}" disabled
                               class="utm-input" style="background:var(--slate-50);color:var(--slate-400);cursor:not-allowed;">
                        <div class="utm-helper-text">Username cannot be changed.</div>
                    </div>

                    {{-- Email --}}
                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label for="email" class="utm-label">Email Address</label>
                        <input id="email" type="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               required autocomplete="username"
                               class="utm-input {{ $errors->has('email') ? 'error' : '' }}">
                        @error('email')
                            <div class="utm-error-msg">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="utm-alert utm-alert-warning" style="margin-top:10px;margin-bottom:0;">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    Your email address is unverified.
                                    <button form="send-verification"
                                            style="background:none;border:none;padding:0;color:inherit;font-weight:700;cursor:pointer;text-decoration:underline;font-size:inherit;">
                                        Click here to resend the verification email.
                                    </button>
                                    @if (session('status') === 'verification-link-sent')
                                        <div style="margin-top:6px;font-weight:600;">A new verification link has been sent.</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- IC Number (read-only) --}}
                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label class="utm-label">IC Number</label>
                        <input type="text" value="{{ $user->ic_number }}" disabled
                               class="utm-input" style="background:var(--slate-50);color:var(--slate-400);cursor:not-allowed;">
                    </div>

                    {{-- Matric / Staff ID (read-only if present) --}}
                    @if($user->matric_number)
                        <div class="utm-form-group" style="margin-bottom:0;">
                            <label class="utm-label">Matric Number</label>
                            <input type="text" value="{{ $user->matric_number }}" disabled
                                   class="utm-input" style="background:var(--slate-50);color:var(--slate-400);cursor:not-allowed;">
                        </div>
                    @endif
                    @if($user->staff_id)
                        <div class="utm-form-group" style="margin-bottom:0;">
                            <label class="utm-label">Staff ID</label>
                            <input type="text" value="{{ $user->staff_id }}" disabled
                                   class="utm-input" style="background:var(--slate-50);color:var(--slate-400);cursor:not-allowed;">
                        </div>
                    @endif

                </div>

                <div style="display:flex;align-items:center;gap:14px;margin-top:24px;">
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                    @if (session('status') === 'profile-updated')
                        <span style="font-size:13px;color:var(--success);font-weight:600;display:flex;align-items:center;gap:5px;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Saved successfully
                        </span>
                    @endif
                </div>

            </form>
        </div>
    </div>
</section>