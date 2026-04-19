<?php

namespace App\Http\Controllers;

use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SiteSettingsController extends Controller
{
    public function __construct(private readonly SiteSettings $siteSettings) {}

    public function edit(): View
    {
        return view('settings.site', [
            'defaults' => $this->siteSettings->all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'site_url' => ['required', 'url', 'max:255'],
            'scanner_title' => ['required', 'string', 'max:160'],
            'scanner_tagline' => ['nullable', 'string', 'max:500'],
            'label_name' => ['required', 'string', 'max:120'],
            'label_tagline' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        $logoPath = $this->siteSettings->get('logo_path');

        if ($request->boolean('remove_logo') && $logoPath) {
            Storage::disk('public')->delete($logoPath);
            $logoPath = null;
        }

        if ($request->hasFile('logo')) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            $extension = $request->file('logo')->getClientOriginalExtension() ?: 'png';
            $logoPath = $request->file('logo')->storeAs(
                'branding',
                'site-logo-'.Str::uuid().'.'.$extension,
                'public',
            );
        }

        $this->siteSettings->save([
            'site_name' => $validated['site_name'],
            'site_url' => $validated['site_url'],
            'scanner_title' => $validated['scanner_title'],
            'scanner_tagline' => $validated['scanner_tagline'] ?? null,
            'label_name' => $validated['label_name'],
            'label_tagline' => $validated['label_tagline'] ?? null,
            'logo_path' => $logoPath,
        ]);

        return redirect()
            ->route('settings.site')
            ->with('status', 'Site settings updated.');
    }
}
