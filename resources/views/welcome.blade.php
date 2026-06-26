<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UTM Sports Facilities Rental System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-grad: linear-gradient(135deg, var(--utm-maroon-dark) 0%, var(--utm-maroon) 60%, #A50000 100%);
        }
        body {
            font-family: var(--font-body);
            background-color: var(--slate-50);
            color: var(--slate-800);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .landing-header {
            background: white;
            border-bottom: 1px solid var(--slate-200);
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .logo-mark {
            width: 40px;
            height: 40px;
            background: var(--utm-maroon);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Georgia', serif;
            font-weight: 700;
            font-size: 13px;
            color: white;
            box-shadow: 0 4px 12px rgba(139, 0, 0, 0.2);
        }
        .logo-text {
            display: flex;
            flex-direction: column;
        }
        .logo-text .app-title {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--utm-maroon);
            line-height: 1.2;
        }
        .logo-text .univ-title {
            font-size: 10.5px;
            color: var(--slate-400);
            letter-spacing: 0.02em;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .hero-section {
            background: var(--primary-grad);
            padding: 96px 40px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .hero-section::before {
            content: '';
            position: absolute;
            right: -60px;
            top: -60px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
        }
        .hero-section::after {
            content: '';
            position: absolute;
            left: -40px;
            bottom: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(201, 168, 76, 0.05);
        }
        .hero-title {
            font-family: var(--font-display);
            font-size: 42px;
            font-weight: 600;
            margin: 0 0 20px;
            line-height: 1.2;
        }
        .hero-subtitle {
            font-size: 17px;
            color: rgba(255, 255, 255, 0.75);
            max-width: 640px;
            margin: 0 auto 36px;
            line-height: 1.6;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            max-width: 1200px;
            margin: -48px auto 0;
            padding: 0 40px;
            position: relative;
            z-index: 10;
        }
        .feature-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-elevated);
            border: 1px solid var(--slate-200);
            text-align: center;
            transition: all 0.2s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
        }
        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(139, 0, 0, 0.06);
            color: var(--utm-maroon);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .feature-title {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 10px;
            color: var(--slate-800);
        }
        .feature-desc {
            font-size: 13.5px;
            color: var(--slate-400);
            margin: 0;
            line-height: 1.6;
        }
        .content-container {
            max-width: 1200px;
            width: 100%;
            margin: 64px auto;
            padding: 0 40px;
        }
        .section-title {
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 600;
            color: var(--slate-800);
            margin: 0 0 8px;
            text-align: center;
        }
        .section-subtitle {
            font-size: 14.5px;
            color: var(--slate-400);
            text-align: center;
            margin: 0 0 40px;
        }
        .landing-footer {
            margin-top: auto;
            background: white;
            border-top: 1px solid var(--slate-200);
            padding: 24px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: var(--slate-400);
            flex-wrap: wrap;
            gap: 12px;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <header class="landing-header">
        <a href="/" class="logo-area">
            <div class="logo-mark">UTM</div>
            <div class="logo-text">
                <span class="app-title">Sports Facilities</span>
                <span class="univ-title">Universiti Teknologi Malaysia</span>
            </div>
        </a>
        <nav class="nav-links">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Sign In</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                    @endif
                @endauth
            @endif
        </nav>
    </header>

    {{-- Hero --}}
    <section class="hero-section">
        <h1 class="hero-title">Book Sports Facilities with Ease</h1>
        <p class="hero-subtitle">Access world-class sports facilities at UTM. Reserve courts, fields, and equipment in minutes — anytime, anywhere.</p>
        <div>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-gold btn-lg">Go to Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-gold btn-lg">Get Started Now</a>
            @auth
                <a href="{{ route('facilities.index') }}" class="btn btn-outline btn-lg" style="color: white; border-color: rgba(255,255,255,.30);">Browse Facilities</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline btn-lg" style="color: white; border-color: rgba(255,255,255,.30);">Browse Facilities</a>
            @endauth
            @endauth
        </div>
    </section>

    {{-- Features --}}
    <section class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="feature-title">Real-time Booking</h3>
            <p class="feature-desc">Select available slots and make instant bookings. No more long queues or manual forms.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <h3 class="feature-title">Secure Payments</h3>
            <p class="feature-desc">Pay securely using multiple payment options. Receive instant booking confirmation slips.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <h3 class="feature-title">Verified Feedback</h3>
            <p class="feature-desc">Check verified facility ratings and reviews from students and staff before you make a booking.</p>
        </div>
    </section>

    {{-- Facilities section --}}
    @php
        $facilities = \App\Models\Facility::with('feedbacks')->where('status', 'available')->get();
    @endphp
    @if($facilities->isNotEmpty())
        <section class="content-container">
            <h2 class="section-title">Explore Our Facilities</h2>
            <p class="section-subtitle">Choose from a variety of top-tier sports fields and indoor courts</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
                @foreach($facilities as $facility)
                    <div class="facility-card">
                        {{-- Image --}}
                        <div class="facility-card-image">
                            @if($facility->image)
                                <img src="{{ Storage::url($facility->image) }}" alt="{{ $facility->name }}">
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: linear-gradient(135deg, var(--slate-100), var(--slate-50));">
                                    <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="var(--slate-300)" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/>
                                    </svg>
                                </div>
                            @endif
                            <div style="position: absolute; top: 12px; right: 12px;">
                                <span class="badge badge-available">Available</span>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="facility-card-body" style="text-align: left; padding: 20px;">
                            <h3 style="font-family: var(--font-display); font-size: 17px; font-weight: 600; color: var(--slate-800); margin: 0 0 6px;">{{ $facility->name }}</h3>
                            <div style="font-size: 14.5px; font-weight: 700; color: var(--utm-maroon); margin-bottom: 12px;">
                                RM {{ number_format($facility->price, 2) }} <span style="font-size: 12px; color: var(--slate-400); font-weight: 500;">/ slot</span>
                            </div>

                            @php
                                $avgRating = $facility->averageRating();
                                $fbCount = $facility->feedbackCount();
                            @endphp
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 16px;">
                                <div style="display: flex; gap: 1px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="{{ $i <= round($avgRating) ? 'var(--utm-gold)' : 'var(--slate-200)' }}" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27Z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span style="font-size: 12px; color: var(--slate-500); font-weight: 600;">{{ $avgRating > 0 ? number_format($avgRating, 1) : '—' }}</span>
                                <span style="font-size: 11.5px; color: var(--slate-400);">({{ $fbCount }} review{{ $fbCount !== 1 ? 's' : '' }})</span>
                            </div>

                            {{-- CTA Button --}}
                            @auth
                                <a href="{{ route('bookings.create', $facility) }}" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">Book Facility</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">Book Facility</a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Footer --}}
    <footer class="landing-footer">
        <span>© {{ date('Y') }} Universiti Teknologi Malaysia. All rights reserved.</span>
        <span>UTM Sports Facilities Rental System</span>
    </footer>

</body>
</html>
