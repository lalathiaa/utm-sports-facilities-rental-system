<x-app-layout>
    <x-slot name="header">Edit Announcement</x-slot>

    <div style="max-width:1100px;margin:0 auto;padding:0 16px;">

        <a href="{{ route('officer.announcements.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;margin-bottom:24px;font-weight:500;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Announcements
        </a>

        <style>
            .announcement-form-grid {
                display: grid;
                grid-template-columns: 1fr 340px;
                gap: 24px;
                align-items: start;
            }
            @media (max-width: 1024px) {
                .announcement-form-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <div class="announcement-form-grid">
            
            {{-- Left Column: Edit Form --}}
            <div style="display:flex;flex-direction:column;gap:20px;">
                <div class="utm-card animate-in">
                    <div class="utm-card-header" style="background:rgba(139,0,0,.04);border-color:rgba(139,0,0,.10);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:var(--utm-maroon);color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                            <div>
                                <div class="utm-card-title">Edit Announcement</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Modify announcement information</div>
                            </div>
                        </div>
                    </div>
                    <div class="utm-card-body">
                        @if($errors->any())
                            <div class="utm-alert utm-alert-error" style="margin-bottom:20px;">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <ul style="margin:0;padding-left:16px;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('officer.announcements.update', $announcement) }}">
                            @csrf
                            @method('PUT')

                            <div class="utm-form-group">
                                <label class="utm-label" for="title">Title <span style="color:var(--danger);">*</span></label>
                                <input type="text" id="title" name="title"
                                       class="utm-input {{ $errors->has('title') ? 'error' : '' }}"
                                       value="{{ old('title', $announcement->title) }}" maxlength="255"
                                       placeholder="Announcement title…">
                                @error('title')
                                    <div class="utm-error-msg">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="utm-form-group">
                                <label class="utm-label" for="message">Message <span style="color:var(--danger);">*</span></label>
                                <textarea id="message" name="message" rows="8"
                                          class="utm-input {{ $errors->has('message') ? 'error' : '' }}"
                                          maxlength="5000" style="resize:vertical;"
                                          placeholder="Write your announcement here…">{{ old('message', $announcement->message) }}</textarea>
                                <div class="utm-helper-text">Maximum 5,000 characters.</div>
                                @error('message')
                                    <div class="utm-error-msg">{{ $message }}</div>
                                @enderror
                            </div>

                            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;">
                                <a href="{{ route('officer.announcements.index') }}" class="btn btn-outline">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Column: Information & Tips --}}
            <div style="display:flex;flex-direction:column;gap:20px;">
                
                {{-- Metadata Card --}}
                <div class="utm-card animate-in animate-in-delay-1">
                    <div class="utm-card-header" style="background:rgba(5,150,105,.04);border-color:rgba(5,150,105,.12);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:#059669;color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">2</div>
                            <div>
                                <div class="utm-card-title">Announcement Info</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Publishing details</div>
                            </div>
                        </div>
                    </div>
                    <div class="utm-card-body" style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
                        <div>
                            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:4px;">Original Post Time</div>
                            <div style="font-size:14px;color:var(--slate-700);">{{ $announcement->announcement_time->format('d M Y, H:i') }}</div>
                        </div>
                        <div>
                            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:4px;">Author</div>
                            <div style="font-size:14px;color:var(--slate-700);">{{ $announcement->user->fullname ?? 'Rental Officer' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Tips Card --}}
                <div class="utm-card animate-in animate-in-delay-1" style="border:1px solid rgba(201,168,76,.25);background:rgba(201,168,76,.04);">
                    <div class="utm-card-header" style="border-color:rgba(201,168,76,.15);">
                        <div class="utm-card-title" style="color:#92600A;">💡 Editing Tips</div>
                    </div>
                    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Saving changes will update the announcement content on all user dashboards immediately.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>The update time is not changed. It retains the original publication timestamp.</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
