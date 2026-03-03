<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>index</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --bg: #ffffff;
                --fg: #0a0a0a;
                --muted: #4a4a4a;
                --border: #e5e5e5;
                --accent: #000000;
            }
            * { box-sizing: border-box; }
            body {
                background: var(--bg);
                color: var(--fg);
                font: 16px/1.5 ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            .cta { display: inline-block; background: var(--accent); color: #fff; padding: 12px 18px; text-decoration: none; font-weight: 600; letter-spacing: 0.2px; border: 1px solid #000; box-shadow: 0 1px 0 #000; }
            .cta:hover { transform: translateY(-1px); }
            .cta:active { transform: translateY(0); }

            main { display: grid; place-items: center; padding: 60px 0 80px; }
            .hero { max-width: 820px; }
            .hero img { width: max(50%, 320px); height: auto; margin: 0 auto 4em; display: block; }
            .tagline { font-size: clamp(24px, 4vw, 42px); line-height: 1.1; font-weight: 800; letter-spacing: -0.02em; margin: 0 0 12px; }
            .desc { font-size: clamp(16px, 2vw, 18px); color: var(--muted); margin: 0 auto 28px; max-width: 720px; }
            .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

            .link { color: var(--fg); text-decoration: none; border-bottom: 2px solid var(--border); padding-bottom: 2px; font-weight: 600; }
            .link:hover { border-color: var(--fg); }

            footer { border-top: 1px solid var(--border); margin-top: 60px; }
            .foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 20px 0; font-size: 14px; color: var(--muted); }
        </style>

    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex items-center lg:justify-center min-h-screen flex-col">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                    <section class="hero" aria-labelledby="tagline">
                        <img src="/index-v.svg" alt="index">
                        <h1 class="tagline my-9">One trusted source.</h1>
                        <p class="desc">
                            A simple and secure way to manage what you own.
                        </p>
                        <div class="actions">
                            <a class="cta" href="/login">Get access</a>
                                       @if (Route::has('login'))

                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                            >
                                Register
                            </a>
                        @endif
                    @endauth
            @endif
                        </div>
                    </section>

                <footer>
                    <div class="foot">
                        <span>&copy; ayoungco</span>
                    </div>
                </footer>
            </main>

    </body>
</html>
