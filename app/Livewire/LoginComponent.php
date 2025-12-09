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
        $this->validate();

        // Intenta iniciar sesión con las credenciales
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            
            // Si es correcto:
            session()->regenerate(); // Seguridad contra ataques de sesión
            return redirect()->intended(route('dashboard')); // Redirigir al panel
        }

        // Si falla:
        $this->addError('email', 'Estas credenciales no coinciden con nuestros registros.');
    }
}