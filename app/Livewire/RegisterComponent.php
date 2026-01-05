<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterComponent extends Component
{
    public $name, $email, $password, $password_confirmation;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed', // 'confirmed' busca password_confirmation
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'cliente', // Importante: Rol por defecto
        ]);

        // Iniciar sesión automáticamente
        Auth::login($user);

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.register-component')->layout('layouts.guest');
    }
}