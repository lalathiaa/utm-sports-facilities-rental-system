<x-app-layout>
    <x-slot name="header">New Announcement</x-slot>

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
            .closure-item {
                padding: 10px 12px;
                background: var(--slate-50);
                border: 1px solid var(--slate-200);
                border-radius: var(--radius-md);
                margin-bottom: 8px;
            }
        </style>

        <div class="announcement-form-grid">
            
            {{-- Left Column: Create Announcement Form --}}
            <div style="display:flex;flex-direction:column;gap:20px;">
                <div class="utm-card animate-in">
                    <div class="utm-card-header" style="background:rgba(139,0,0,.04);border-color:rgba(139,0,0,.10);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:var(--utm-maroon);color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                            <div>
                                <div class="utm-card-title">Announcement Details</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Publish updates, news, or facility closure alerts</div>
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

                        <form method="POST" action="{{ route('officer.announcements.store') }}">
                            @csrf

                            <div class="utm-form-group">
                                <label class="utm-label" for="title">Title <span style="color:var(--danger);">*</span></label>
                                <input type="text" id="title" name="title"
                                       class="utm-input {{ $errors->has('title') ? 'error' : '' }}"
                                       value="{{ old('title', request('title')) }}" maxlength="255"
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
                                          placeholder="Write your announcement here…">{{ old('message', request('message')) }}</textarea>
                                <div class="utm-helper-text">Maximum 5,000 characters.</div>
                                @error('message')
                                    <div class="utm-error-msg">{{ $message }}</div>
                                @enderror
                            </div>

                            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;">
                                <a href="{{ route('officer.announcements.index') }}" class="btn btn-outline">Cancel</a>
                                <button type="submit" class="btn btn-primary">Publish Announcement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Column: Facility Closures Import & Tips --}}
            <div style="display:flex;flex-direction:column;gap:20px;">
                
                {{-- Facility Closures Import --}}
                <div class="utm-card animate-in animate-in-delay-1">
                    <div class="utm-card-header" style="background:rgba(5,150,105,.04);border-color:rgba(5,150,105,.12);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:#059669;color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">2</div>
                            <div>
                                <div class="utm-card-title">Link Closures</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Select facility closures to announce</div>
                            </div>
                        </div>
                    </div>
                    <div class="utm-card-body" style="padding:16px 20px;">
                        @if($upcomingClosures->isEmpty())
                            <div style="padding:20px;text-align:center;color:var(--slate-300);font-size:13px;">
                                No upcoming closures scheduled.
                            </div>
                        @else
                            <div style="display:flex;flex-direction:column;gap:14px;">
                                @foreach($upcomingClosures as $facilityName => $closures)
                                    <div style="border:1px solid var(--slate-200);border-radius:var(--radius-md);padding:12px;background:white;">
                                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;gap:8px;">
                                            <span style="font-size:13.5px;font-weight:700;color:var(--slate-800);word-break:break-all;padding-right:4px;">{{ $facilityName }}</span>
                                            <button type="button" class="btn btn-xs btn-outline"
                                                    style="color:#059669;border-color:#059669;background:rgba(5,150,105,.02);flex-shrink:0;"
                                                    onclick="importClosure('{{ addslashes($facilityName) }}', '{{ json_encode($closures) }}')">
                                                Apply
                                            </button>
                                        </div>
                                        <div style="display:flex;flex-direction:column;gap:6px;">
                                            @foreach($closures->take(3) as $c)
                                                <div style="font-size:11.5px;color:var(--slate-500);display:flex;justify-content:space-between;">
                                                    <span>{{ $c->closure_date->format('d M') }} — {{ $c->slotLabel() }}</span>
                                                </div>
                                            @endforeach
                                            @if($closures->count() > 3)
                                                <div style="font-size:10.5px;color:var(--slate-400);font-style:italic;">
                                                    + {{ $closures->count() - 3 }} more closure(s)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tips Card --}}
                <div class="utm-card animate-in animate-in-delay-1" style="border:1px solid rgba(201,168,76,.25);background:rgba(201,168,76,.04);">
                    <div class="utm-card-header" style="border-color:rgba(201,168,76,.15);">
                        <div class="utm-card-title" style="color:#92600A;">💡 Announcement Tips</div>
                    </div>
                    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Announcements are immediately published on all user dashboards.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Use the <strong>"Apply"</strong> button under "Link Closures" to instantly compose a professional closure notice.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Ensure the text is clear, professional, and contains direct instructions for affected users.</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
    function importClosure(facilityName, closuresJson) {
        const closures = JSON.parse(closuresJson);
        const titleInput = document.getElementById('title');
        const messageInput = document.getElementById('message');
        
        titleInput.value = `Notice: Upcoming Closures for ${facilityName}`;
        
        let msg = `Dear renters,\n\nPlease be informed that the ${facilityName} will be temporarily closed on the following date(s) due to maintenance/events:\n\n`;
        
        // Group by date
        const groups = {};
        closures.forEach(c => {
            const d = c.closure_date.split('T')[0];
            if (!groups[d]) groups[d] = [];
            groups[d].push(c);
        });
        
        for (const [dateStr, items] of Object.entries(groups)) {
            const dateObj = new Date(dateStr);
            const formattedDate = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            
            let slotsStr = '';
            const hasFullDay = items.some(item => !item.slot_start);
            if (hasFullDay) {
                slotsStr = 'Full Day';
            } else {
                slotsStr = items.map(item => {
                    const start = item.slot_start.substring(0, 5);
                    const [h, m] = start.split(':');
                    const endH = (parseInt(h) + 1).toString().padStart(2, '0');
                    return `${start} – ${endH}:${m}`;
                }).join(', ');
            }
            const reason = items[0].reason ? ` (${items[0].reason})` : '';
            msg += `• ${formattedDate}: ${slotsStr}${reason}\n`;
        }
        
        msg += `\nWe apologize for any inconvenience caused. Thank you.`;
        messageInput.value = msg;
    }
    </script>
</x-app-layout>
