<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Password;

class ForgotPassword extends Component
{
    public $email;
    public $status; // Para mostrar mensaje de "Correo enviado"

    protected $rules = ['email' => 'required|email'];

    public function sendResetLink()
    {
        $this->validate();

        // Envía el enlace usando el sistema de notificaciones de Laravel
        $response = Password::sendResetLink(['email' => $this->email]);

        if ($response == Password::RESET_LINK_SENT) {
            $this->status = '¡Enlace enviado! Revisa tu correo.';
            $this->email = '';
        } else {
            $this->addError('email', trans($response));
        }
    }

    public function render()
    {
        return view('livewire.forgot-password')->layout('layouts.guest');
    }
}