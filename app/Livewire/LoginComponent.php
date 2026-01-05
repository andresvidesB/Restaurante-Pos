<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoginComponent extends Component
{
    public $email;
    public $password;

    // Reglas de validación
    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function render()
    {
        // Usamos un layout diferente (sin menú lateral) para el login
        return view('livewire.login-component')->layout('layouts.guest');
    }

    public function login()
{
    $credentials = $this->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (auth()->attempt($credentials)) {
        session()->regenerate();

        // LÓGICA DE REDIRECCIÓN SEGÚN ROL
        $user = auth()->user();

        if ($user->role === 'admin' || $user->role === 'mesero') {
            return redirect()->intended('/dashboard');
        } else {
            // Usuarios normales o clientes van al menú público
            return redirect()->intended('/');
        }
    }

    $this->addError('email', 'Las credenciales no coinciden.');
}
}