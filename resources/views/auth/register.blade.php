<x-guest-layout>

    <div class="animate-in">
        <div style="margin-bottom:32px;">
            <h2 style="font-family:'Lora',Georgia,serif;font-size:26px;font-weight:600;color:var(--slate-800);margin:0 0 8px;">
                Create an account
            </h2>
            <p style="font-size:14px;color:var(--slate-400);margin:0;">
                Register with your UTM email to get started
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Full Name --}}
            <div class="utm-form-group animate-in animate-in-delay-1">
                <label for="fullname" class="utm-label">Full Name</label>
                <input id="fullname" type="text" name="fullname" value="{{ old('fullname') }}" required autofocus
                       placeholder="As per identification card"
                       class="utm-input {{ $errors->has('fullname') ? 'error' : '' }}">
                @error('fullname')
                    <div class="utm-error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Username --}}
            <div class="utm-form-group animate-in animate-in-delay-1">
                <label for="username" class="utm-label">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required
                       placeholder="Choose a unique username"
                       class="utm-input {{ $errors->has('username') ? 'error' : '' }}">
                @error('username')
                    <div class="utm-error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- IC Number --}}
            <div class="utm-form-group animate-in animate-in-delay-1">
                <label for="ic_number" class="utm-label">IC Number</label>
                <input id="ic_number" type="text" name="ic_number" value="{{ old('ic_number') }}" required
                       placeholder="e.g. 990101011234"
                       class="utm-input {{ $errors->has('ic_number') ? 'error' : '' }}">
                @error('ic_number')
                    <div class="utm-error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="utm-form-group animate-in animate-in-delay-2">
                <label for="email" class="utm-label">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       placeholder="yourname@utm.my or @graduate.utm.my"
                       oninput="detectRole(this.value)"
                       class="utm-input {{ $errors->has('email') ? 'error' : '' }}">
                @error('email')
                    <div class="utm-error-msg">{{ $message }}</div>
                @enderror

                {{-- Role Badge --}}
                <div id="role-badge" style="display:none;margin-top:8px;">
                    <span id="role-badge-text" class="badge"></span>
                </div>
            </div>

            {{-- Matric (student) --}}
            <div class="utm-form-group animate-in animate-in-delay-2" id="matric-field" style="display:none;">
                <label for="matric_number" class="utm-label">Matric Number</label>
                <input id="matric_number" type="text" name="matric_number" value="{{ old('matric_number') }}"
                       placeholder="e.g. A23EC1234"
                       class="utm-input {{ $errors->has('matric_number') ? 'error' : '' }}">
                @error('matric_number')
                    <div class="utm-error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Staff ID (staff) --}}
            <div class="utm-form-group animate-in animate-in-delay-2" id="staff-field" style="display:none;">
                <label for="staff_id" class="utm-label">Staff ID</label>
                <input id="staff_id" type="text" name="staff_id" value="{{ old('staff_id') }}"
                       placeholder="e.g. P12345"
                       class="utm-input {{ $errors->has('staff_id') ? 'error' : '' }}">
                @error('staff_id')
                    <div class="utm-error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="utm-form-group animate-in animate-in-delay-3">
                <label for="password" class="utm-label">Password</label>
                <input id="password" type="password" name="password" required
                       autocomplete="new-password"
                       placeholder="Create a strong password"
                       class="utm-input {{ $errors->has('password') ? 'error' : '' }}">
                @error('password')
                    <div class="utm-error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="utm-form-group animate-in animate-in-delay-3">
                <label for="password_confirmation" class="utm-label">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       autocomplete="new-password"
                       placeholder="Re-enter your password"
                       class="utm-input">
            </div>

            <button type="submit" class="btn btn-primary btn-lg animate-in animate-in-delay-4"
                    style="justify-content:center;width:100%;margin-top:4px;">
                Create Account
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>

        <p style="text-align:center;font-size:13.5px;color:var(--slate-400);margin-top:24px;" class="animate-in animate-in-delay-4">
            Already have an account?
            <a href="{{ route('login') }}" style="color:var(--utm-maroon);font-weight:600;text-decoration:none;">
                Sign in
            </a>
        </p>
    </div>

    <script>
    function detectRole(email) {
        const badge      = document.getElementById('role-badge');
        const badgeText  = document.getElementById('role-badge-text');
        const matricDiv  = document.getElementById('matric-field');
        const staffDiv   = document.getElementById('staff-field');
        const matricInput = document.getElementById('matric_number');
        const staffInput  = document.getElementById('staff_id');

        // Reset
        matricDiv.style.display = 'none';
        staffDiv.style.display  = 'none';
        matricInput.removeAttribute('required');
        staffInput.removeAttribute('required');
        badge.style.display = 'none';

        if (email.endsWith('@graduate.utm.my')) {
            matricDiv.style.display = 'block';
            matricInput.setAttribute('required', 'required');
            badge.style.display = 'block';
            badgeText.textContent = '🎓 UTM Student';
            badgeText.className = 'badge badge-student';
        } else if (email.endsWith('@utm.my')) {
            staffDiv.style.display = 'block';
            staffInput.setAttribute('required', 'required');
            badge.style.display = 'block';
            badgeText.textContent = '🏛 UTM Staff';
            badgeText.className = 'badge badge-staff';
        } else if (email.includes('@') && email.indexOf('@') < email.length - 1) {
            badge.style.display = 'block';
            badgeText.textContent = '👤 Guest';
            badgeText.className = 'badge badge-guest';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const email = document.getElementById('email');
        if (email.value) detectRole(email.value);
    });
    </script>

</x-guest-layout>