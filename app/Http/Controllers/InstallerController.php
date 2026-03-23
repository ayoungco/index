<?php

namespace App\Http\Controllers;

use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InstallerController extends Controller
{
    public function __construct(private readonly SiteSettings $siteSettings) {}

    public function show(): View|RedirectResponse
    {
        if ($this->siteSettings->isInstalled()) {
            return redirect()->route('home');
        }

        return view('install.show', [
            'defaults' => $this->siteSettings->all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->siteSettings->isInstalled()) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'site_url' => ['required', 'url', 'max:255'],
            'scanner_title' => ['required', 'string', 'max:160'],
            'scanner_tagline' => ['nullable', 'string', 'max:500'],
            'label_name' => ['required', 'string', 'max:120'],
            'label_tagline' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'max:4096'],
        ]);

        $logoPath = null;

        if ($request->hasFile('logo')) {
            $extension = $request->file('logo')->getClientOriginalExtension() ?: 'png';
            $logoPath = $request->file('logo')->storeAs(
                'branding',
                'site-logo-'.Str::uuid().'.'.$extension,
                'public',
            );
        }

        $this->siteSettings->install([
            'site_name' => $validated['site_name'],
            'site_url' => $validated['site_url'],
            'scanner_title' => $validated['scanner_title'],
            'scanner_tagline' => $validated['scanner_tagline'] ?? null,
            'label_name' => $validated['label_name'],
            'label_tagline' => $validated['label_tagline'] ?? null,
            'logo_path' => $logoPath,
        ]);

        return redirect()
            ->route('home')
            ->with('status', 'Installation complete. Your self-hosted scanner is ready.');
    }
}
