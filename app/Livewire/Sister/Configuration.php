<?php

namespace App\Livewire\Sister;

use App\Models\Sister\SisterConfig;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Configuration extends Component
{
    #[Validate('required|url')]
    public $url = '';

    #[Validate('required|string')]
    public $password = '';

    #[Validate('required|string')]
    public $username = '';

    #[Validate('required|string')]
    public $id_pengguna = '';

    public function mount()
    {
        $data = SisterConfig::first();

        if ($data) {
            $this->url = $data->url;
            $this->password = $data->password;
            $this->username = $data->username;
            $this->id_pengguna = $data->id_pengguna;
        }
    }


    public function storePrompt()
    {
        LivewireAlert::title('Are you sure?')
                ->text('Apakah anda yakin ingin menyimpan data?')
                ->confirmButtonText('Yes')
                ->withConfirmButton()
                ->asConfirm()
                ->onConfirm('store')
                ->show();
    }

    public function store()
    {
        $this->validate();

        SisterConfig::updateOrCreate([
            'id' => 1
        ], [
            'url' => $this->url,
            'password' => $this->password,
            'username' => $this->username,
            'id_pengguna' => $this->id_pengguna,
        ]);

        LivewireAlert::title('Success!')
            ->text('Berhasil menyimpan data!')
            ->success()
            ->show();


    }

    public function render()
    {
        return view('livewire.sister.configuration');
    }
}
