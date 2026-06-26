<section>
    <div class="utm-card animate-in animate-in-delay-2">
        <div class="utm-card-header" style="background:rgba(220,38,38,.04);border-color:rgba(220,38,38,.12);">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:26px;height:26px;border-radius:50%;background:var(--danger);color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">3</div>
                <div>
                    <div class="utm-card-title">Delete Account</div>
                    <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Permanently delete your account and all associated data</div>
                </div>
            </div>
        </div>
        <div class="utm-card-body">

            <div class="utm-alert utm-alert-error" style="margin-bottom:20px;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Once your account is deleted, all of its resources and data will be permanently removed. This action cannot be undone.</span>
            </div>

            <button type="button" class="btn btn-danger"
                    onclick="document.getElementById('delete-account-modal').style.display='flex'">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Account
            </button>

        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="delete-account-modal"
         style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;background:rgba(0,0,0,.45);">
        <div style="background:white;border-radius:var(--radius-xl);padding:32px;max-width:460px;width:90%;box-shadow:var(--shadow-elevated);">

            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(220,38,38,.10);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="var(--danger)" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 style="font-family:'Lora',Georgia,serif;font-size:20px;font-weight:600;color:var(--slate-800);margin:0;">
                    Delete Account?
                </h3>
            </div>

            <p style="font-size:13.5px;color:var(--slate-500);margin:0 0 24px;line-height:1.6;">
                Are you sure you want to delete your account? All of your data will be permanently removed. Please enter your password to confirm.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="utm-form-group">
                    <label for="delete_password" class="utm-label">
                        Password <span style="color:var(--danger);">*</span>
                    </label>
                    <input id="delete_password"
                           type="password" name="password"
                           placeholder="Enter your password to confirm"
                           class="utm-input {{ $errors->userDeletion->has('password') ? 'error' : '' }}">
                    @if($errors->userDeletion->has('password'))
                        <div class="utm-error-msg">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $errors->userDeletion->first('password') }}
                        </div>
                    @endif
                </div>

                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:8px;">
                    <button type="button" class="btn btn-outline"
                            onclick="document.getElementById('delete-account-modal').style.display='none'">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Yes, Delete My Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Auto-open modal if there are deletion errors --}}
    @if($errors->userDeletion->isNotEmpty())
        <script>
            document.getElementById('delete-account-modal').style.display = 'flex';
        </script>
    @endif
</section>