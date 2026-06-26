<x-app-layout>
    <x-slot name="header">Edit Facility</x-slot>

    <a href="{{ route('facilities.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--slate-400);text-decoration:none;margin-bottom:24px;font-weight:500;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Facilities
    </a>

    <form method="POST" action="{{ route('facilities.update', $facility) }}" enctype="multipart/form-data" style="max-width:1100px;margin:0 auto;">

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
        @csrf @method('PUT')

        <div class="facility-form-grid">

            {{-- ── LEFT: Facility Information ── --}}
            <div style="display:flex;flex-direction:column;gap:20px;">

                <div class="utm-card animate-in">
                    <div class="utm-card-header" style="background:rgba(139,0,0,.04);border-color:rgba(139,0,0,.10);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:var(--utm-maroon);color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                            <div>
                                <div class="utm-card-title">Facility Information</div>
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Update details about this facility</div>
                            </div>
                        </div>
                    </div>
                    <div class="utm-card-body">

                        <div class="utm-form-group">
                            <label for="name" class="utm-label">Facility Name <span style="color:var(--danger);">*</span></label>
                            <input id="name" type="text" name="name"
                                   value="{{ old('name', $facility->name) }}" required
                                   class="utm-input {{ $errors->has('name') ? 'error' : '' }}">
                            @error('name') <div class="utm-error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div class="utm-form-group">
                                <label for="price" class="utm-label">Price per Slot (RM) <span style="color:var(--danger);">*</span></label>
                                <div style="position:relative;">
                                    <span style="position:absolute;inset-y:0;left:0;padding-left:13px;display:flex;align-items:center;font-size:14px;color:var(--slate-400);pointer-events:none;">RM</span>
                                    <input id="price" type="number" name="price"
                                           value="{{ old('price', $facility->price) }}"
                                           step="0.01" min="0" required
                                           style="padding-left:36px;"
                                           class="utm-input {{ $errors->has('price') ? 'error' : '' }}">
                                </div>
                                @error('price') <div class="utm-error-msg">{{ $message }}</div> @enderror
                            </div>

                            <div class="utm-form-group">
                                <label for="required_participants" class="utm-label">Required Participants <span style="color:var(--danger);">*</span></label>
                                <input id="required_participants" type="number" name="required_participants"
                                       value="{{ old('required_participants', $facility->required_participants) }}"
                                       min="1" max="50" required
                                       class="utm-input {{ $errors->has('required_participants') ? 'error' : '' }}">
                                <div class="utm-helper-text">Total people (including renter).</div>
                                @error('required_participants') <div class="utm-error-msg">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="utm-form-group">
                            <label for="status" class="utm-label">Availability Status <span style="color:var(--danger);">*</span></label>
                            <select id="status" name="status" class="utm-select">
                                <option value="available" {{ old('status', $facility->status) === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="not_available" {{ old('status', $facility->status) === 'not_available' ? 'selected' : '' }}>Not Available</option>
                            </select>
                            @error('status') <div class="utm-error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div class="utm-form-group" style="margin-bottom:0;">
                            <label for="image" class="utm-label">Facility Image</label>
                            @if($facility->image)
                                <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;padding:12px;background:var(--slate-50);border-radius:var(--radius-md);border:1px solid var(--slate-200);">
                                    <img src="{{ Storage::url($facility->image) }}" alt="Current image"
                                         style="height:56px;width:56px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                                    <div>
                                        <div style="font-size:12.5px;font-weight:600;color:var(--slate-600);">Current image</div>
                                        <div style="font-size:12px;color:var(--slate-400);">Upload a new one to replace it</div>
                                    </div>
                                </div>
                            @endif
                            <input id="image" type="file" name="image" accept="image/*"
                                   class="utm-input" style="padding:8px 14px;cursor:pointer;">
                            <div class="utm-helper-text">JPG, PNG, GIF up to 2MB. Leave blank to keep current image.</div>
                            @error('image') <div class="utm-error-msg">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

                {{-- Facility Preview --}}
                <div class="utm-card animate-in animate-in-delay-1">
                    <div class="utm-card-header">
                        <div class="utm-card-title">Preview</div>
                    </div>
                    <div class="utm-card-body" style="padding:20px;">
                        @if($facility->image)
                            <img src="{{ Storage::url($facility->image) }}" alt="{{ $facility->name }}"
                                 style="width:100%;height:160px;object-fit:cover;border-radius:var(--radius-md);margin-bottom:14px;">
                        @else
                            <div style="width:100%;height:160px;background:var(--slate-100);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                                <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/>
                                </svg>
                            </div>
                        @endif
                        <div style="font-weight:700;font-size:15px;color:var(--slate-800);margin-bottom:6px;">{{ $facility->name }}</div>
                        <div style="font-size:13px;color:var(--utm-maroon);font-weight:700;margin-bottom:10px;">
                            RM {{ number_format($facility->price, 2) }} / slot
                        </div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
                            <span class="badge {{ $facility->status === 'available' ? 'badge-available' : 'badge-unavailable' }}">
                                {{ $facility->status === 'available' ? '● Available' : '● Unavailable' }}
                            </span>
                            <span style="font-size:12px;color:var(--slate-400);display:flex;align-items:center;">
                                👥 {{ $facility->required_participants }} participant(s)
                            </span>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            <a href="{{ route('facilities.show', $facility) }}"
                               style="display:flex;align-items:center;justify-content:space-between;padding:9px 12px;border-radius:var(--radius-md);background:var(--slate-50);border:1px solid var(--slate-200);text-decoration:none;font-size:12.5px;font-weight:600;color:var(--slate-700);"
                               onmouseover="this.style.borderColor='var(--utm-maroon)';this.style.color='var(--utm-maroon)'"
                               onmouseout="this.style.borderColor='var(--slate-200)';this.style.color='var(--slate-700)'">
                                <span>View Facility Page</span>
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ route('facilities.closures.index', $facility) }}"
                               style="display:flex;align-items:center;justify-content:space-between;padding:9px 12px;border-radius:var(--radius-md);background:var(--slate-50);border:1px solid var(--slate-200);text-decoration:none;font-size:12.5px;font-weight:600;color:var(--slate-700);"
                               onmouseover="this.style.borderColor='var(--utm-maroon)';this.style.color='var(--utm-maroon)'"
                               onmouseout="this.style.borderColor='var(--slate-200)';this.style.color='var(--slate-700)'">
                                <span>Manage Closures</span>
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
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
                                <div style="font-size:12px;color:var(--slate-400);margin-top:2px;">Manage equipment for this facility</div>
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
                        @forelse($facility->equipment as $i => $eq)
                            <div class="equipment-row" data-index="{{ $i }}"
                                 style="padding:20px 24px;border-top:1px solid var(--slate-100);position:relative;">
                                <input type="hidden" name="equipment[{{ $i }}][id]" value="{{ $eq->id }}">

                                <button type="button" class="remove-equipment-btn"
                                        style="position:absolute;top:16px;right:16px;padding:4px;background:rgba(220,38,38,.08);border:none;border-radius:6px;cursor:pointer;color:var(--danger);" title="Remove">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>

                                <div style="display:flex;flex-direction:column;gap:12px;padding-right:32px;">
                                    <div>
                                        <label class="utm-label">Equipment Name <span style="color:var(--danger);">*</span></label>
                                        <input type="text" name="equipment[{{ $i }}][name]" required
                                               value="{{ old("equipment.$i.name", $eq->name) }}"
                                               class="utm-input {{ $errors->has("equipment.$i.name") ? 'error' : '' }}">
                                        @error("equipment.$i.name") <div class="utm-error-msg">{{ $message }}</div> @enderror
                                    </div>
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                        <div>
                                            <label class="utm-label">Price / Slot (RM) <span style="color:var(--danger);">*</span></label>
                                            <div style="position:relative;">
                                                <span style="position:absolute;inset-y:0;left:0;padding-left:13px;display:flex;align-items:center;font-size:14px;color:var(--slate-400);pointer-events:none;">RM</span>
                                                <input type="number" name="equipment[{{ $i }}][price]" required step="0.01" min="0"
                                                       value="{{ old("equipment.$i.price", $eq->price) }}"
                                                       style="padding-left:36px;"
                                                       class="utm-input {{ $errors->has("equipment.$i.price") ? 'error' : '' }}">
                                            </div>
                                            @error("equipment.$i.price") <div class="utm-error-msg">{{ $message }}</div> @enderror
                                        </div>
                                        <div>
                                            <label class="utm-label">Quantity <span style="color:var(--danger);">*</span></label>
                                            <input type="number" name="equipment[{{ $i }}][quantity]" required min="1"
                                                   value="{{ old("equipment.$i.quantity", $eq->quantity) }}"
                                                   class="utm-input {{ $errors->has("equipment.$i.quantity") ? 'error' : '' }}">
                                            @error("equipment.$i.quantity") <div class="utm-error-msg">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="utm-label">Status <span style="color:var(--danger);">*</span></label>
                                        <select name="equipment[{{ $i }}][status]" required class="utm-select">
                                            <option value="available" {{ old("equipment.$i.status", $eq->status) === 'available' ? 'selected' : '' }}>Available</option>
                                            <option value="not_available" {{ old("equipment.$i.status", $eq->status) === 'not_available' ? 'selected' : '' }}>Not Available</option>
                                        </select>
                                        @error("equipment.$i.status") <div class="utm-error-msg">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label class="utm-label">Image</label>
                                        @if($eq->image)
                                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                                                <img src="{{ Storage::url($eq->image) }}" alt="{{ $eq->name }}"
                                                     style="height:40px;width:40px;object-fit:cover;border-radius:8px;border:1px solid var(--slate-200);">
                                                <span style="font-size:12px;color:var(--slate-400);">Current image. Upload new to replace.</span>
                                            </div>
                                        @endif
                                        <input type="file" name="equipment[{{ $i }}][image]" accept="image/*"
                                               class="utm-input" style="padding:8px 14px;cursor:pointer;">
                                        @error("equipment.$i.image") <div class="utm-error-msg">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div id="no-equipment-msg" style="padding:40px;text-align:center;color:var(--slate-300);font-size:13.5px;">
                                <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="var(--slate-200)" stroke-width="1.5" style="margin:0 auto 10px;display:block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                                No equipment added yet.<br>
                                <span style="font-size:12.5px;">Click "+ Add" above to add equipment.</span>
                            </div>
                        @endforelse
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
                            <span>Set status to <strong>Not Available</strong> to hide equipment from bookings.</span>
                        </div>
                        <div style="font-size:12.5px;color:#92600A;display:flex;gap:8px;align-items:flex-start;">
                            <span style="flex-shrink:0;">•</span>
                            <span>Use <strong>Manage Closures</strong> to block specific dates or time slots.</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;" class="animate-in animate-in-delay-2">
            <a href="{{ route('facilities.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
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
        let eqIndex  = {{ $facility->equipment->count() }};
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