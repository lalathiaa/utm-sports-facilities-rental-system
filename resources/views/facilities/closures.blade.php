<x-app-layout>
    <x-slot name="header">Manage Closures</x-slot>

    <div style="max-width:1100px;margin:0 auto;">

        {{-- Back --}}
        <a href="{{ route('facilities.show', $facility) }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;margin-bottom:24px;font-weight:500;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Facility
        </a>

        {{-- Flash --}}
        @if(session('success'))
            <div class="utm-alert utm-alert-success animate-in">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="utm-alert utm-alert-error animate-in">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
                </div>
            </div>
        @endif

        {{-- Page Header --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
            <div>
                <h1 style="font-family:'Lora',Georgia,serif;font-size:22px;font-weight:600;color:var(--slate-800);margin:0 0 4px;">
                    Closure Schedule
                </h1>
                <p style="font-size:13.5px;color:var(--slate-400);margin:0;">
                    {{ $facility->name }} — mark specific dates or time slots as unavailable
                </p>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

            {{-- ══ LEFT: Add Closure Form ══ --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                <div class="utm-card animate-in">
                    <div class="utm-card-header" style="background:rgba(139,0,0,.04);border-color:rgba(139,0,0,.10);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:var(--utm-maroon);color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                            <div>
                                <div class="utm-card-title">Set Closure</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Choose a date and mark it fully or partially unavailable</div>
                            </div>
                        </div>
                    </div>
                    <div class="utm-card-body">
                        <form method="POST" action="{{ route('facilities.closures.store', $facility) }}" id="closure-form">
                            @csrf

                            {{-- Date --}}
                            <div class="utm-form-group">
                                <label class="utm-label">Date <span style="color:var(--danger);">*</span></label>
                                <input type="date" name="closure_date" id="closure-date"
                                       value="{{ old('closure_date', $date) }}"
                                       min="{{ now()->toDateString() }}"
                                       class="utm-input" style="max-width:220px;">
                            </div>

                            {{-- Closure Type --}}
                            <div class="utm-form-group">
                                <label class="utm-label">Closure Type <span style="color:var(--danger);">*</span></label>
                                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                                    <label style="display:flex;align-items:center;gap:8px;padding:10px 16px;border:1.5px solid var(--slate-200);border-radius:var(--radius-md);cursor:pointer;transition:all .15s;"
                                           id="label-full-day">
                                        <input type="radio" name="closure_type" value="full_day"
                                               id="type-full-day"
                                               {{ old('closure_type','full_day') === 'full_day' ? 'checked' : '' }}
                                               onchange="toggleClosureType()"
                                               style="accent-color:var(--utm-maroon);width:16px;height:16px;">
                                        <div>
                                            <div style="font-size:13.5px;font-weight:600;color:var(--slate-700);">Full Day</div>
                                            <div style="font-size:11.5px;color:var(--slate-400);">All slots unavailable</div>
                                        </div>
                                    </label>
                                    <label style="display:flex;align-items:center;gap:8px;padding:10px 16px;border:1.5px solid var(--slate-200);border-radius:var(--radius-md);cursor:pointer;transition:all .15s;"
                                           id="label-specific">
                                        <input type="radio" name="closure_type" value="specific_slots"
                                               id="type-specific"
                                               {{ old('closure_type') === 'specific_slots' ? 'checked' : '' }}
                                               onchange="toggleClosureType()"
                                               style="accent-color:var(--utm-maroon);width:16px;height:16px;">
                                        <div>
                                            <div style="font-size:13.5px;font-weight:600;color:var(--slate-700);">Specific Slots</div>
                                            <div style="font-size:11.5px;color:var(--slate-400);">Choose time slots</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Slot Picker (shown only for specific_slots) --}}
                            <div id="slot-picker" style="{{ old('closure_type') === 'specific_slots' ? '' : 'display:none;' }}margin-bottom:20px;">
                                <label class="utm-label">Select Slots to Close <span style="color:var(--danger);">*</span></label>
                                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:8px;margin-top:6px;">
                                    @php
                                        $allSlots  = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00'];
                                        $oldSlots  = old('slots', []);
                                    @endphp
                                    @foreach($allSlots as $slot)
                                        @php
                                            $end      = date('H:i', strtotime($slot) + 3600);
                                            $isClosed = in_array($slot, $closedSlots);
                                            $isOld    = in_array($slot, $oldSlots);
                                        @endphp
                                        <label style="cursor:pointer;">
                                            <input type="checkbox" name="slots[]" value="{{ $slot }}"
                                                   class="closure-slot-cb" style="display:none;"
                                                   {{ $isOld ? 'checked' : '' }}>
                                            <div class="slot-card {{ $isClosed ? 'booked' : ($isOld ? 'selected' : 'available') }}"
                                                 data-slot="{{ $slot }}">
                                                <div style="font-weight:600;font-size:13px;">{{ $slot }}</div>
                                                <div style="font-size:11px;opacity:.7;margin-top:2px;">– {{ $end }}</div>
                                                @if($isClosed)
                                                    <div style="font-size:10px;color:var(--slate-400);margin-top:3px;">Closed</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Reason --}}
                            <div class="utm-form-group">
                                <label class="utm-label">Reason (optional)</label>
                                <input type="text" name="reason"
                                       value="{{ old('reason') }}"
                                       placeholder="e.g. Maintenance, Public holiday, Event"
                                       class="utm-input">
                                <div class="utm-helper-text">This will be shown to users when they try to book.</div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Set Unavailable
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Current Day Closures --}}
                <div class="utm-card animate-in animate-in-delay-1">
                    <div class="utm-card-header">
                        <div>
                            <div class="utm-card-title">
                                Closures on {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                            </div>
                            <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">
                                Currently blocked slots for the selected date
                            </div>
                        </div>
                        @if($closuresOnDate->isNotEmpty())
                            <form method="POST" action="{{ route('facilities.closures.destroy-date', $facility) }}"
                                  onsubmit="return confirm('Remove ALL closures for {{ $date }}?')">
                                @csrf @method('DELETE')
                                <input type="hidden" name="closure_date" value="{{ $date }}">
                                <button type="submit" class="btn btn-sm"
                                        style="background:rgba(220,38,38,.08);color:var(--danger);border:1px solid rgba(220,38,38,.15);">
                                    Clear All
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($closuresOnDate->isEmpty())
                        <div style="padding:32px;text-align:center;color:var(--slate-300);font-size:13.5px;">
                            No closures set for this date.
                        </div>
                    @elseif($hasFullDayClosure)
                        <div style="padding:20px 24px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:rgba(220,38,38,.05);border:1px solid rgba(220,38,38,.12);border-radius:var(--radius-md);">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:8px;height:8px;border-radius:50%;background:var(--danger);flex-shrink:0;"></div>
                                    <div>
                                        <div style="font-size:13.5px;font-weight:600;color:var(--slate-800);">Full Day Closure</div>
                                        @if($closuresOnDate->first()->reason)
                                            <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">{{ $closuresOnDate->first()->reason }}</div>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('facilities.closures.destroy', [$facility, $closuresOnDate->first()]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm"
                                            style="background:rgba(220,38,38,.08);color:var(--danger);border:1px solid rgba(220,38,38,.15);">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div style="padding:16px 24px;display:flex;flex-direction:column;gap:8px;">
                            @foreach($closuresOnDate as $closure)
                                <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:var(--slate-50);border:1px solid var(--slate-200);border-radius:var(--radius-md);">
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:8px;height:8px;border-radius:50%;background:var(--warning);flex-shrink:0;"></div>
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:var(--slate-700);">{{ $closure->slotLabel() }}</div>
                                            @if($closure->reason)
                                                <div style="font-size:11.5px;color:var(--slate-400);margin-top:1px;">{{ $closure->reason }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('facilities.closures.destroy', [$facility, $closure]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm"
                                                style="background:rgba(220,38,38,.08);color:var(--danger);border:1px solid rgba(220,38,38,.15);">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- ══ RIGHT: Sidebar Container ══ --}}
            <div style="display:flex;flex-direction:column;gap:20px;position:sticky;top:80px;">

                {{-- Upcoming Closures Card --}}
                <div class="utm-card animate-in animate-in-delay-1" style="margin:0;">
                    <div class="utm-card-header">
                        <div class="utm-card-title">Upcoming Closures</div>
                    </div>
                    @if($upcomingClosures->isEmpty())
                        <div style="padding:32px 20px;text-align:center;color:var(--slate-300);font-size:13px;">
                            No upcoming closures scheduled.
                        </div>
                    @else
                        <div style="max-height:360px;overflow-y:auto;">
                            @foreach($upcomingClosures as $closureDate => $group)
                                <div style="padding:14px 20px;border-bottom:1px solid var(--slate-100);">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                        <div style="font-size:12.5px;font-weight:700;color:var(--slate-600);">
                                            {{ \Carbon\Carbon::parse($closureDate)->format('d M Y') }}
                                            @if(\Carbon\Carbon::parse($closureDate)->isToday())
                                                <span style="font-size:10px;background:rgba(37,99,235,.10);color:#1D4ED8;padding:1px 6px;border-radius:100px;margin-left:4px;font-weight:600;">Today</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('facilities.closures.index', [$facility, 'date' => $closureDate]) }}"
                                           style="font-size:11px;color:var(--utm-maroon);font-weight:600;text-decoration:none;">
                                            Edit
                                        </a>
                                    </div>
                                    <div style="display:flex;flex-wrap:wrap;gap:5px;">
                                        @if($group->whereNull('slot_start')->isNotEmpty())
                                            <span style="font-size:11px;padding:3px 8px;border-radius:100px;background:rgba(220,38,38,.10);color:var(--danger);font-weight:600;">
                                                Full Day
                                            </span>
                                            @if($group->first()->reason)
                                                <span style="font-size:11px;color:var(--slate-400);padding:3px 0;word-break:break-all;">
                                                    — {{ $group->first()->reason }}
                                                </span>
                                            @endif
                                        @else
                                            @foreach($group as $c)
                                                <span style="font-size:11px;padding:3px 8px;border-radius:100px;background:rgba(217,119,6,.10);color:#B45309;font-weight:600;">
                                                    {{ substr($c->slot_start, 0, 5) }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Announce Closures Card --}}
                <div class="utm-card animate-in animate-in-delay-2" style="margin:0;border:1px solid rgba(201,168,76,.25);background:rgba(201,168,76,.04);">
                    <div class="utm-card-header" style="border-color:rgba(201,168,76,.15);">
                        <div class="utm-card-title" style="color:#92600A;">📣 Announce Closures</div>
                    </div>
                    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
                        <p style="font-size:12.5px;color:#92600A;margin:0;line-height:1.5;">
                            Broadcast these facility closures to all users by publishing an official announcement.
                        </p>
                        @if($upcomingClosures->isEmpty())
                            <button class="btn btn-primary" disabled style="width:100%;justify-content:center;gap:6px;background:var(--slate-200);border-color:var(--slate-200);color:var(--slate-400);cursor:not-allowed;">
                                No Closures to Announce
                            </button>
                        @else
                            @php
                                $annTitle = "Notice: Upcoming Closures for " . $facility->name;
                                $annMsg = "Dear renters,\n\nPlease be informed that " . $facility->name . " will be temporarily closed on the following date(s) due to maintenance/events:\n\n";
                                foreach ($upcomingClosures as $cDate => $group) {
                                    $formattedDate = \Carbon\Carbon::parse($cDate)->format('d M Y');
                                    $slotsStr = '';
                                    if ($group->whereNull('slot_start')->isNotEmpty()) {
                                        $slotsStr = 'Full Day';
                                    } else {
                                        $slotsStr = $group->map(fn($c) => substr($c->slot_start, 0, 5))->implode(', ');
                                    }
                                    $reason = $group->first()->reason ? ' (' . $group->first()->reason . ')' : '';
                                    $annMsg .= "• {$formattedDate}: {$slotsStr}{$reason}\n";
                                }
                                $annMsg .= "\nWe apologize for any inconvenience caused. Thank you.";
                            @endphp
                            <a href="{{ route('officer.announcements.create', ['title' => $annTitle, 'message' => $annMsg]) }}"
                               class="btn btn-primary"
                               style="width:100%;justify-content:center;gap:6px;background:#92600A;border-color:#92600A;color:white;">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                                Announce Closures
                            </a>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
    function toggleClosureType() {
        const isFull   = document.getElementById('type-full-day').checked;
        const picker   = document.getElementById('slot-picker');
        const lblFull  = document.getElementById('label-full-day');
        const lblSpec  = document.getElementById('label-specific');

        picker.style.display = isFull ? 'none' : 'block';

        const activeStyle   = 'border-color:var(--utm-maroon);background:rgba(139,0,0,.03);';
        const inactiveStyle = 'border-color:var(--slate-200);background:white;';
        lblFull.style.cssText += isFull ? activeStyle : inactiveStyle;
        lblSpec.style.cssText += isFull ? inactiveStyle : activeStyle;
    }

    // Slot card toggle
    document.querySelectorAll('.closure-slot-cb').forEach(cb => {
        const card = cb.nextElementSibling;
        if (card.classList.contains('booked')) return; // already closed, not toggleable
        cb.parentElement.addEventListener('click', e => {
            e.preventDefault();
            cb.checked = !cb.checked;
            card.className = 'slot-card ' + (cb.checked ? 'selected' : 'available');
        });
    });

    // Sync date input → reload page
    document.getElementById('closure-date').addEventListener('change', function () {
        const url = new URL(window.location.href);
        url.searchParams.set('date', this.value);
        window.location.href = url.toString();
    });

    // Init radio style on load
    toggleClosureType();
    </script>
</x-app-layout>