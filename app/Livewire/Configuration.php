<?php

namespace App\Livewire;

use App\Models\Configuration as ModelsConfiguration;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Configuration extends Component
{
    #[Validate('required|url')]
    public $url = '';

    #[Validate('required|string')]
    public $password = '';

    #[Validate('required|string')]
    public $username = '';

    public function mount()
    {
        if (session()->has('saved')) {
            LivewireAlert::title(session('saved.title'))
                ->text(session('saved.text'))
                ->success()
                ->show();
        }

        $data = ModelsConfiguration::first();

        if ($data) {
            $this->url = $data->url;
            $this->password = $data->password;
            $this->username = $data->username;
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

        ModelsConfiguration::firstOrCreate(
            [
                'url' => $this->url,
                'password' => $this->password,
                'username' => $this->username,
            ]
        );

        LivewireAlert::title('Success!')
            ->text('Berhasil menyimpan data!')
            ->success()
            ->show();

        
    }

    public function render()
    {
        return view('livewire.configuration');
    }
}
