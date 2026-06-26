<x-app-layout>
    <x-slot name="header">Profile</x-slot>

    <style>
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
            max-width: 1100px;
            margin: 0 auto;
        }
        @media (max-width: 1024px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="profile-grid">

        {{-- ── LEFT: Profile Information ── --}}
        <div style="display:flex;flex-direction:column;gap:20px;">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- ── RIGHT: Password + Delete + Tips ── --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            @include('profile.partials.update-password-form')

            @include('profile.partials.delete-user-form')

            {{-- Account Tips --}}
            <div class="utm-card animate-in animate-in-delay-2" style="border:1px solid rgba(201,168,76,.25);background:rgba(201,168,76,.04);">
                <div class="utm-card-header" style="border-color:rgba(201,168,76,.15);">
                    <div class="utm-card-title" style="color:#92600A;">💡 Account Tips</div>
                </div>
                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
                    <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                        <span style="flex-shrink:0;">•</span>
                        <span>Use a <strong>strong password</strong> with a mix of uppercase, lowercase, numbers, and symbols.</span>
                    </div>
                    <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                        <span style="flex-shrink:0;">•</span>
                        <span>Your <strong>username and IC number</strong> are fixed and cannot be changed after registration.</span>
                    </div>
                    <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                        <span style="flex-shrink:0;">•</span>
                        <span>Keep your <strong>email address up-to-date</strong> so you receive booking confirmations and announcements.</span>
                    </div>
                    <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                        <span style="flex-shrink:0;">•</span>
                        <span>Account deletion is <strong>permanent and irreversible</strong> — all your bookings and data will be lost.</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>