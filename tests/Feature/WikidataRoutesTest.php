<?php

use App\Models\Item;
use App\Models\User;
use App\Services\Wikidata;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    installApplication();
    $this->actingAs(User::factory()->create(), 'auth0-session');

    $fake = Mockery::mock(Wikidata::class);
    $fake->shouldReceive('entityBasics')
        ->andReturnUsing(function (string $qid): array {
            if ($qid === 'Q629') {
                return [
                    'label' => 'oxygen',
                    'desc' => 'chemical element with symbol O and atomic number 8',
                    'raw' => [
                        'claims' => [
                            'P31' => [
                                [
                                    'mainsnak' => [
                                        'datavalue' => [
                                            'value' => ['id' => 'Q11344'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            }

            return [
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
            ];
        });

    $fake->shouldReceive('search')
        ->andReturn([
            [
                'id' => 'Q629',
                'label' => 'oxygen',
                'description' => 'chemical element with symbol O and atomic number 8',
            ],
            [
                'id' => 'not-a-qid',
                'label' => 'Invalid result',
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

it('searches wikidata concepts for the claim flow', function () {
    $response = $this->getJson('/wd/search?q=oxygen');

    $response
        ->assertOk()
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.id', 'Q629')
        ->assertJsonPath('results.0.label', 'oxygen')
        ->assertJsonPath('results.0.description', 'chemical element with symbol O and atomic number 8');
});

it('does not search wikidata for a one-character query', function () {
    $this->getJson('/wd/search?q=o')
        ->assertOk()
        ->assertJsonCount(0, 'results');
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

it('enriches an item page from its wikidata qid and derives a namespace', function () {
    $item = Item::factory()->create([
        'name' => 'Oxygen tank shelf 3',
        'slug' => 'oxygen-tank-shelf-3',
        'wikidata_qid' => 'Q629',
        'type_namespace' => null,
    ]);

    $response = $this->get('/'.$item->uuid);

    $response->assertOk();
    $response->assertSee('oxygen');
    $response->assertSee('Q629');
    $response->assertSee('Q11344');
    $response->assertSee('/element/oxygen-tank-shelf-3');

    expect($item->fresh()->type_namespace)->toBe('element');
});

it('resolves item semantic urls through namespace and slug', function () {
    $item = Item::factory()->create([
        'name' => 'Oxygen tank shelf 3',
        'slug' => 'oxygen-tank-shelf-3',
        'type_namespace' => 'element',
    ]);

    $response = $this->get('/element/oxygen-tank-shelf-3');

    $response->assertOk();
    $response->assertSee($item->name);
    $response->assertSee($item->uuid);
});
