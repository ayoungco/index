<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Wikidata
{
    private string $base;
    private string $sparql;
    private array $headers;
    private int $ttl;

    public function __construct()
    {
        $cfg = config('services.wikidata');
        $this->base = rtrim($cfg['base'] ?? 'https://www.wikidata.org', '/');
        $this->sparql = $cfg['sparql'] ?? 'https://query.wikidata.org/sparql';
        $this->headers = [
            'User-Agent' => $cfg['ua'] ?? 'index/0.1 (adam@index)',
            'Accept'     => 'application/json',
        ];
        $this->ttl = (int) ($cfg['ttl'] ?? 43200);
    }

    /** Fetch raw entity JSON for a Q-id */
    public function entity(string $qid): array
    {
        $url = $this->base . '/wiki/Special:EntityData/' . urlencode($qid) . '.json';
        $key = 'wd:entity:' . $qid;
        return Cache::remember($key, $this->ttl, function () use ($url) {
            return $this->getJsonWithRetry($url);
        });
    }

    /** Search entities via wbsearchentities */
    public function search(string $query, string $lang = 'en', int $limit = 10): array
    {
        $url = $this->base . '/w/api.php?action=wbsearchentities&format=json'
            . '&language=' . urlencode($lang)
            . '&search=' . urlencode($query)
            . '&limit=' . max(1, min(50, $limit));

        $key = 'wd:search:' . md5($url);
        return Cache::remember($key, $this->ttl, function () use ($url) {
            $j = $this->getJsonWithRetry($url);
            return $j['search'] ?? [];
        });
    }

    /** Convenience: label/desc/raw for a Q-id */
    public function entityBasics(string $qid, string $lang = 'en'): array
    {
        $j = $this->entity($qid);
        $ent = $j['entities'][$qid] ?? [];
        $label = $ent['labels'][$lang]['value'] ?? ($ent['labels']['en']['value'] ?? $qid);
        $desc  = $ent['descriptions'][$lang]['value'] ?? ($ent['descriptions']['en']['value'] ?? null);
        return ['label' => $label, 'desc' => $desc, 'raw' => $ent];
    }

    /** Execute a SPARQL query; returns rows of bindings */
    public function sparql(string $query): array
    {
        $key = 'wd:sparql:' . md5($query);
        return Cache::remember($key, $this->ttl, function () use ($query) {
            $resp = Http::withHeaders([
                'User-Agent' => $this->headers['User-Agent'],
                'Accept'     => 'application/sparql-results+json',
            ])->asForm()->post($this->sparql, ['query' => $query]);

            if (!$resp->ok()) {
                // Provide a stable structure even on error
                return [];
            }

            $json = $resp->json();
            return $json['results']['bindings'] ?? [];
        });
    }

    /** Internal: GET JSON with simple 429 backoff */
    private function getJsonWithRetry(string $url, int $tries = 3, int $backoffMs = 500): array
    {
        for ($i = 0; $i < $tries; $i++) {
            $resp = Http::withHeaders($this->headers)->get($url);
            if ($resp->status() === 429 && $i < $tries - 1) {
                usleep($backoffMs * 1000);
                $backoffMs *= 2;
                continue;
            }

            if ($resp->ok()) {
                return $resp->json() ?? [];
            }

            // If not 429 and not ok, bubble up in the last attempt as empty
            if ($i < $tries - 1) {
                usleep(50 * 1000);
                continue;
            }
        }
        return [];
    }
}

