<?php

namespace App\Http\Controllers;

use App\Services\Wikidata;

class WikidataThingController extends Controller
{
    public function __construct(private Wikidata $wd) {}

    public function show(string $qid)
    {
        $lang = request('lang', 'en');
        $basics = $this->wd->entityBasics($qid, $lang);
        $raw = $basics['raw'];

        // Example: Pull instance-of (P31) QIDs
        $claims = $raw['claims']['P31'] ?? [];
        $instances = collect($claims)->map(function ($c) {
            $v = $c['mainsnak']['datavalue']['value'] ?? null;
            return is_array($v) && isset($v['id']) ? $v['id'] : null;
        })->filter()->values();

        return view('wikidata.thing', [
            'qid'       => $qid,
            'label'     => $basics['label'],
            'desc'      => $basics['desc'],
            'instances' => $instances,
            'entity'    => $raw,
        ]);
    }
}

