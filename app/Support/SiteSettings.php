<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SiteSettings
{
    private ?array $settings = null;

    public function all(): array
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        if (! Schema::hasTable('app_settings')) {
            return $this->settings = $this->defaults();
        }

        $stored = AppSetting::query()
            ->pluck('value', 'key')
            ->all();

        $settings = array_merge($this->defaults(), $stored);
        $settings['installed'] = filled($settings['installed_at'] ?? null);
        $settings['site_url'] = $this->normalizeUrl($settings['site_url'] ?? null) ?? config('app.url');
        $settings['logo_url'] = $this->logoUrl($settings['logo_path'] ?? null);

        return $this->settings = $settings;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function isInstalled(): bool
    {
        return (bool) ($this->all()['installed'] ?? false);
    }

    public function install(array $settings): void
    {
        $payload = array_merge($this->defaults(), $settings, [
            'installed_at' => $settings['installed_at'] ?? now()->toIso8601String(),
        ]);

        $payload['site_url'] = $this->normalizeUrl($payload['site_url']) ?? config('app.url');
        $rows = [];

        foreach ($payload as $key => $value) {
            if ($key === 'installed' || $key === 'logo_url') {
                continue;
            }

            $rows[] = [
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::transaction(function () use ($rows): void {
            AppSetting::query()->upsert($rows, ['key'], ['value', 'updated_at']);
        });

        $this->settings = null;
    }

    public function logoUrl(?string $path = null): string
    {
        $path ??= $this->get('logo_path');

        return $path
            ? asset('storage/'.$path)
            : asset('index-h.svg');
    }

    private function defaults(): array
    {
        return [
            'installed' => false,
            'installed_at' => null,
            'site_name' => config('app.name', 'Index'),
            'site_url' => config('app.url'),
            'logo_path' => null,
            'scanner_title' => 'One trusted source.',
            'scanner_tagline' => 'Scan an item UUID, post photos from camera, and keep the canonical timeline in one place.',
            'label_name' => 'Asset Label',
            'label_tagline' => 'Scan to access the canonical item record and timeline.',
        ];
    }

    private function normalizeUrl(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        return rtrim($url, '/');
    }
}
