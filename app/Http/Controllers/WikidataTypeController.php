<?php

namespace App\Http\Controllers;

use App\Services\Wikidata;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WikidataTypeController extends Controller
{
    public function __construct(private Wikidata $wd) {}

    public function index(Request $req, string $type)
    {
        $map = [
            'human' => 'Q5',
            'city'  => 'Q515',
            'book'  => 'Q571',
        ];
        abort_unless(isset($map[$type]), 404);

        $qid = $map[$type];
        $lang = $req->get('lang', 'en');
        $limit = min((int) $req->get('limit', 20), 100);

        $sparql = <<<SPARQL
SELECT ?item ?itemLabel WHERE {
  ?item wdt:P31 wd:$qid .
  SERVICE wikibase:label { bd:serviceParam wikibase:language "$lang,en". }
}
LIMIT $limit
SPARQL;

        $rows = $this->wd->sparql($sparql);

        $items = collect($rows)->map(fn($r) => [
            'uri'   => $r['item']['value'],
            'qid'   => Str::after($r['item']['value'], 'http://www.wikidata.org/entity/'),
            'label' => $r['itemLabel']['value'] ?? null,
        ])->all();

        return view('wikidata.type', compact('type', 'items', 'qid'));
    }
}

