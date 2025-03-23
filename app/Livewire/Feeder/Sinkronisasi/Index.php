<?php

namespace App\Livewire\Feeder\Sinkronisasi;

use App\Services\Feeder\FeederAPI;
use Illuminate\Bus\BatchRepository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{

    public string $currentAction = '';

    public $batchId;
    public $progress = 0;
    public $completed = false;

    public function mount()
    {
        // Cari batch yang belum selesai langsung dari database
        $unfinishedBatch = DB::table('job_batches')
            ->whereNull('finished_at') // Batch yang belum selesai
            ->orderBy('created_at', 'desc') // Ambil batch terbaru
            ->first();

        if ($unfinishedBatch) {
            $this->batchId = $unfinishedBatch->id;
            $this->getBatchProgress();
        }
    }

    public function getBatchProgress()
    {
        if (!$this->batchId) return;

        $batch = app(BatchRepository::class)->find($this->batchId);

        if ($batch) {
            $this->progress = $batch->progress();
            $this->completed = $batch->finished();
        }
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

        $batch = Bus::batch([])->name('sync_mahasiswa')->dispatch();

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

        LivewireAlert::title('Batch Job')
                    ->text('Batch berhasil Dibuat!')
                    ->success()
                    ->show();
    }

    public function render()
    {
        return view('livewire.feeder.sinkronisasi.index');
    }
}
