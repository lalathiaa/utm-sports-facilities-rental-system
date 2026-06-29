<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'UTM Sports Facilities') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin:0;padding:0;background:white;">

<div class="utm-auth-shell">

    {{-- ══ Left Panel — UTM Brand ══════════════════════════ --}}
    <div class="utm-auth-panel" style="background-image: linear-gradient(135deg, rgba(107, 0, 0, 0.60) 0%, rgba(0, 0, 0, 0.70) 100%), url('{{ asset('images/utm-scenery.png') }}');">

        {{-- Logo --}}
        <div>
            <div style="margin-bottom:40px;">
                <a href="/" style="display:inline-flex; justify-content:center; align-items:center; transition: all 0.25s ease;" onmouseover="this.style.transform='scale(1.02)';" onmouseout="this.style.transform='scale(1)';">
                    <img src="{{ asset('images/logo.png?v=4') }}" alt="UTM x Sports" style="width:100%; max-width:280px; height:auto; display:block; filter: drop-shadow(0px 0px 2px #fff) drop-shadow(0px 0px 4px rgba(255, 255, 255, 0.6));">
                </a>
            </div>

            {{-- Hero text --}}
            <div style="max-width:340px;">
                <h1 style="font-family:'Lora',Georgia,serif;font-size:36px;font-weight:600;color:white;line-height:1.25;margin:0 0 20px; text-shadow: 0 2px 4px rgba(0,0,0,0.65);">
                    Book Sports Facilities with Ease
                </h1>
                <p style="font-size:15px;color:rgba(255,255,255,.90);line-height:1.7;margin:0; text-shadow: 0 1px 3px rgba(0,0,0,0.6);">
                    Access world-class sports facilities at UTM. Reserve courts, fields, and equipment in minutes — anytime, anywhere.
                </p>
            </div>

            {{-- Feature list --}}
            <div style="margin-top:48px;display:flex;flex-direction:column;gap:16px;">
                @foreach([
                    ['icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text'=>'Real-time slot availability'],
                    ['icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'text'=>'Instant booking confirmation'],
                    ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'text'=>'Digital booking slips & receipts'],
                ] as $feat)
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:32px;height:32px;background:rgba(255,255,255,.18);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;backdrop-filter:blur(4px);">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="rgba(201,168,76,1)" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feat['icon'] }}"/>
                            </svg>
                        </div>
                        <span style="font-size:13.5px;color:rgba(255,255,255,.95);text-shadow: 0 1px 3px rgba(0,0,0,0.6);font-weight:500;">{{ $feat['text'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom --}}
        <div style="font-size:11.5px;color:rgba(255,255,255,.70);text-shadow: 0 1px 3px rgba(0,0,0,0.6);font-weight:500;">
            © {{ date('Y') }} Universiti Teknologi Malaysia
        </div>
    </div>

    {{-- ══ Right Panel — Form ═══════════════════════════════ --}}
    <div class="utm-auth-form-area">
        <div style="max-width:420px;width:100%;margin:0 auto;">
            {{ $slot }}
        </div>
    </div>

</div>

</body>
</html>