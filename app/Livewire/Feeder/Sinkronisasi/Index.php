<?php

namespace App\Livewire\Feeder\Sinkronisasi;

use App\Models\SinkronisasiFeeder;
use App\Services\Feeder\FeederAPI;
use Illuminate\Bus\BatchRepository;
use Illuminate\Support\Facades\Bus;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Rules\LivewireFunctionExists;

class Index extends Component
{

    public string $currentAction = '';

    public $batchId;
    public $progress = 0;
    public $completed = false;

    public $sinkronisasiItems = [];

    public $showConfirmModal = false;
    public $hasActiveBatch = false;

    #[Validate('required|string')]
    public $name_create = '';

    #[Validate('required|string')]
    public $batch_name_create = '';

    #[Validate([
        'required',
        'string',
        new LivewireFunctionExists(self::class) // Validasi fungsi harus ada di component ini
    ])]
    public $function_name_create = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->sinkronisasiItems = SinkronisasiFeeder::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->name,
                'function' => $item->function_name,
                'batch_id' => $item->batch_id,
                'terakhir_sinkronisasi' => $item->terakhir_sinkronisasi,
                'progress' => $this->getBatchProgress($item->batch_id),
            ];
        })->toArray();

        $this->checkActiveBatch();
    }

    public function getBatchProgress($batchId)
    {
        if (!$batchId) return null;

        $batch = app(BatchRepository::class)->find($batchId);
        if ($batch) {
            if ($batch->finished()) {
                return null;
            }
            return $batch->progress();
        }
        return null;
    }

    public function updateProgress()
    {
        $updatedItems = [];
        $hasRunningBatch = false;
        foreach ($this->sinkronisasiItems as $item) {
            if ($item['batch_id']) {
                $progress = $this->getBatchProgress($item['batch_id']);

                // Jika batch sudah selesai, hapus batch_id dari database
                if ($progress === 100 || $progress === null) {
                    SinkronisasiFeeder::where('id', $item['id'])->update(['batch_id' => null, 'terakhir_sinkronisasi' => now()]);
                    $progress = null;
                } else {
                    $hasRunningBatch = true; // Set flag jika masih ada batch aktif
                }

                $updatedItems[] = [
                    'id' => $item['id'],
                    'nama' => $item['nama'],
                    'function' => $item['function'],
                    'terakhir_sinkronisasi' => $item['terakhir_sinkronisasi'],
                    'batch_id' => $progress !== null ? $item['batch_id'] : null,
                    'progress' => $progress,
                ];
            } else {
                $updatedItems[] = $item;
                // if batch is finished, remove batch
            }
        }
        $this->sinkronisasiItems = $updatedItems;
        $this->hasActiveBatch = $hasRunningBatch;
    }

    public function checkActiveBatch()
    {
        $this->hasActiveBatch = SinkronisasiFeeder::whereNotNull('batch_id')->exists();
    }

    public function prompt($function)
    {
        $this->currentAction = $function;

        LivewireAlert::title('Sinkronisasi')
                    ->text('Apakah anda yakin untuk melakukan sinkronisasi ini?')
                    ->asConfirm()
                    ->onConfirm($function)
                    ->show();
    }

    private function count_value($act)
    {
        $data = new FeederAPI($act,0,0, '');
        $response = $data->runWS();
        $count = $response['data'];

        return $count;
    }

    private function sync($act, $limit, $offset, $order)
    {
        $get = new FeederAPI($act, $offset, $limit, $order);

        $data = $get->runWS();

        return $data;
    }

    public function sync_referensi()
    {

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1G');

        $ref = [
            ['act' => 'GetLevelWilayah', 'primary' => 'id_level_wilayah', 'model' => \App\Models\Feeder\Referensi\LevelWilayah::class],
            ['act' => 'GetWilayah', 'primary' =>'id_wilayah', 'model' => \App\Models\Feeder\Referensi\Wilayah::class],
            ['act' => 'GetNegara', 'primary' => 'id_negara', 'model' => \App\Models\Feeder\Referensi\Negara::class],
            ['act' => 'GetStatusMahasiswa', 'primary' => 'id_status_mahasiswa', 'model' => \App\Models\Feeder\Referensi\StatusMahasiswa::class],
            ['act' => 'GetSemester', 'primary' => 'id_semester', 'model' => \App\Models\Feeder\Referensi\Semester::class],
            ['act' => 'GetJenisKeluar', 'primary' => 'id_jenis_keluar', 'model' => \App\Models\Feeder\Referensi\JenisKeluar::class],
            ['act' => 'GetJenisPendaftaran', 'primary' => 'id_jenis_daftar', 'model' => \App\Models\Feeder\Referensi\JenisDaftar::class],
            ['act' => 'GetJalurMasuk', 'primary' => 'id_jalur_masuk', 'model' => \App\Models\Feeder\Referensi\JalurMasuk::class],
            ['act' => 'GetJenisEvaluasi', 'primary' => 'id_jenis_evaluasi', 'model' => \App\Models\Feeder\Referensi\JenisEvaluasi::class],
            ['act' => 'GetIkatanKerjaSdm', 'primary' => 'id_ikatan_kerja', 'model' => \App\Models\Feeder\Referensi\IkatanKerja::class],
            ['act' => 'GetJenisSubstansi', 'primary' => 'id_jenis_substansi', 'model' => \App\Models\Feeder\Referensi\JenisSubstansi::class],
            // ['act' => 'GetListSubstansiKuliah', 'primary' => 'id_substansi', 'model' => \App\Models\Perkuliahan\Feeder\Referensi\SubstansiKuliah::class],
            ['act' => 'GetPembiayaan', 'primary' => 'id_pembiayaan', 'model' => \App\Models\Feeder\Referensi\Pembiayaan::class],
            ['act' => 'GetJenisAktivitasMahasiswa', 'primary' => 'id_jenis_aktivitas_mahasiswa', 'model' => \App\Models\Feeder\Referensi\JenisAktivitasMahasiswa::class],
            ['act' => 'GetKategoriKegiatan', 'primary' => 'id_kategori_kegiatan', 'model' => \App\Models\Feeder\Referensi\KategoriKegiatan::class],
            ['act' => 'GetAgama', 'primary' => 'id_agama', 'model' => \App\Models\Feeder\Referensi\Agama::class],
            ['act' => 'GetAlatTransportasi' , 'primary' => 'id_alat_transportasi', 'model' => \App\Models\Feeder\Referensi\AlatTransportasi::class],
            ['act' => 'GetPekerjaan' , 'primary' => 'id_pekerjaan', 'model' => \App\Models\Feeder\Referensi\Pekerjaan::class],
            ['act' => 'GetJenisPrestasi' , 'primary' => 'id_jenis_prestasi', 'model' => \App\Models\Feeder\Referensi\JenisPrestasi::class],
            ['act' => 'GetTingkatPrestasi' , 'primary' => 'id_tingkat_prestasi', 'model' => \App\Models\Feeder\Referensi\TingkatPrestasi::class],
            ['act' => 'GetProfilPT' , 'primary' => 'id_perguruan_tinggi', 'model' => \App\Models\Feeder\Referensi\ProfilPt::class],
            ['act' => 'GetProdi' , 'primary' => 'id_prodi', 'model' => \App\Models\Feeder\Referensi\ProgramStudi::class],
            ['act' => 'GetJenjangPendidikan' , 'primary' => 'id_jenjang_didik', 'model' => \App\Models\Feeder\Referensi\JenjangPendidikan::class],
            // ['act' => 'GetAllPT', 'primary' => 'id_perguruan_tinggi', 'model' => \App\Models\Referensi\AllPt::class],
        ];

        foreach ($ref as $r) {
            $act = $r['act'];
            $offset = 0;
            $limit = 0;
            $order = '';

            $data = $this->sync($act, $limit, $offset, $order);

            if (isset($data['data']) && !empty($data['data'])) {

                if ($act == 'GetWilayah') {
                    $data['data'] = array_map(function($d) {
                        $d['id_wilayah'] = trim($d['id_wilayah']);
                        $d['id_induk_wilayah'] = trim($d['id_induk_wilayah']);
                        return $d;
                    }, $data['data']);
                }

                if ($act == 'GetJenisSubstansi') {
                    $data['data'] = array_map(function($d) {
                        $d['id_jenis_substansi'] = trim($d['id_jenis_substansi']);
                        return $d;
                    }, $data['data']);
                }

                if($act == 'GetStatusMahasiswa')
                {
                    // add new status mahasiswa id_status_mahasiswa = 'K', nama_status_mahasiswa = 'Keluar'
                    $newStatusMahasiswa = [
                        ['id_status_mahasiswa' => 'K', 'nama_status_mahasiswa' => 'Keluar'],
                        ['id_status_mahasiswa' => 'D', 'nama_status_mahasiswa' => 'Drop-Out/Putus Studi'],
                        ['id_status_mahasiswa' => 'U', 'nama_status_mahasiswa' => 'Menunggu Ujian'],
                        ['id_status_mahasiswa' => 'L', 'nama_status_mahasiswa' => 'Lulus'],
                    ];
                    array_push($data['data'], ...$newStatusMahasiswa);
                }

                if($act == 'GetAgama')
                {
                    $agama = [
                        ['id_agama' => 98, 'nama_agama' => 'Tidak Diisi'],
                    ];
                    array_push($data['data'], ...$agama);
                }

                if($act == 'GetAlatTransportasi')
                {
                    $at = [
                        ['id_alat_transportasi' => 2, 'nama_alat_transportasi' => 'Kendaraan Pribadi'],
                    ];
                    array_push($data['data'], ...$at);
                }

                $r['model']::upsert($data['data'], $r['primary']);
            }
        }

        LivewireAlert::title('Sinkronisasi')
                    ->text('Sinkronisasi referensi berhasil')
                    ->success()
                    ->show();

        SinkronisasiFeeder::where('function_name', 'sync_referensi')->update(['terakhir_sinkronisasi' => now()]);

        $this->loadData();

    }

    public function sync_dosen()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1G');

        $data = [
            [
                'act' => 'DetailBiodataDosen',
                'limit' => '1000',
                'offset' => '',
                'order' => 'id_dosen',
                'count' => 'GetCountRiwayatPendidikanMahasiswa',
                'job' => \App\Jobs\Feeder\SyncJob::class,
                'model' => \App\Models\Feeder\Dosen\BiodataDosen::class,
                'primary' => 'id_dosen',
            ],
            [
                'act' => 'GetListPenugasanDosen',
                'limit' => '1000',
                'offset' => '',
                'order' => 'id_registrasi_dosen',
                'count' => 'GetCountPenugasanSemuaDosen',
                'job' => \App\Jobs\Feeder\SyncJob::class,
                'model' => \App\Models\Feeder\Dosen\PenugasanDosen::class,
                'primary' => ['id_tahun_ajaran', 'id_registrasi_dosen'],
            ],
        ];

        $sync = SinkronisasiFeeder::where('function_name', 'sync_dosen')->first();

        $batch = Bus::batch([])->name($sync->batch_name)->dispatch();

        foreach ($data as $d) {

            $count = $this->count_value($d['count']);

            $limit = 1000;
            $act = $d['act'];
            $order = $d['order'];

            for ($i=0; $i < $count; $i+=$limit) {
                $job = new $d['job']($act, $limit, $i, $order, null, $d['model'], $d['primary']);
                $batch->add($job);
            }

        }

        SinkronisasiFeeder::where('function_name', 'sync_dosen')->update(['batch_id' => $batch->id]);

        LivewireAlert::title('Batch Job')
                    ->text('Batch berhasil Dibuat!')
                    ->success()
                    ->show();

        $this->loadData();
    }

    public function sync_mahasiswa()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1G');

        $data = [
            [
                'act' => 'GetBiodataMahasiswa',
                'limit' => '',
                'offset' => '',
                'order' => '',
                'count' => 'GetCountBiodataMahasiswa',
                'job' => \App\Jobs\Feeder\SyncJob::class,
                'model' => \App\Models\Feeder\Mahasiswa\BiodataMahasiswa::class,
                'primary' => 'id_mahasiswa',
            ],
            [
                'act' => 'GetListRiwayatPendidikanMahasiswa',
                'limit' => '',
                'offset' => '',
                'order' => '',
                'count' => 'GetCountRiwayatPendidikanMahasiswa',
                'job' => \App\Jobs\Feeder\SyncJob::class,
                'model' => \App\Models\Feeder\Mahasiswa\RiwayatPendidikan::class,
                'primary' => 'id_registrasi_mahasiswa',
            ],
            // ['act' => 'GetBiodataMahasiswa', 'count' => 'GetCountBiodataMahasiswa', 'order' => 'id_mahasiswa', 'job' => \App\Jobs\Feeder\SyncJob::class],
            // ['act' => 'GetListRiwayatPendidikanMahasiswa', 'count' => 'GetCountRiwayatPendidikanMahasiswa', 'order' => 'id_registrasi_mahasiswa', 'job' => \App\Jobs\Feeder\SyncJob::class],
            // ['act' => 'GetListMahasiswaLulusDO', 'count' => 'GetCountMahasiswaLulusDO', 'order' => 'id_registrasi_mahasiswa', 'job' => \App\Jobs\Feeder\SyncJob::class]
        ];

        $sync = SinkronisasiFeeder::where('function_name', 'sync_mahasiswa')->first();

        $batch = Bus::batch([])->name($sync->batch_name)->dispatch();

        foreach ($data as $d) {

            $count = $this->count_value($d['count']);

            $limit = 1000;
            $act = $d['act'];
            $order = $d['order'];

            if ($d['act'] == 'GetListMahasiswaLulusDO') {

                // for ($i=0; $i < $count; $i+=$limit) {
                //     $job = new $d['job']($act, $limit, $i, $order, null, \App\Models\Mahasiswa\LulusDo::class, 'id_registrasi_mahasiswa');
                //     $batch->add($job);
                // }

            } else {
                for ($i=0; $i < $count; $i+=$limit) {
                    $job = new $d['job']($act, $limit, $i, $order, null, $d['model'], $d['primary']);
                    $batch->add($job);
                }
            }


        }

        SinkronisasiFeeder::where('function_name', 'sync_mahasiswa')->update(['batch_id' => $batch->id]);

        LivewireAlert::title('Batch Job')
                    ->text('Batch berhasil Dibuat!')
                    ->success()
                    ->show();

        $this->loadData();
    }

    public function create()
    {
        LivewireAlert::title('Create Sinkronisasi')
                ->text('Apakah anda yakin ?')
                ->asConfirm()
                ->onConfirm('store')
                ->show();
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name_create,
            'batch_name' => $this->batch_name_create,
            'function_name' => $this->function_name_create,
        ];

        SinkronisasiFeeder::create($data);

        $this->reset('name_create', 'batch_name_create', 'function_name_create');

        LivewireAlert::title('Sinkronisasi')
                    ->text('Sinkronisasi berhasil disimpan')
                    ->success()
                    ->show();

        $this->loadData();
    }

    public function delete($id)
    {
        LivewireAlert::title('Delete Sinkronisasi')
        ->text('Apakah anda yakin ?')
        ->asConfirm()
        ->onConfirm('deleteSync', ['id' => $id])
        ->show();
    }

    public function deleteSync($data)
    {
        $sinkron = SinkronisasiFeeder::find($data['id']);
        if (!$sinkron) return;

        $sinkron->delete();

        LivewireAlert::title('Sinkronisasi')
                    ->text('Sinkronisasi berhasil dihapus')
                    ->success()
                    ->show();

        $this->loadData();
    }

    public function render()
    {
        return view('livewire.feeder.sinkronisasi.index');
    }
}
