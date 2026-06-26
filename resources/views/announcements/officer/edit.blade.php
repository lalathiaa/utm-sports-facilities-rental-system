<x-app-layout>
    <x-slot name="header">Edit Announcement</x-slot>

    <div style="max-width:700px;margin:0 auto;">
        <div class="utm-card animate-in">
            <div class="utm-card-header">
                <span class="utm-card-title">Edit Announcement</span>
                <span style="font-size:12px;color:var(--slate-400);">
                    Posted {{ $announcement->announcement_time->format('d M Y, H:i') }}
                </span>
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
                        <textarea id="message" name="message" rows="6"
                                  class="utm-input {{ $errors->has('message') ? 'error' : '' }}"
                                  maxlength="5000" style="resize:vertical;"
                                  placeholder="Write your announcement here…">{{ old('message', $announcement->message) }}</textarea>
                        <div class="utm-helper-text">Maximum 5,000 characters.</div>
                        @error('message')
                            <div class="utm-error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="utm-form-group">
                        <label class="utm-label" for="announcement_time">Announcement Time <span style="color:var(--danger);">*</span></label>
                        <input type="datetime-local" id="announcement_time" name="announcement_time"
                               class="utm-input {{ $errors->has('announcement_time') ? 'error' : '' }}"
                               value="{{ old('announcement_time', $announcement->announcement_time->format('Y-m-d\TH:i')) }}">
                        @error('announcement_time')
                            <div class="utm-error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="display:flex;gap:12px;justify-content:flex-end;">
                        <a href="{{ route('officer.announcements.index') }}" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
