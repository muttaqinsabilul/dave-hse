<?php

namespace Tests\Feature;

use App\Models\Worker;
use Database\Seeders\RiskMatrixSeeder;
use Database\Seeders\SiteCounterSeeder;
use Database\Seeders\SiteSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SiteSeeder::class, SiteCounterSeeder::class, RiskMatrixSeeder::class, UserSeeder::class]);
        Storage::fake('public');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(string $site = '01'): array
    {
        return [
            'site_code' => $site,
            'nama' => 'Calon Pekerja',
            'jenis_pekerjaan' => 'Tukang Las',
            'mandor_subkon' => config('hse.mandor_per_site.'.$site)[0],
            'foto' => UploadedFile::fake()->image('foto.jpg'),
            'usia' => 25,
            'asal' => 'Jember',
            'riwayat_penyakit' => null,
        ];
    }

    public function test_id_auto_urut_per_site(): void
    {
        $this->post(route('pekerja.daftar.post'), $this->payload('01'))->assertRedirect();
        $this->post(route('pekerja.daftar.post'), $this->payload('01'))->assertRedirect();
        $this->post(route('pekerja.daftar.post'), $this->payload('02'))->assertRedirect();

        $this->assertTrue(Worker::where('id', '01-001')->exists());
        $this->assertTrue(Worker::where('id', '01-002')->exists());
        $this->assertTrue(Worker::where('id', '02-001')->exists());
    }

    public function test_registrasi_otomatis_login_dan_buka_id_card(): void
    {
        $response = $this->post(route('pekerja.daftar.post'), $this->payload('01'));

        $worker = Worker::where('id', '01-001')->firstOrFail();

        $response->assertRedirect(route('idcard.show', $worker));
        $this->assertSame('pekerja', session('auth_kind'));
        $this->assertSame('01-001', session('auth_id'));
        Storage::disk('public')->assertExists($worker->foto_path);
    }

    public function test_mandor_luar_site_ditolak(): void
    {
        $luarSite = config('hse.mandor_per_site.02')[0];

        $this->post(route('pekerja.daftar.post'), [...$this->payload('01'), 'mandor_subkon' => $luarSite])
            ->assertSessionHasErrors('mandor_subkon');

        $this->post(route('pekerja.daftar.post'), [...$this->payload('01'), 'mandor_subkon' => 'Asal Ketik'])
            ->assertSessionHasErrors('mandor_subkon');

        $this->assertFalse(Worker::where('id', '01-001')->exists());
    }

    public function test_usia_di_luar_batas_ditolak(): void
    {
        $this->post(route('pekerja.daftar.post'), [...$this->payload('01'), 'usia' => 16])
            ->assertSessionHasErrors('usia');

        $this->assertFalse(Worker::where('id', '01-001')->exists());
    }
}
