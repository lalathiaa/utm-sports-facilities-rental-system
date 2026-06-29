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

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div style="display:flex;flex-direction:column;gap:16px;">

                    {{-- Profile Picture --}}
                    <div class="utm-form-group" style="margin-bottom:0; display:flex; align-items:center; gap:20px; padding-bottom:16px; border-bottom:1px solid var(--slate-100);">
                        <img src="{{ $user->profile_picture_url }}" alt="{{ $user->fullname }}"
                             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2.5px solid var(--utm-maroon);box-shadow:0 4px 10px rgba(0,0,0,0.06);flex-shrink:0;">
                        <div>
                            <label for="profile_picture" class="utm-label" style="margin-bottom:6px;">Profile Picture</label>
                            <input id="profile_picture" type="file" name="profile_picture" accept="image/*"
                                   class="utm-input" style="padding:6px 12px;font-size:13px;max-width:280px;">
                            <div style="font-size:12px;color:var(--slate-400);margin-top:5px;">Allowed JPG, PNG. Max size of 2MB</div>
                            @error('profile_picture')
                                <div class="utm-error-msg" style="margin-top:4px;">
                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Full Name --}}
                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label for="fullname" class="utm-label">Full Name</label>
                        <input id="fullname" type="text" name="fullname"
                               value="{{ old('fullname', $user->fullname) }}"
                               required autofocus autocomplete="name"
                               class="utm-input {{ $errors->has('fullname') ? 'error' : '' }}">
                        @error('fullname')
                            <div class="utm-error-msg">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label for="username" class="utm-label">Username</label>
                        <input id="username" type="text" name="username"
                               value="{{ old('username', $user->username) }}"
                               required class="utm-input {{ $errors->has('username') ? 'error' : '' }}">
                        @error('username')
                            <div class="utm-error-msg">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
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

                    {{-- IC Number --}}
                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label for="ic_number" class="utm-label">IC Number</label>
                        <input id="ic_number" type="text" name="ic_number"
                               value="{{ old('ic_number', $user->ic_number) }}"
                               required class="utm-input {{ $errors->has('ic_number') ? 'error' : '' }}">
                        @error('ic_number')
                            <div class="utm-error-msg">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label for="phone_number" class="utm-label">Phone Number</label>
                        <input id="phone_number" type="text" name="phone_number"
                               value="{{ old('phone_number', $user->phone_number) }}"
                               required placeholder="e.g. +60123456789"
                               class="utm-input {{ $errors->has('phone_number') ? 'error' : '' }}">
                        @error('phone_number')
                            <div class="utm-error-msg">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
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