Thought for 12s
A clean pattern is:
Store comments separately from the URL entity
Normalize URLs before saving
Allow comments only on whitelisted/explicit URLs
Hash/canonicalize URLs so duplicates collapse together
Minimal schema:
urls
----
id
canonical_url
url_hash
created_at

comments
--------
id
url_id
user_id
body
created_at
Normalization example:
function canonicalize(url) {
  const u = new URL(url)

  u.hash = ""
  u.hostname = u.hostname.toLowerCase()

  if (u.pathname.endsWith("/"))
    u.pathname = u.pathname.slice(0, -1)

  return u.toString()
}
Flow:
user submits URL
  -> normalize
  -> validate explicit/allowed domain
  -> find/create url record
  -> attach comments
Example route structure:
POST /urls
POST /urls/:id/comments
GET  /urls/:id/comments
If by “explicit URLs” you mean:
NSFW links
manually approved URLs
URLs explicitly tagged by users
only exact URLs (not domains)
…the implementation changes slightly.
lol not that kind of explicit. Just keep the url as untransfotmed as possible. People should be able to punch a URL in to index and see the information we 
I’ll treat this as a code-change request against your repo’s main: find the Index app shape, then propose or apply the smallest patch that preserves submitted URLs verbatim while enabling comments/lookups.

Received app response
Found ayoungco/index on main. The important correction is: keep url as the user typed it for display/lookup, and use a separate normalized/search key only if the app needs dedupe later.

Received app response

Received app response
The existing app already has items, item_events, and a timeline/comment concept. I’m going to keep this additive: add URL-backed items instead of mutating existing QR/photo behavior.

Received app response

Received app response

Running app request
Thought for 2m 28s
Yep — better model:
url_entries
- id
- url        # exact user-entered URL, trimmed only
- url_hash   # sha256($url), for exact lookup
- title
- description

url_comments
- id
- url_entry_id
- user_id
- body
Flow:
$url = trim($request->input('url')); // no canonicalization
$hash = hash('sha256', $url);

$entry = UrlEntry::firstOrCreate(
    ['url_hash' => $hash],
    ['url' => $url, 'user_id' => $request->user()->id]
);
Routes to add before the catch-all slug route:
Route::post('urls', [UrlEntryController::class, 'store'])->name('urls.store');
Route::get('urls/{urlEntry}', [UrlEntryController::class, 'show'])->name('urls.show');
Route::post('urls/{urlEntry}/comments', [UrlEntryController::class, 'storeComment'])->name('urls.comments.store');
I found the right spots: dashboard currently has create/search UI, item pages already have a timeline/comment pattern, and routes/web.php has a catch-all slug route near the bottom that URL routes must go before.
I tried to apply the files directly in GitHub, but the write call was blocked.
