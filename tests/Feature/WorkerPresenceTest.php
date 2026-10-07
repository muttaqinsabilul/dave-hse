<?php

namespace Tests\Feature;

use App\Models\Worker;
use Database\Seeders\RiskMatrixSeeder;
use Database\Seeders\SiteCounterSeeder;
use Database\Seeders\SiteSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkerPresenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SiteSeeder::class, SiteCounterSeeder::class, RiskMatrixSeeder::class, UserSeeder::class]);
    }

    public function test_hse_bisa_toggle_status_keberadaan_pekerja(): void
    {
        $worker = Worker::factory()->forSite('01')->onsite()->create(['nama' => 'Budi Santoso']);

        $this->post(route('masuk.post'), ['id' => 'INS-01-001', 'kind' => 'hse'])
            ->assertRedirect(route('dashboard'));

        $this->assertTrue($worker->fresh()->isOnsite());

        // Toggle to offsite
        $this->post(route('pekerja.toggle-lokasi', $worker))
            ->assertSessionHas('status')
            ->assertRedirect();

        $this->assertFalse($worker->fresh()->isOnsite());
        $this->assertSame(Worker::STATUS_OFFSITE, $worker->fresh()->status_lokasi);

        // Toggle back to onsite
        $this->post(route('pekerja.toggle-lokasi', $worker))
            ->assertSessionHas('status')
            ->assertRedirect();

        $this->assertTrue($worker->fresh()->isOnsite());
        $this->assertSame(Worker::STATUS_ONSITE, $worker->fresh()->status_lokasi);
    }

    public function test_inspector_beda_site_dilarang_toggle_pekerja(): void
    {
        $worker = Worker::factory()->forSite('02')->create();

        // Login as Inspector Site 01
        $this->post(route('masuk.post'), ['id' => 'INS-01-001', 'kind' => 'hse']);

        $this->post(route('pekerja.toggle-lokasi', $worker))
            ->assertForbidden();
    }

    public function test_dashboard_menampilkan_kpi_onsite_dan_offsite(): void
    {
        Worker::factory()->forSite('01')->onsite()->create(['nama' => 'Pekerja Lapangan']);
        Worker::factory()->forSite('01')->offsite()->create(['nama' => 'Pekerja Pulang']);

        $this->post(route('masuk.post'), ['id' => 'INS-01-001', 'kind' => 'hse']);

        $response = $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pekerja On-site')
            ->assertSee('Pekerja Off-site')
            ->assertSee('Pekerja Lapangan')
            ->assertSee('Pekerja Pulang');

        $stats = $response->viewData('stats');
        $this->assertSame(1, $stats['pekerja_onsite']);
        $this->assertSame(1, $stats['pekerja_offsite']);
    }
}
