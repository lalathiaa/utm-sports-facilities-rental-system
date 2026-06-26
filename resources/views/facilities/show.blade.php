<x-app-layout>
    <x-slot name="header">Facility Details</x-slot>

    {{-- Back --}}
    <a href="{{ route('facilities.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;margin-bottom:20px;font-weight:500;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Facilities
    </a>

    <div style="max-width:1100px;margin:0 auto;">
        <div style="display:grid;grid-template-columns:2fr 1.1fr;gap:24px;align-items:start;">

        {{-- ── LEFT: Main Content ── --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Hero Image --}}
            @if($facility->image)
                <div style="border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-card);border:1px solid var(--slate-200);height:300px;">
                    <img src="{{ Storage::url($facility->image) }}" alt="{{ $facility->name }}"
                         style="width:100%;height:100%;object-fit:cover;">
                </div>
            @endif

            {{-- Facility Info Card --}}
            <div class="utm-card animate-in">
                <div style="padding:28px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:20px;">
                        <div>
                            <h1 style="font-family:'Lora',Georgia,serif;font-size:26px;font-weight:600;color:var(--slate-800);margin:0 0 10px;">
                                {{ $facility->name }}
                            </h1>
                            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                                <span style="font-size:24px;font-weight:800;color:var(--utm-maroon);">
                                    RM {{ number_format($facility->price, 2) }}
                                    <span style="font-size:14px;font-weight:400;color:var(--slate-400);">/ slot</span>
                                </span>
                                <span class="badge {{ $facility->status === 'available' ? 'badge-available' : 'badge-unavailable' }}">
                                    {{ $facility->status === 'available' ? '● Available' : '● Unavailable' }}
                                </span>
                            </div>
                        </div>
                        @auth
                            @if(Auth::user()->isRentalOfficer())
                                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                                    <a href="{{ route('facilities.closures.index', $facility) }}" class="btn btn-outline btn-sm"
                                       style="background:rgba(217,119,6,.08);color:#92600A;border-color:rgba(217,119,6,.20);">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        Manage Closures
                                    </a>
                                    <a href="{{ route('facilities.edit', $facility) }}" class="btn btn-outline btn-sm">Edit Facility</a>
                                </div>
                            @endif
                        @endauth
                    </div>

                    {{-- Stats Row --}}
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:20px;">
                        <div style="padding:14px 16px;background:var(--slate-50);border-radius:var(--radius-md);border:1px solid var(--slate-200);">
                            <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--slate-400);margin-bottom:5px;">Participants</div>
                            <div style="font-size:18px;font-weight:800;color:var(--slate-800);">{{ $facility->required_participants }}</div>
                            <div style="font-size:11.5px;color:var(--slate-400);margin-top:2px;">required to book</div>
                        </div>
                        <div style="padding:14px 16px;background:var(--slate-50);border-radius:var(--radius-md);border:1px solid var(--slate-200);">
                            <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--slate-400);margin-bottom:5px;">Equipment</div>
                            <div style="font-size:18px;font-weight:800;color:var(--slate-800);">{{ $facility->equipment->count() }}</div>
                            <div style="font-size:11.5px;color:var(--slate-400);margin-top:2px;">item(s) available</div>
                        </div>
                        @if(!Auth::check() || !Auth::user()->isAdmin())
                            @php $avgRating = $facility->averageRating(); $fbCount = $facility->feedbackCount(); @endphp
                            <div style="padding:14px 16px;background:var(--slate-50);border-radius:var(--radius-md);border:1px solid var(--slate-200);">
                                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--slate-400);margin-bottom:5px;">Rating</div>
                                <div style="display:flex;align-items:center;gap:5px;">
                                    <span style="font-size:18px;font-weight:800;color:var(--slate-800);">{{ $avgRating > 0 ? number_format($avgRating, 1) : '—' }}</span>
                                    @if($avgRating > 0)
                                        <span style="font-size:14px;color:var(--utm-gold);">★</span>
                                    @endif
                                </div>
                                <div style="font-size:11.5px;color:var(--slate-400);margin-top:2px;">{{ $fbCount }} review{{ $fbCount !== 1 ? 's' : '' }}</div>
                            </div>
                        @endif
                        <div style="padding:14px 16px;background:var(--slate-50);border-radius:var(--radius-md);border:1px solid var(--slate-200);">
                            <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--slate-400);margin-bottom:5px;">Price</div>
                            <div style="font-size:18px;font-weight:800;color:var(--utm-maroon);">RM {{ number_format($facility->price, 2) }}</div>
                            <div style="font-size:11.5px;color:var(--slate-400);margin-top:2px;">per slot (1 hour)</div>
                        </div>
                    </div>

                    @if(!Auth::check() || !Auth::user()->isAdmin())
                        {{-- Star Rating Display --}}
                        <div style="display:flex;align-items:center;gap:8px;padding:14px 16px;background:rgba(201,168,76,.05);border-radius:var(--radius-md);border:1px solid rgba(201,168,76,.15);">
                            <div style="display:flex;gap:3px;">
                                @for($i = 1; $i <= 5; $i++)
                                    <span style="font-size:20px;color:{{ $i <= round($avgRating) ? 'var(--utm-gold)' : 'var(--slate-200)' }};">★</span>
                                @endfor
                            </div>
                            <span style="font-size:15px;color:var(--slate-600);font-weight:700;">{{ $avgRating > 0 ? number_format($avgRating, 1) : '—' }}</span>
                            <span style="font-size:13px;color:var(--slate-400);">({{ $fbCount }} review{{ $fbCount !== 1 ? 's' : '' }})</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Equipment Card --}}
            <div class="utm-card animate-in animate-in-delay-1">
                <div class="utm-card-header">
                    <span class="utm-card-title">Equipment Available</span>
                    <span style="font-size:13px;color:var(--slate-400);">{{ $facility->equipment->count() }} item(s)</span>
                </div>
                @if($facility->equipment->isEmpty())
                    <div style="padding:40px;text-align:center;color:var(--slate-300);font-size:14px;">
                        No equipment listed for this facility.
                    </div>
                @else
                    <div>
                        @foreach($facility->equipment as $eq)
                            <div style="display:flex;align-items:center;gap:16px;padding:16px 24px;border-bottom:1px solid var(--slate-100);">
                                <div style="width:52px;height:52px;border-radius:10px;overflow:hidden;background:var(--slate-100);flex-shrink:0;">
                                    @if($eq->image)
                                        <img src="{{ Storage::url($eq->image) }}" alt="{{ $eq->name }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <div style="display:flex;align-items:center;justify-content:center;height:100%;">
                                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div style="flex:1;">
                                    <div style="font-weight:600;font-size:14px;color:var(--slate-800);">{{ $eq->name }}</div>
                                    <div style="display:flex;gap:12px;font-size:12.5px;color:var(--slate-400);margin-top:3px;">
                                        <span>RM {{ number_format($eq->price, 2) }} / slot</span>
                                        <span>·</span>
                                        <span>{{ $eq->quantity }} unit(s) in stock</span>
                                    </div>
                                </div>
                                <span class="badge {{ $eq->status === 'available' ? 'badge-available' : 'badge-unavailable' }}">
                                    {{ $eq->status === 'available' ? 'Available' : 'Unavailable' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- ── RIGHT: Sticky Sidebar ── --}}
        <div style="position:sticky;top:80px;display:flex;flex-direction:column;gap:16px;">

            {{-- Book / Action CTA --}}
            @auth
                @if(!Auth::user()->isRentalOfficer() && !Auth::user()->isAdmin())
                    <div class="utm-card animate-in" style="overflow:hidden;">
                        <div style="background:linear-gradient(135deg,var(--utm-maroon-dark),var(--utm-maroon));padding:20px 24px;">
                            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:rgba(201,168,76,.85);margin-bottom:4px;">Ready to Play?</div>
                            <div style="font-size:20px;font-weight:800;color:white;">RM {{ number_format($facility->price, 2) }}<span style="font-size:13px;font-weight:400;color:rgba(255,255,255,.6);"> / slot</span></div>
                        </div>
                        <div style="padding:20px 24px;">
                            @if($facility->isAvailable())
                                <a href="{{ route('bookings.create', $facility) }}" class="btn btn-primary"
                                   style="width:100%;justify-content:center;margin-bottom:12px;">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Book This Facility
                                </a>
                                <div style="font-size:12px;color:var(--slate-400);text-align:center;">
                                    {{ $facility->required_participants }} participant(s) required · Select slots on next page
                                </div>
                            @else
                                <div style="padding:12px 16px;background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.15);border-radius:var(--radius-md);text-align:center;">
                                    <div style="font-size:13.5px;font-weight:600;color:var(--danger);margin-bottom:4px;">Currently Unavailable</div>
                                    <div style="font-size:12px;color:var(--slate-400);">This facility is not accepting bookings at the moment.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endauth

            {{-- Booking Info --}}
            <div class="utm-card animate-in animate-in-delay-1">
                <div class="utm-card-header">
                    <div class="utm-card-title">Booking Info</div>
                </div>
                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
                        <span style="color:var(--slate-500);">Price per slot</span>
                        <span style="font-weight:700;color:var(--slate-800);">RM {{ number_format($facility->price, 2) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
                        <span style="color:var(--slate-500);">Slot duration</span>
                        <span style="font-weight:700;color:var(--slate-800);">1 hour</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
                        <span style="color:var(--slate-500);">Participants needed</span>
                        <span style="font-weight:700;color:var(--slate-800);">{{ $facility->required_participants }} person(s)</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
                        <span style="color:var(--slate-500);">Equipment available</span>
                        <span style="font-weight:700;color:var(--slate-800);">{{ $facility->equipment->count() }} item(s)</span>
                    </div>
                    <div style="height:1px;background:var(--slate-100);"></div>
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:13px;">
                        <span style="color:var(--slate-500);">Status</span>
                        <span class="badge {{ $facility->status === 'available' ? 'badge-available' : 'badge-unavailable' }}">
                            {{ $facility->status === 'available' ? '● Available' : '● Unavailable' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Officer Quick Links --}}
            @auth
                @if(Auth::user()->isRentalOfficer())
                    <div class="utm-card animate-in animate-in-delay-2">
                        <div class="utm-card-header">
                            <div class="utm-card-title">Manage</div>
                        </div>
                        <div style="padding:12px 16px;display:flex;flex-direction:column;gap:6px;">
                            <a href="{{ route('facilities.edit', $facility) }}"
                               style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:var(--radius-md);background:var(--slate-50);border:1px solid var(--slate-200);text-decoration:none;font-size:13px;font-weight:600;color:var(--slate-700);transition:all .15s;"
                               onmouseover="this.style.borderColor='var(--utm-maroon)';this.style.color='var(--utm-maroon)'"
                               onmouseout="this.style.borderColor='var(--slate-200)';this.style.color='var(--slate-700)'">
                                <span style="display:flex;align-items:center;gap:8px;">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit Facility
                                </span>
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <a href="{{ route('facilities.closures.index', $facility) }}"
                               style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:var(--radius-md);background:var(--slate-50);border:1px solid var(--slate-200);text-decoration:none;font-size:13px;font-weight:600;color:var(--slate-700);transition:all .15s;"
                               onmouseover="this.style.borderColor='var(--utm-maroon)';this.style.color='var(--utm-maroon)'"
                               onmouseout="this.style.borderColor='var(--slate-200)';this.style.color='var(--slate-700)'">
                                <span style="display:flex;align-items:center;gap:8px;">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    Manage Closures
                                </span>
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <a href="{{ route('facilities.index') }}"
                               style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:var(--radius-md);background:var(--slate-50);border:1px solid var(--slate-200);text-decoration:none;font-size:13px;font-weight:600;color:var(--slate-700);transition:all .15s;"
                               onmouseover="this.style.borderColor='var(--utm-maroon)';this.style.color='var(--utm-maroon)'"
                               onmouseout="this.style.borderColor='var(--slate-200)';this.style.color='var(--slate-700)'">
                                <span style="display:flex;align-items:center;gap:8px;">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    All Facilities
                                </span>
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif
            @endauth

        </div>
    </div>
</div>

</x-app-layout>