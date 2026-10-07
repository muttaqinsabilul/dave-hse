<?php

namespace Tests\Feature;

use App\Models\Worker;
use Database\Seeders\RiskMatrixSeeder;
use Database\Seeders\SiteCounterSeeder;
use Database\Seeders\SiteSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SiteSeeder::class, SiteCounterSeeder::class, RiskMatrixSeeder::class, UserSeeder::class]);
    }

    public function test_guest_dialihkan_ke_masuk(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('masuk'));
    }

    public function test_admin_melihat_semua_site(): void
    {
        Worker::factory()->forSite('01')->create(['nama' => 'Pekerja Jember']);
        Worker::factory()->forSite('02')->create(['nama' => 'Pekerja Situbondo']);

        $response = $this->post(route('masuk.post'), ['id' => 'ADM-001', 'kind' => 'hse'])
            ->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pekerja Jember')
            ->assertSee('Pekerja Situbondo');
    }

    public function test_inspector_terkunci_di_wilayahnya(): void
    {
        Worker::factory()->forSite('01')->create(['nama' => 'Pekerja Jember']);
        Worker::factory()->forSite('02')->create(['nama' => 'Pekerja Situbondo']);

        $this->post(route('masuk.post'), ['id' => 'INS-02-001', 'kind' => 'hse'])
            ->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pekerja Situbondo')
            ->assertDontSee('Pekerja Jember');
    }

    public function test_pekerja_hanya_melihat_data_sendiri(): void
    {
        $own = Worker::factory()->forSite('01')->create(['nama' => 'Pekerja Saya']);
        Worker::factory()->forSite('01')->create(['nama' => 'Pekerja Lain']);

        $this->post(route('masuk.post'), ['id' => $own->id, 'kind' => 'pekerja'])
            ->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pekerja Saya')
            ->assertDontSee('Pekerja Lain');
    }

    public function test_id_tidak_terdaftar_ditolak(): void
    {
        $this->post(route('masuk.post'), ['id' => '99-999', 'kind' => 'pekerja'])
            ->assertSessionHasErrors('id');
    }

    public function test_dashboard_mendukung_filter_range_tanggal(): void
    {
        $this->post(route('masuk.post'), ['id' => 'ADM-001', 'kind' => 'hse']);

        $from = now()->subDays(5)->toDateString();
        $to = now()->toDateString();

        $this->get(route('dashboard', ['from' => $from, 'to' => $to]))
            ->assertOk()
            ->assertViewHas('from', $from)
            ->assertViewHas('to', $to)
            ->assertViewHas('hasDateFilter', true);
    }
}
