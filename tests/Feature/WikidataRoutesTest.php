<?php

use App\Services\Wikidata;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    installApplication();

    $fake = Mockery::mock(Wikidata::class);
    $fake->shouldReceive('entityBasics')
        ->andReturn([
            'label' => 'Douglas Adams',
            'desc' => 'English writer',
            'raw' => [
                'claims' => [
                    'P31' => [
                        [
                            'mainsnak' => [
                                'datavalue' => [
                                    'value' => ['id' => 'Q5'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

    $fake->shouldReceive('sparql')
        ->andReturn([
            [
                'item' => ['value' => 'http://www.wikidata.org/entity/Q42'],
                'itemLabel' => ['value' => 'Douglas Adams'],
            ],
        ]);

    app()->instance(Wikidata::class, $fake);
});

it('loads a wikidata entity route', function () {
    $resp = $this->get('/wd/item/Q42');

    $resp->assertOk();
    $resp->assertSee('Douglas Adams');
    $resp->assertSee('Q42');
});

it('loads a wikidata type route', function () {
    $resp = $this->get('/wd/type/human?limit=1');

    $resp->assertOk();
    $resp->assertSee('Type: Human');
    $resp->assertSee('Douglas Adams');
});

it('rejects unsupported wikidata types', function () {
    $this->get('/wd/type/not-a-real-type')->assertNotFound();
});
