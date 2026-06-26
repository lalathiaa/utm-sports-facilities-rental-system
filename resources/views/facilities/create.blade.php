<x-app-layout>
    <x-slot name="header">Add Facility</x-slot>

    <a href="{{ route('facilities.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;margin-bottom:24px;font-weight:500;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Facilities
    </a>

    <form method="POST" action="{{ route('facilities.store') }}" enctype="multipart/form-data" style="max-width:1100px;margin:0 auto;">

        <style>
            .facility-form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
                align-items: start;
            }
            @media (max-width: 1024px) {
                .facility-form-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        @csrf

        <div class="facility-form-grid">

            {{-- ── LEFT: Facility Information ── --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                <div class="utm-card animate-in">
                    <div class="utm-card-header" style="background:rgba(139,0,0,.04);border-color:rgba(139,0,0,.10);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:var(--utm-maroon);color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                            <div>
                                <div class="utm-card-title">Facility Information</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Basic details about the facility</div>
                            </div>
                        </div>
                    </div>
                    <div class="utm-card-body">

                        <div class="utm-form-group">
                            <label for="name" class="utm-label">Facility Name <span style="color:var(--danger);">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                   placeholder="e.g. Badminton Court A"
                                   class="utm-input {{ $errors->has('name') ? 'error' : '' }}">
                            @error('name') <div class="utm-error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div class="utm-form-group">
                                <label for="price" class="utm-label">Price per Slot (RM) <span style="color:var(--danger);">*</span></label>
                                <div style="position:relative;">
                                    <span style="position:absolute;inset-y:0;left:0;padding-left:13px;display:flex;align-items:center;font-size:14px;color:var(--slate-400);pointer-events:none;">RM</span>
                                    <input id="price" type="number" name="price" value="{{ old('price','0.00') }}"
                                           step="0.01" min="0" required
                                           style="padding-left:36px;"
                                           class="utm-input {{ $errors->has('price') ? 'error' : '' }}">
                                </div>
                                @error('price') <div class="utm-error-msg">{{ $message }}</div> @enderror
                            </div>

                            <div class="utm-form-group">
                                <label for="required_participants" class="utm-label">Required Participants <span style="color:var(--danger);">*</span></label>
                                <input id="required_participants" type="number" name="required_participants"
                                       value="{{ old('required_participants',1) }}" min="1" max="50" required
                                       class="utm-input {{ $errors->has('required_participants') ? 'error' : '' }}">
                                <div class="utm-helper-text">Total people (including renter).</div>
                                @error('required_participants') <div class="utm-error-msg">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="utm-form-group">
                            <label for="status" class="utm-label">Availability Status <span style="color:var(--danger);">*</span></label>
                            <select id="status" name="status" class="utm-select">
                                <option value="available" {{ old('status','available') === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="not_available" {{ old('status') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                            </select>
                            @error('status') <div class="utm-error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div class="utm-form-group" style="margin-bottom:0;">
                            <label for="image" class="utm-label">Facility Image (optional)</label>
                            <input id="image" type="file" name="image" accept="image/*"
                                   class="utm-input" style="padding:8px 14px;cursor:pointer;">
                            <div class="utm-helper-text">JPG, PNG, GIF up to 2MB.</div>
                            @error('image') <div class="utm-error-msg">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

                {{-- Getting Started Guide --}}
                <div class="utm-card animate-in animate-in-delay-1">
                    <div class="utm-card-header">
                        <div class="utm-card-title">Getting Started</div>
                    </div>
                    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
                        @foreach([
                            ['num'=>'1','text'=>'Enter the facility name and set a price per booking slot (1 hour).'],
                            ['num'=>'2','text'=>'Set the number of required participants including the renter.'],
                            ['num'=>'3','text'=>'Upload a clear image so users can identify the facility easily.'],
                            ['num'=>'4','text'=>'Optionally add equipment that users can rent alongside the facility.'],
                        ] as $step)
                            <div style="display:flex;gap:10px;align-items:flex-start;">
                                <div style="width:22px;height:22px;border-radius:50%;background:var(--utm-maroon);color:white;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $step['num'] }}</div>
                                <span style="font-size:12.5px;color:var(--slate-600);line-height:1.5;">{{ $step['text'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- ── RIGHT: Equipment ── --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                <div class="utm-card animate-in">
                    <div class="utm-card-header" style="background:rgba(5,150,105,.04);border-color:rgba(5,150,105,.12);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:#059669;color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">2</div>
                            <div>
                                <div class="utm-card-title">Equipment</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Add equipment available for this facility</div>
                            </div>
                        </div>
                        <button type="button" id="add-equipment-btn" class="btn btn-outline btn-sm">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add
                        </button>
                    </div>

                    <div id="equipment-container">
                        @if(old('equipment'))
                            @foreach(old('equipment') as $i => $eq)
                                <div class="equipment-row" data-index="{{ $i }}" style="padding:20px 24px;border-top:1px solid var(--slate-100);position:relative;">
                                    @include('facilities._equipment_row', ['i' => $i, 'eq' => $eq])
                                </div>
                            @endforeach
                        @else
                            <div id="no-equipment-msg" style="padding:40px;text-align:center;color:var(--slate-300);font-size:13.5px;">
                                <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="var(--slate-200)" stroke-width="1.5" style="margin:0 auto 10px;display:block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                                No equipment added yet.<br>
                                <span style="font-size:12.5px;">Click "+ Add" above to get started.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tips --}}
                <div class="utm-card animate-in animate-in-delay-1" style="border:1px solid rgba(201,168,76,.25);background:rgba(201,168,76,.04);">
                    <div class="utm-card-header" style="border-color:rgba(201,168,76,.15);">
                        <div class="utm-card-title" style="color:#92600A;">💡 Tips</div>
                    </div>
                    <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Equipment prices are charged <strong>per unit per slot</strong> on top of facility price.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>You can manage <strong>closure dates</strong> after creating the facility.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Set status to <strong>Not Available</strong> to temporarily hide from bookings.</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;" class="animate-in animate-in-delay-2">
            <a href="{{ route('facilities.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Facility</button>
        </div>

    </form>

    <template id="equipment-row-template">
        <div class="equipment-row" data-index="__INDEX__"
             style="padding:20px 24px;border-top:1px solid var(--slate-100);position:relative;">
            <button type="button" class="remove-equipment-btn"
                    style="position:absolute;top:16px;right:16px;padding:4px;background:rgba(220,38,38,.08);border:none;border-radius:6px;cursor:pointer;color:var(--danger);" title="Remove">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div style="display:flex;flex-direction:column;gap:12px;padding-right:32px;">
                <div>
                    <label class="utm-label">Equipment Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="equipment[__INDEX__][name]" required placeholder="e.g. Racket" class="utm-input">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="utm-label">Price / Slot (RM) <span style="color:var(--danger);">*</span></label>
                        <div style="position:relative;">
                            <span style="position:absolute;inset-y:0;left:0;padding-left:13px;display:flex;align-items:center;font-size:14px;color:var(--slate-400);pointer-events:none;">RM</span>
                            <input type="number" name="equipment[__INDEX__][price]" required step="0.01" min="0" value="0.00"
                                   style="padding-left:36px;" class="utm-input">
                        </div>
                    </div>
                    <div>
                        <label class="utm-label">Quantity <span style="color:var(--danger);">*</span></label>
                        <input type="number" name="equipment[__INDEX__][quantity]" required min="1" value="1" class="utm-input">
                    </div>
                </div>
                <div>
                    <label class="utm-label">Status <span style="color:var(--danger);">*</span></label>
                    <select name="equipment[__INDEX__][status]" required class="utm-select">
                        <option value="available">Available</option>
                        <option value="not_available">Not Available</option>
                    </select>
                </div>
                <div>
                    <label class="utm-label">Image (optional)</label>
                    <input type="file" name="equipment[__INDEX__][image]" accept="image/*"
                           class="utm-input" style="padding:8px 14px;cursor:pointer;">
                </div>
            </div>
        </div>
    </template>

    <script>
        let eqIndex = {{ old('equipment') ? count(old('equipment')) : 0 }};
        const container = document.getElementById('equipment-container');
        const addBtn    = document.getElementById('add-equipment-btn');
        const template  = document.getElementById('equipment-row-template');

        function updateNoMsg() {
            const noMsg = document.getElementById('no-equipment-msg');
            if (noMsg) noMsg.style.display = container.querySelectorAll('.equipment-row').length === 0 ? 'block' : 'none';
        }

        function attachRemove(row) {
            const btn = row.querySelector('.remove-equipment-btn');
            if (btn) btn.addEventListener('click', () => { row.remove(); updateNoMsg(); });
        }

        addBtn.addEventListener('click', () => {
            const html = template.innerHTML.replaceAll('__INDEX__', eqIndex++);
            const div  = document.createElement('div');
            div.innerHTML = html;
            const row = div.firstElementChild;
            container.appendChild(row);
            attachRemove(row);
            updateNoMsg();
        });

        document.querySelectorAll('.equipment-row').forEach(attachRemove);
        updateNoMsg();
    </script>
</x-app-layout>