<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\AffiliateNetworks\Pages\CreateAffiliateNetwork;
use App\Filament\Resources\AffiliateNetworks\Pages\EditAffiliateNetwork;
use App\Filament\Resources\AffiliateNetworks\Pages\ListAffiliateNetworks;
use App\Models\AffiliateLink;
use App\Models\AffiliateNetwork;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class AffiliateNetworkResourceTest extends FilamentTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperAdmin();
    }

    // -------------------------------------------------------------------------
    // List Page
    // -------------------------------------------------------------------------

    public function test_can_load_list_page(): void
    {
        $networks = AffiliateNetwork::factory()->count(3)->create();

        Livewire::test(ListAffiliateNetworks::class)
            ->assertOk()
            ->assertCanSeeTableRecords($networks);
    }

    public function test_can_search_networks_by_name(): void
    {
        AffiliateNetwork::factory()->create(['name' => 'Amazon']);
        AffiliateNetwork::factory()->create(['name' => 'Awin']);

        Livewire::test(ListAffiliateNetworks::class)
            ->searchTable('Amazon')
            ->assertCanSeeTableRecords(AffiliateNetwork::where('name', 'Amazon')->get())
            ->assertCanNotSeeTableRecords(AffiliateNetwork::where('name', 'Awin')->get());
    }

    public function test_can_sort_networks_by_name(): void
    {
        AffiliateNetwork::factory()->create(['name' => 'Amazon']);
        AffiliateNetwork::factory()->create(['name' => 'Awin']);
        AffiliateNetwork::factory()->create(['name' => 'Digistore24']);

        $sortedAsc  = AffiliateNetwork::orderBy('name')->get();
        $sortedDesc = AffiliateNetwork::orderBy('name', 'desc')->get();

        Livewire::test(ListAffiliateNetworks::class)
            ->sortTable('name')
            ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
            ->sortTable('name', 'desc')
            ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
    }

    // -------------------------------------------------------------------------
    // Create Page
    // -------------------------------------------------------------------------

    public function test_can_load_create_page(): void
    {
        Livewire::test(CreateAffiliateNetwork::class)
            ->assertOk();
    }

    public function test_can_create_network(): void
    {
        Livewire::test(CreateAffiliateNetwork::class)
            ->fillForm(['name' => 'ShareASale'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('affiliate_networks', ['name' => 'ShareASale']);
    }

    public function test_name_is_required(): void
    {
        Livewire::test(CreateAffiliateNetwork::class)
            ->fillForm(['name' => null])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_name_must_be_unique(): void
    {
        AffiliateNetwork::factory()->create(['name' => 'Amazon']);

        Livewire::test(CreateAffiliateNetwork::class)
            ->fillForm(['name' => 'Amazon'])
            ->call('create')
            ->assertHasFormErrors(['name' => 'unique']);
    }

    // -------------------------------------------------------------------------
    // Edit Page
    // -------------------------------------------------------------------------

    public function test_can_load_edit_page(): void
    {
        $network = AffiliateNetwork::factory()->create();

        Livewire::test(EditAffiliateNetwork::class, ['record' => $network->getRouteKey()])
            ->assertOk()
            ->assertSchemaStateSet(['name' => $network->name]);
    }

    public function test_can_update_network(): void
    {
        $network = AffiliateNetwork::factory()->create(['name' => 'Amazon']);

        Livewire::test(EditAffiliateNetwork::class, ['record' => $network->getRouteKey()])
            ->fillForm(['name' => 'Amazon Updated'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('affiliate_networks', [
            'id'   => $network->id,
            'name' => 'Amazon Updated',
        ]);
    }

    public function test_name_must_be_unique_but_ignores_current_record(): void
    {
        $network = AffiliateNetwork::factory()->create(['name' => 'Amazon']);

        Livewire::test(EditAffiliateNetwork::class, ['record' => $network->getRouteKey()])
            ->fillForm(['name' => 'Amazon'])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function test_can_delete_network_without_links(): void
    {
        $network = AffiliateNetwork::factory()->create();

        Livewire::test(EditAffiliateNetwork::class, ['record' => $network->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($network);
    }

    public function test_delete_is_blocked_when_network_has_links(): void
    {
        $network = AffiliateNetwork::factory()->create();
        AffiliateLink::factory()->create(['affiliate_network_id' => $network->id]);

        Livewire::test(EditAffiliateNetwork::class, ['record' => $network->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertModelExists($network);
    }
}
