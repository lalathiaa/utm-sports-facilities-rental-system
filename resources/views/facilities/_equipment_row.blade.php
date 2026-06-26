{{-- Partial: facilities/_equipment_row.blade.php --}}
<button type="button" class="remove-equipment-btn"
        style="position:absolute;top:20px;right:20px;padding:4px;background:rgba(220,38,38,.08);border:none;border-radius:6px;cursor:pointer;color:var(--danger);" title="Remove">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
    </svg>
</button>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;padding-right:36px;">
    <div>
        <label class="utm-label">Equipment Name <span style="color:var(--danger);">*</span></label>
        <input type="text" name="equipment[{{ $i }}][name]" required
               value="{{ $eq['name'] ?? '' }}" placeholder="e.g. Racket" class="utm-input {{ $errors->has("equipment.$i.name") ? 'error' : '' }}">
        @error("equipment.$i.name") <div class="utm-error-msg">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="utm-label">Price per Slot (RM) <span style="color:var(--danger);">*</span></label>
        <div style="position:relative;">
            <span style="position:absolute;inset-y:0;left:0;padding-left:13px;display:flex;align-items:center;font-size:14px;color:var(--slate-400);pointer-events:none;">RM</span>
            <input type="number" name="equipment[{{ $i }}][price]" required step="0.01" min="0"
                   value="{{ $eq['price'] ?? '0.00' }}" style="padding-left:36px;" class="utm-input">
        </div>
        @error("equipment.$i.price") <div class="utm-error-msg">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="utm-label">Quantity <span style="color:var(--danger);">*</span></label>
        <input type="number" name="equipment[{{ $i }}][quantity]" required min="1"
               value="{{ $eq['quantity'] ?? 1 }}" class="utm-input">
        @error("equipment.$i.quantity") <div class="utm-error-msg">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="utm-label">Status <span style="color:var(--danger);">*</span></label>
        <select name="equipment[{{ $i }}][status]" required class="utm-select">
            <option value="available" {{ ($eq['status'] ?? '') === 'available' ? 'selected' : '' }}>Available</option>
            <option value="not_available" {{ ($eq['status'] ?? '') === 'not_available' ? 'selected' : '' }}>Not Available</option>
        </select>
        @error("equipment.$i.status") <div class="utm-error-msg">{{ $message }}</div> @enderror
    </div>
    <div style="grid-column:1/-1;">
        <label class="utm-label">Image (optional)</label>
        <input type="file" name="equipment[{{ $i }}][image]" accept="image/*" class="utm-input" style="padding:8px 14px;cursor:pointer;">
        @error("equipment.$i.image") <div class="utm-error-msg">{{ $message }}</div> @enderror
    </div>
</div>