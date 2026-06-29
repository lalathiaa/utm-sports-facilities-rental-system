<x-app-layout>
    <x-slot name="header">Leave Feedback</x-slot>

    <div style="max-width:1100px;margin:0 auto;padding:0 16px;">

        <a href="{{ route('bookings.my') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;margin-bottom:24px;font-weight:500;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to My Bookings
        </a>

        <style>
            .feedback-form-grid {
                display: grid;
                grid-template-columns: 1fr 340px;
                gap: 24px;
                align-items: start;
            }
            @media (max-width: 1024px) {
                .feedback-form-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <div class="feedback-form-grid">
            {{-- Left column: Form --}}
            <div style="display:flex;flex-direction:column;gap:20px;">
                <div class="utm-card animate-in">
                    <div class="utm-card-header" style="background:rgba(139,0,0,.04);border-color:rgba(139,0,0,.10);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:var(--utm-maroon);color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                            <div>
                                <div class="utm-card-title">Leave Feedback</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Rate your experience and share your thoughts</div>
                            </div>
                        </div>
                    </div>
                    <div class="utm-card-body">
                        @if($errors->any())
                            <div class="utm-alert utm-alert-error" style="margin-bottom:20px;">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <ul style="margin:0;padding-left:16px;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('feedback.store', $booking->booking_group_id ?? $booking->id) }}">
                            @csrf

                            {{-- Star Rating --}}
                            <div class="utm-form-group">
                                <label class="utm-label">Rating <span style="color:var(--danger);">*</span></label>
                                <div id="star-rating" style="display:flex;gap:8px;margin-bottom:6px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                                data-value="{{ $i }}"
                                                onclick="setRating({{ $i }})"
                                                style="background:none;border:none;cursor:pointer;padding:4px;font-size:32px;line-height:1;color:var(--slate-300);transition:color .15s,transform .1s;"
                                                onmouseover="hoverRating({{ $i }})"
                                                onmouseout="resetHover()">★</button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', 0) }}">
                                <div id="rating-label" style="font-size:12.5px;color:var(--slate-400);min-height:18px;"></div>
                                @error('rating')
                                    <div class="utm-error-msg">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Title --}}
                            <div class="utm-form-group">
                                <label class="utm-label" for="title">Feedback Title <span style="color:var(--danger);">*</span></label>
                                <input type="text" id="title" name="title" class="utm-input {{ $errors->has('title') ? 'error' : '' }}"
                                       value="{{ old('title') }}" maxlength="255"
                                       placeholder="Summarise your experience in a few words…">
                                @error('title')
                                    <div class="utm-error-msg">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Message --}}
                            <div class="utm-form-group">
                                <label class="utm-label" for="message">Feedback Message <span style="color:var(--danger);">*</span></label>
                                <textarea id="message" name="message" rows="5"
                                          class="utm-input {{ $errors->has('message') ? 'error' : '' }}"
                                          maxlength="2000" style="resize:vertical;"
                                          placeholder="Tell us more about your experience…">{{ old('message') }}</textarea>
                                <div class="utm-helper-text">Maximum 2,000 characters.</div>
                                @error('message')
                                    <div class="utm-error-msg">{{ $message }}</div>
                                @enderror
                            </div>

                            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;">
                                <a href="{{ route('bookings.my') }}" class="btn btn-outline">Cancel</a>
                                <button type="submit" class="btn btn-primary">Submit Feedback</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right column: Booking Summary + Tips --}}
            <div style="display:flex;flex-direction:column;gap:20px;">
                <div class="utm-card animate-in animate-in-delay-1">
                    <div class="utm-card-header" style="background:rgba(5,150,105,.04);border-color:rgba(5,150,105,.12);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:#059669;color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">2</div>
                            <div>
                                <div class="utm-card-title">Booking Summary</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Details of your reservation</div>
                            </div>
                        </div>
                    </div>
                    <div class="utm-card-body" style="display:flex;flex-direction:column;gap:14px;">
                        <div>
                            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:4px;">Facility</div>
                            <div style="font-size:14px;font-weight:600;color:var(--slate-800);">{{ $booking->facility->name }}</div>
                        </div>
                        <div>
                            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:4px;">Date</div>
                            <div style="font-size:14px;color:var(--slate-700);">{{ $booking->booking_date->format('d M Y') }}</div>
                        </div>
                        <div>
                            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--slate-400);margin-bottom:4px;">Slots</div>
                            <div style="font-size:14px;color:var(--slate-700);word-break:break-all;">
                                @foreach($groupBookings as $gb)
                                    {{ $gb->slotLabel() }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tips Card --}}
                <div class="utm-card animate-in animate-in-delay-1" style="border:1px solid rgba(201,168,76,.25);background:rgba(201,168,76,.04);">
                    <div class="utm-card-header" style="border-color:rgba(201,168,76,.15);">
                        <div class="utm-card-title" style="color:#92600A;">💡 Tips & Guidelines</div>
                    </div>
                    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Your feedback helps the management improve the facilities and booking experience.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Ratings and reviews are public, helping other UTM community members choose venues.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>For urgent facilities issues (e.g., cleanliness, missing equipment), please report to the office immediately.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const ratingLabels = {
        0: '',
        1: 'Poor',
        2: 'Fair',
        3: 'Good',
        4: 'Very Good',
        5: 'Excellent'
    };
    let currentRating = {{ old('rating', 0) }};

    function renderStars(value, isHover = false) {
        const stars = document.querySelectorAll('#star-rating button');
        stars.forEach((btn, idx) => {
            if (idx < value) {
                btn.style.color = isHover ? '#C9A84C' : 'var(--utm-gold)';
                btn.style.transform = 'scale(1.1)';
            } else {
                btn.style.color = 'var(--slate-300)';
                btn.style.transform = 'scale(1)';
            }
        });
        document.getElementById('rating-label').textContent = ratingLabels[value] || '';
    }

    function setRating(val) {
        currentRating = val;
        document.getElementById('rating-input').value = val;
        renderStars(val);
    }

    function hoverRating(val) {
        renderStars(val, true);
    }

    function resetHover() {
        renderStars(currentRating);
    }

    // Init on load
    renderStars(currentRating);
    </script>
</x-app-layout>
