<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Index extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';

    public function mount()
    {
        if (session()->has('saved')) {
            LivewireAlert::title(session('saved.title'))
                ->text(session('saved.text'))
                ->success()
                ->show();
        }
    }

    public function confirmUserDeletion($id)
    {
        LivewireAlert::title('Delete User')
            ->text('Are you sure you want to delete this user?')
            ->asConfirm()
            ->onConfirm('delete', ['id' => $id])
            ->show();
    }

    public function delete($data)
    {
        $user = User::find($data['id']);

        // dd($user);
        if (!$user) {
            LivewireAlert::title('Error')
                ->text('User not found')
                ->error()
                ->show();
            return;
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            // Throw an error or handle the case where there is only one admin left
            LivewireAlert::title('Error')
                ->text('You need at least 1 admin in the system')
                ->error()
                ->show();
            return;
        }

        $user->delete();

        LivewireAlert::title("User Deleted")
                ->text('User has been deleted')
                ->success()
                ->show();
    }

    public function render()
    {
        $page = [
            10,
            25,
            50,
            100,
        ];
        return view('livewire.users.index', [
            'users' => User::search($this->search)->paginate($this->perPage),
            'page' => $page,
        ]);
    }
}
