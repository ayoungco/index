<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uninitialized Object</title>
    <style>
        body { background: #000; color: #fff; font-family: "Courier New", Courier, monospace; margin: 0; padding: 16px; }
        .panel { max-width: 840px; margin: 0 auto; border: 1px solid #34d399; padding: 16px; }
        .uuid { color: #34d399; }
        .title { margin: 8px 0 0; font-size: 24px; }
        .copy { margin-top: 16px; color: #d4d4d8; }
        .btn { display: inline-block; margin-top: 16px; border: 1px solid #34d399; color: #6ee7b7; padding: 10px 14px; text-decoration: none; }
        .btn:hover { background: #34d399; color: #000; }
    </style>
</head>
<body>
    <section class="panel">
        <p class="uuid">[SCAN TARGET: {{ $uuid }}]</p>
        <h1 class="title">Object Not Initialized</h1>
        <p class="copy">This UUID is unclaimed. Log in to initialize this object.</p>
        <a
            href="/login"
            class="btn"
        >
            Login With Auth0
        </a>
    </section>
</body>
</html>
