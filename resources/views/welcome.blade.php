<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => config('app.name')])
    </head>
    <body>
        <main class="landing-shell">
            <section class="landing-hero" aria-labelledby="landing-title">
                <img src="{{ app(\App\Support\SiteSettings::class)->logoUrl() }}" alt="{{ config('app.name') }}" class="landing-logo logo-adaptive">

                @if (session('status'))
                    <p class="app-notice landing-status">{{ session('status') }}</p>
                @endif

                <p class="landing-kicker">{{ $siteSettings['scanner_title'] }}</p>
                <h1 id="landing-title" class="landing-title">
                    Track every asset from first label to final handoff.
                </h1>
                <p class="landing-copy">
                    Index gives equipment, inventory, tools, and shared objects a permanent record. Scan the label, capture proof, and keep every photo, note, and change in one trusted timeline.
                </p>

                <div class="landing-actions">
                    <a class="app-btn app-btn-primary" href="{{ route('login') }}">Start tracking assets</a>
                    @auth
                        <a class="app-btn" href="{{ url('/dashboard') }}">Dashboard</a>
                    @endauth
                </div>

                <div class="landing-proof" aria-label="Asset tracking capabilities">
                    <div>
                        <strong>Identify</strong>
                        <span>Durable QR labels connect physical assets to canonical records.</span>
                    </div>
                    <div>
                        <strong>Document</strong>
                        <span>Camera-ready updates preserve condition, location, and custody.</span>
                    </div>
                    <div>
                        <strong>Resolve</strong>
                        <span>Shared timelines make lost context searchable and auditable.</span>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
