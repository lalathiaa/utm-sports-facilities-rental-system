<section>
    <div class="utm-card animate-in animate-in-delay-1">
        <div class="utm-card-header" style="background:rgba(5,150,105,.04);border-color:rgba(5,150,105,.12);">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:26px;height:26px;border-radius:50%;background:#059669;color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">2</div>
                <div>
                    <div class="utm-card-title">Update Password</div>
                    <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Ensure your account uses a long, random password to stay secure</div>
                </div>
            </div>
        </div>
        <div class="utm-card-body">

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div style="display:flex;flex-direction:column;gap:16px;">

                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label for="update_password_current_password" class="utm-label">Current Password</label>
                        <input id="update_password_current_password"
                               type="password" name="current_password"
                               autocomplete="current-password"
                               placeholder="Enter your current password"
                               class="utm-input {{ $errors->updatePassword->has('current_password') ? 'error' : '' }}">
                        @if($errors->updatePassword->has('current_password'))
                            <div class="utm-error-msg">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $errors->updatePassword->first('current_password') }}
                            </div>
                        @endif
                    </div>

                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label for="update_password_password" class="utm-label">New Password</label>
                        <input id="update_password_password"
                               type="password" name="password"
                               autocomplete="new-password"
                               placeholder="Create a strong new password"
                               class="utm-input {{ $errors->updatePassword->has('password') ? 'error' : '' }}">
                        @if($errors->updatePassword->has('password'))
                            <div class="utm-error-msg">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $errors->updatePassword->first('password') }}
                            </div>
                        @endif
                    </div>

                    <div class="utm-form-group" style="margin-bottom:0;">
                        <label for="update_password_password_confirmation" class="utm-label">Confirm New Password</label>
                        <input id="update_password_password_confirmation"
                               type="password" name="password_confirmation"
                               autocomplete="new-password"
                               placeholder="Re-enter your new password"
                               class="utm-input {{ $errors->updatePassword->has('password_confirmation') ? 'error' : '' }}">
                        @if($errors->updatePassword->has('password_confirmation'))
                            <div class="utm-error-msg">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $errors->updatePassword->first('password_confirmation') }}
                            </div>
                        @endif
                    </div>

                </div>

                <div style="display:flex;align-items:center;gap:14px;margin-top:24px;">
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Update Password
                    </button>
                    @if (session('status') === 'password-updated')
                        <span style="font-size:13px;color:var(--success);font-weight:600;display:flex;align-items:center;gap:5px;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Password updated
                        </span>
                    @endif
                </div>

            </form>
        </div>
    </div>
</section>