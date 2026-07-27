<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SiteSettings
{
    private const DEFAULT_PRIMARY_COLOR = '#000000';

    private const DEFAULT_BACKGROUND_COLOR = '#ffffff';

    private const DEFAULT_HIGHLIGHT_COLOR = '#ff4f00';

    private const WRITABLE_KEYS = [
        'site_name',
        'logo_path',
        'scanner_title',
        'scanner_tagline',
        'label_name',
        'label_tagline',
        'primary_color',
        'background_color',
        'highlight_color',
    ];

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
        $settings['site_url'] = $this->environmentUrl();
        $settings['primary_color'] = $this->normalizeColor($settings['primary_color'] ?? null, self::DEFAULT_PRIMARY_COLOR);
        $settings['background_color'] = $this->normalizeColor($settings['background_color'] ?? null, self::DEFAULT_BACKGROUND_COLOR);
        $settings['highlight_color'] = $this->normalizeColor($settings['highlight_color'] ?? null, self::DEFAULT_HIGHLIGHT_COLOR);
        $settings['theme_is_default'] = $settings['primary_color'] === self::DEFAULT_PRIMARY_COLOR
            && $settings['background_color'] === self::DEFAULT_BACKGROUND_COLOR
            && $settings['highlight_color'] === self::DEFAULT_HIGHLIGHT_COLOR;
        $settings['logo_url'] = ($settings['logo_path'] ?? null)
            ? $this->mediaUrl($settings['logo_path'])
            : asset('index-h.svg');

        return $this->settings = $settings;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function isInstalled(): bool
    {
        return true;
    }

    public function save(array $settings): void
    {
        $payload = array_intersect_key(
            array_merge($this->defaults(), $settings),
            array_flip(self::WRITABLE_KEYS),
        );

        $rows = [];

        foreach ($payload as $key => $value) {
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

    public function install(array $settings): void
    {
        $this->save($settings);
    }

    public function logoUrl(?string $path = null): string
    {
        if (func_num_args() === 0) {
            $path = $this->get('logo_path');
        }

        return $path
            ? $this->mediaUrl($path)
            : asset('index-h.svg');
    }

    private function defaults(): array
    {
        return [
            'site_name' => config('app.name', 'Index'),
            'site_url' => $this->environmentUrl(),
            'logo_path' => null,
            'scanner_title' => 'One trusted source.',
            'scanner_tagline' => 'Scan an item UUID, post photos from camera, and keep the canonical timeline in one place.',
            'label_name' => 'Asset Label',
            'label_tagline' => 'Scan to access the canonical item record and timeline.',
            'primary_color' => self::DEFAULT_PRIMARY_COLOR,
            'background_color' => self::DEFAULT_BACKGROUND_COLOR,
            'highlight_color' => self::DEFAULT_HIGHLIGHT_COLOR,
            'theme_is_default' => true,
        ];
    }

    private function normalizeUrl(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        return rtrim($url, '/');
    }

    private function mediaUrl(string $path): string
    {
        return url('/media/'.ltrim($path, '/'));
    }

    private function environmentUrl(): string
    {
        return $this->normalizeUrl(config('app.url')) ?? 'http://localhost';
    }

    private function normalizeColor(mixed $color, string $fallback): string
    {
        return is_string($color) && preg_match('/^#[0-9a-f]{6}$/i', $color)
            ? strtolower($color)
            : $fallback;
    }
}
