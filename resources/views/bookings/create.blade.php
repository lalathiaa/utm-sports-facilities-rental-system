<x-app-layout>
    <x-slot name="header">Book Facility</x-slot>

    <a href="{{ route('facilities.show', $facility) }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;margin-bottom:24px;font-weight:500;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Facility
    </a>

    @if($errors->any())
        <div class="utm-alert utm-alert-error animate-in" style="margin-bottom:20px;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <div style="font-weight:600;margin-bottom:4px;">Please fix the following errors:</div>
                <ul style="margin:0;padding-left:16px;">
                    @foreach($errors->all() as $err)
                        <li style="font-size:13px;">{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('bookings.store', $facility) }}" id="booking-form"
          style="max-width:1100px;margin:0 auto;">
        @csrf

        <div class="booking-grid">

            {{-- ══ LEFT: Booking Details ══ --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Section 1: Booking Details --}}
                <div class="utm-card animate-in">
                    <div class="utm-card-header" style="background:rgba(139,0,0,.04);border-color:rgba(139,0,0,.10);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:var(--utm-maroon);color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                            <div>
                                <div class="utm-card-title">Booking Details</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Select date, time slots, and optional equipment</div>
                            </div>
                        </div>
                    </div>
                    <div class="utm-card-body" style="display:flex;flex-direction:column;gap:24px;">

                        {{-- Facility Summary --}}
                        <div style="display:flex;align-items:center;gap:16px;padding:16px;background:var(--slate-50);border-radius:var(--radius-md);border:1px solid var(--slate-200);">
                            @if($facility->image)
                                <img src="{{ Storage::url($facility->image) }}" alt="{{ $facility->name }}"
                                     style="width:56px;height:56px;border-radius:10px;object-fit:cover;flex-shrink:0;">
                            @endif
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:700;font-size:15px;color:var(--slate-800);">{{ $facility->name }}</div>
                                <div style="font-size:13px;color:var(--utm-maroon);font-weight:600;margin-top:2px;">
                                    RM {{ number_format($facility->price, 2) }} / slot
                                </div>
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <div style="font-size:11px;color:var(--slate-400);text-transform:uppercase;letter-spacing:.06em;">Required</div>
                                <div style="font-size:15px;font-weight:700;color:var(--slate-700);">{{ $facility->required_participants }} pax</div>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div>
                            <label class="utm-label">Booking Date <span style="color:var(--danger);">*</span></label>
                            <input type="date" name="booking_date" id="booking-date"
                                   value="{{ old('booking_date', $date) }}"
                                   min="{{ now()->toDateString() }}"
                                   class="utm-input" style="max-width:240px;">
                        </div>

                        {{-- Time Slots --}}
                        <div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px;">
                                <label class="utm-label" style="margin:0;">Time Slot(s) <span style="color:var(--danger);">*</span></label>
                                <div style="display:flex;gap:14px;font-size:12px;color:var(--slate-400);">
                                    <span style="display:flex;align-items:center;gap:5px;">
                                        <span style="width:12px;height:12px;border-radius:3px;background:white;border:1.5px solid var(--slate-300);display:inline-block;"></span>Available
                                    </span>
                                    <span style="display:flex;align-items:center;gap:5px;">
                                        <span style="width:12px;height:12px;border-radius:3px;background:var(--utm-maroon);display:inline-block;"></span>Selected
                                    </span>
                                    <span style="display:flex;align-items:center;gap:5px;">
                                        <span style="width:12px;height:12px;border-radius:3px;background:var(--slate-100);display:inline-block;"></span>Booked
                                    </span>
                                </div>
                            </div>

                            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:8px;">
                                @php
                                    $allSlots = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00'];
                                    $oldSlots = old('slots', []);
                                @endphp
                                @foreach($allSlots as $slot)
                                    @php
                                        $end = date('H:i', strtotime($slot) + 3600);
                                        $isBooked = in_array($slot, $bookedSlots);
                                        
                                        // Block slots whose ending time has passed (if booking date is today)
                                        $isPassed = false;
                                        if (date('Y-m-d', strtotime($date)) === date('Y-m-d')) {
                                            $isPassed = (date('H:i') >= $end);
                                        }
                                        
                                        $isBookedOrPassed = $isBooked || $isPassed;
                                        $isOld = in_array($slot, $oldSlots);
                                    @endphp
                                    <label class="slot-label" style="{{ $isBookedOrPassed ? 'cursor:not-allowed;' : 'cursor:pointer;' }}">
                                        <input type="checkbox" name="slots[]" value="{{ $slot }}"
                                               class="slot-checkbox" style="display:none;"
                                               {{ $isBookedOrPassed ? 'disabled' : '' }} {{ $isOld ? 'checked' : '' }}>
                                        <div class="slot-card {{ $isBookedOrPassed ? 'booked' : ($isOld ? 'selected' : 'available') }}">
                                            <div style="font-weight:600;font-size:13px;">{{ $slot }}</div>
                                            <div style="font-size:11px;opacity:.7;margin-top:2px;">– {{ $end }}</div>
                                            @if($isBooked)
                                                <div style="font-size:10px;color:var(--slate-400);margin-top:3px;">Booked</div>
                                            @elseif($isPassed)
                                                <div style="font-size:10px;color:var(--slate-400);margin-top:3px;">Passed</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <div style="font-size:12px;color:var(--slate-400);margin-top:8px;">Select one or more slots. Each slot = 1 hour. Facility price is charged per slot.</div>
                            @error('slots') <div class="utm-error-msg" style="margin-top:6px;">{{ $message }}</div> @enderror
                        </div>

                        {{-- Equipment --}}
                        @php $availableEquipment = $facility->equipment->where('status','available'); @endphp
                        @if($availableEquipment->isNotEmpty())
                            <div>
                                <label class="utm-label">Equipment (optional)</label>
                                <p style="font-size:12.5px;color:var(--slate-400);margin:0 0 12px;">Equipment price is charged per unit per slot. Tick to add, then set quantity.</p>
                                <div style="display:flex;flex-direction:column;gap:8px;">
                                    @foreach($availableEquipment as $idx => $eq)
                                        @php
                                            $oldEquipment = collect(old('equipment', []));
                                            $isChecked    = $oldEquipment->contains('id', $eq->id);
                                            $oldQty       = $oldEquipment->firstWhere('id', $eq->id)['quantity'] ?? 1;
                                        @endphp
                                        <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;border:1.5px solid var(--slate-200);border-radius:var(--radius-md);transition:border-color .15s;"
                                             onmouseover="this.style.borderColor='var(--utm-maroon)'" onmouseout="this.style.borderColor='var(--slate-200)'">
                                            <input type="checkbox"
                                                   class="equipment-toggle"
                                                   data-eq-index="{{ $idx }}"
                                                   {{ $isChecked ? 'checked' : '' }}
                                                   style="width:17px;height:17px;accent-color:var(--utm-maroon);cursor:pointer;flex-shrink:0;">
                                            <input type="hidden" name="equipment[{{ $idx }}][id]" value="{{ $eq->id }}"
                                                   {{ $isChecked ? '' : 'disabled' }} class="eq-id-input" data-eq-index="{{ $idx }}">
                                            @if($eq->image)
                                                <img src="{{ Storage::url($eq->image) }}" alt="{{ $eq->name }}"
                                                     style="width:40px;height:40px;border-radius:8px;object-fit:cover;flex-shrink:0;">
                                            @endif
                                            <div style="flex:1;min-width:0;">
                                                <div style="font-weight:600;font-size:13.5px;color:var(--slate-800);">{{ $eq->name }}</div>
                                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">
                                                    RM {{ number_format($eq->price, 2) }} / unit / slot · Max {{ $eq->quantity }} unit(s)
                                                </div>
                                            </div>
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <label style="font-size:12px;color:var(--slate-500);white-space:nowrap;">Qty:</label>
                                                <input type="number"
                                                       name="equipment[{{ $idx }}][quantity]"
                                                       class="equipment-qty utm-input"
                                                       style="width:70px;padding:7px 10px;font-size:13px;"
                                                       value="{{ $oldQty }}"
                                                       min="1" max="{{ $eq->quantity }}"
                                                       data-price="{{ $eq->price }}"
                                                       data-eq-index="{{ $idx }}"
                                                       {{ $isChecked ? '' : 'disabled' }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('equipment') <div class="utm-error-msg" style="margin-top:6px;">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        {{-- Price Summary --}}
                        <div style="background:rgba(139,0,0,.04);border:1px solid rgba(139,0,0,.10);border-radius:var(--radius-md);padding:18px 20px;">
                            <div style="font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--utm-maroon);margin-bottom:12px;">
                                Estimated Total
                            </div>
                            <div style="display:flex;flex-direction:column;gap:6px;font-size:13.5px;color:var(--slate-600);">
                                <div style="display:flex;justify-content:space-between;">
                                    <span>Facility × <span id="slot-count">0</span> slot(s)</span>
                                    <span id="facility-subtotal">RM 0.00</span>
                                </div>
                                <div id="equipment-row" style="display:none;justify-content:space-between;">
                                    <span>Equipment</span>
                                    <span id="equipment-subtotal">RM 0.00</span>
                                </div>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-top:12px;padding-top:12px;border-top:1px solid rgba(139,0,0,.10);font-weight:800;font-size:16px;color:var(--utm-maroon);">
                                <span>Total</span>
                                <span id="grand-total">RM 0.00</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ══ RIGHT: Participants + Tips + Guidelines ══ --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                {{-- Section 2: Participant Information --}}
                <div class="utm-card animate-in">
                    <div class="utm-card-header" style="background:rgba(5,150,105,.04);border-color:rgba(5,150,105,.12);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:#059669;color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">2</div>
                            <div>
                                <div class="utm-card-title">Participant Information</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">
                                    {{ $facility->required_participants }} participant(s) required
                                    @if($facility->additionalParticipantsRequired() > 0)
                                        — your details + {{ $facility->additionalParticipantsRequired() }} additional
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="utm-card-body" style="display:flex;flex-direction:column;gap:20px;">

                        {{-- Primary Participant --}}
                        <div style="padding:20px;background:rgba(139,0,0,.03);border-radius:var(--radius-md);border:1px solid rgba(139,0,0,.08);">
                            <div style="font-size:12px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--utm-maroon);margin-bottom:16px;">
                                Your Details — Participant 1 (Renter)
                            </div>
                            
                            @if(empty($user->phone_number))
                                <div class="utm-alert utm-alert-warning" style="margin-bottom:16px; display:flex; gap:10px;">
                                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span style="font-size:13.5px;line-height:1.5;">
                                        Please set your <strong>phone number</strong> in your <a href="{{ route('profile.edit') }}" style="font-weight:700;text-decoration:underline;color:inherit;">Profile Settings</a> before making a booking.
                                    </span>
                                </div>
                            @endif

                            <div class="participant-grid">
                                <div>
                                    <label for="primary_fullname" class="utm-label">Full Name <span style="color:var(--danger);">*</span></label>
                                    <input id="primary_fullname" type="text" name="primary_fullname"
                                           value="{{ old('primary_fullname', $user->fullname) }}" required readonly
                                           style="background:var(--slate-50);color:var(--slate-500);cursor:not-allowed;"
                                           placeholder="As per IC" class="utm-input {{ $errors->has('primary_fullname') ? 'error' : '' }}">
                                    @error('primary_fullname') <div class="utm-error-msg">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label for="primary_ic_number" class="utm-label">IC Number <span style="color:var(--danger);">*</span></label>
                                    <input id="primary_ic_number" type="text" name="primary_ic_number"
                                           value="{{ old('primary_ic_number', $user->ic_number) }}" required readonly
                                           style="background:var(--slate-50);color:var(--slate-500);cursor:not-allowed;"
                                           placeholder="e.g. 990101011234" class="utm-input {{ $errors->has('primary_ic_number') ? 'error' : '' }}">
                                    @error('primary_ic_number') <div class="utm-error-msg">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label for="primary_matric_number" class="utm-label">Matric / Staff ID</label>
                                    <input id="primary_matric_number" type="text" name="primary_matric_number"
                                           value="{{ old('primary_matric_number', $user->matric_number ?? $user->staff_id) }}" readonly
                                           style="background:var(--slate-50);color:var(--slate-500);cursor:not-allowed;"
                                           placeholder="If applicable" class="utm-input">
                                </div>
                                <div>
                                    <label for="primary_phone_number" class="utm-label">Phone Number <span style="color:var(--danger);">*</span></label>
                                    <input id="primary_phone_number" type="text" name="primary_phone_number"
                                           value="{{ old('primary_phone_number', $user->phone_number) }}" required readonly
                                           style="background:var(--slate-50);color:var(--slate-500);cursor:not-allowed;"
                                           placeholder="e.g. +60123456789" class="utm-input {{ $errors->has('primary_phone_number') ? 'error' : '' }}">
                                    @error('primary_phone_number') <div class="utm-error-msg">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Additional Participants --}}
                        @for($p = 0; $p < $facility->additionalParticipantsRequired(); $p++)
                            <div style="padding:20px;background:var(--slate-50);border-radius:var(--radius-md);border:1px solid var(--slate-200);">
                                <div style="font-size:12px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--slate-500);margin-bottom:16px;">
                                    Participant {{ $p + 2 }}
                                </div>
                                <div class="participant-grid">
                                    <div>
                                        <label class="utm-label">Full Name <span style="color:var(--danger);">*</span></label>
                                        <input type="text" name="participants[{{ $p }}][fullname]"
                                               value="{{ old("participants.$p.fullname") }}" required
                                               placeholder="As per IC"
                                               class="utm-input {{ $errors->has("participants.$p.fullname") ? 'error' : '' }}">
                                        @error("participants.$p.fullname") <div class="utm-error-msg">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="utm-label">IC Number <span style="color:var(--danger);">*</span></label>
                                        <input type="text" name="participants[{{ $p }}][ic_number]"
                                               value="{{ old("participants.$p.ic_number") }}" required
                                               placeholder="e.g. 990101011234"
                                               class="utm-input {{ $errors->has("participants.$p.ic_number") ? 'error' : '' }}">
                                        @error("participants.$p.ic_number") <div class="utm-error-msg">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="utm-label">Matric / Staff ID</label>
                                        <input type="text" name="participants[{{ $p }}][matric_number]"
                                               value="{{ old("participants.$p.matric_number") }}"
                                               placeholder="If applicable" class="utm-input">
                                    </div>
                                </div>
                            </div>
                        @endfor

                    </div>
                </div>

                {{-- Tips Card --}}
                <div class="utm-card animate-in animate-in-delay-1" style="border:1px solid rgba(201,168,76,.25);background:rgba(201,168,76,.04);">
                    <div class="utm-card-header" style="border-color:rgba(201,168,76,.15);">
                        <div class="utm-card-title" style="color:#92600A;">💡 Tips</div>
                    </div>
                    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Select <strong>consecutive slots</strong> for longer sessions — the price is charged per slot.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Equipment is charged <strong>per unit per slot</strong> on top of the facility price.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Ensure all participant <strong>IC numbers are accurate</strong> — they are used for verification at the facility.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Bookings can be cancelled from <strong>My Bookings</strong> before the session starts.</span>
                        </div>
                    </div>
                </div>

                {{-- Booking Guidelines Card --}}
                <div class="utm-card animate-in animate-in-delay-1">
                    <div class="utm-card-header">
                        <div class="utm-card-title">📋 Booking Guidelines</div>
                    </div>
                    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
                        @foreach([
                            ['num'=>'1','text'=>'Choose your preferred date and select available time slots (grey = booked).'],
                            ['num'=>'2','text'=>'Add any optional equipment you need for your session.'],
                            ['num'=>'3','text'=>'Fill in the details for all required participants.'],
                            ['num'=>'4','text'=>'Review the estimated total, then confirm your booking.'],
                        ] as $step)
                            <div style="display:flex;gap:10px;align-items:flex-start;">
                                <div style="width:22px;height:22px;border-radius:50%;background:var(--utm-maroon);color:white;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $step['num'] }}</div>
                                <span style="font-size:12.5px;color:var(--slate-600);line-height:1.5;">{{ $step['text'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- Submit Actions --}}
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;" class="animate-in animate-in-delay-2">
            <a href="{{ route('facilities.show', $facility) }}" class="btn btn-outline">Cancel</a>
            <button type="submit" id="submit-btn" class="btn btn-primary" disabled>
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Confirm Booking
            </button>
        </div>
    </form>

    {{-- Responsive styles --}}
    <style>
        .booking-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
        }
        /* Participant fields: 3-col on wide, 1-col on narrow */
        .participant-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }
        @media (max-width: 1024px) {
            .booking-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 640px) {
            .participant-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 480px) {
            .participant-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
    const facilityPrice = {{ (float) $facility->price }};

    // Slot toggle
    document.querySelectorAll('.slot-label').forEach(label => {
        const cb   = label.querySelector('.slot-checkbox');
        const card = label.querySelector('.slot-card');
        if (cb.disabled) return;
        label.addEventListener('click', e => {
            e.preventDefault();
            cb.checked = !cb.checked;
            card.className = 'slot-card ' + (cb.checked ? 'selected' : 'available');
            updateSummary();
        });
    });

    // Equipment toggle
    document.querySelectorAll('.equipment-toggle').forEach(cb => {
        const idx = cb.dataset.eqIndex;
        cb.addEventListener('change', () => {
            const qtyInput = document.querySelector(`.equipment-qty[data-eq-index="${idx}"]`);
            const idInput  = document.querySelector(`.eq-id-input[data-eq-index="${idx}"]`);
            if (qtyInput) qtyInput.disabled = !cb.checked;
            if (idInput)  idInput.disabled  = !cb.checked;
            updateSummary();
        });
    });

    document.querySelectorAll('.equipment-qty').forEach(i => i.addEventListener('input', updateSummary));

    function getSlotCount() { return document.querySelectorAll('.slot-checkbox:checked').length; }

    function getEqTotal() {
        let t = 0;
        document.querySelectorAll('.equipment-toggle:checked').forEach(cb => {
            const idx = cb.dataset.eqIndex;
            const qty = document.querySelector(`.equipment-qty[data-eq-index="${idx}"]`);
            t += parseFloat(qty.dataset.price) * parseInt(qty.value || 1);
        });
        return t;
    }

    function updateSummary() {
        const slots = getSlotCount();
        const eq    = getEqTotal();
        const total = slots * (facilityPrice + eq);

        document.getElementById('slot-count').textContent        = slots;
        document.getElementById('facility-subtotal').textContent = 'RM ' + (slots * facilityPrice).toFixed(2);
        document.getElementById('grand-total').textContent       = 'RM ' + total.toFixed(2);

        const eqRow = document.getElementById('equipment-row');
        if (eq > 0) {
            eqRow.style.display = 'flex';
            document.getElementById('equipment-subtotal').textContent = 'RM ' + (slots * eq).toFixed(2);
        } else {
            eqRow.style.display = 'none';
        }

        const phoneMissing = {{ empty($user->phone_number) ? 'true' : 'false' }};
        document.getElementById('submit-btn').disabled = slots === 0 || phoneMissing;
    }

    document.getElementById('booking-date').addEventListener('change', function () {
        const url = new URL(window.location.href);
        url.searchParams.set('date', this.value);
        window.location.href = url.toString();
    });

    updateSummary();
    </script>

</x-app-layout>