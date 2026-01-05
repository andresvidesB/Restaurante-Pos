<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UsuariosComponent extends Component
{
    use WithPagination;

    public $name, $email, $password, $role = 'mesero'; // Por defecto mesero
    public $user_id;
    public $isModalOpen = false;

    // Reglas de validación
    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role' => 'required|in:admin,mesero,cocina',
    ];

    public function render()
{
    $users = User::paginate(10);
    return view('livewire.usuarios-component', compact('users'))
        ->layout('layouts.app'); // <--- Especifica esta ruta
}

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'mesero';
        $this->user_id = null;
    }

    public function store()
    {
        // Validación personalizada para ignorar el email propio al editar
        $rules = $this->rules;
        if($this->user_id){
            $rules['email'] = 'required|email|unique:users,email,' . $this->user_id;
            $rules['password'] = 'nullable|min:6'; // Password opcional al editar
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        // Solo actualizamos password si se escribió algo
        if (!empty($this->password)) {
            $data['password'] = bcrypt($this->password);
        }

        User::updateOrCreate(['id' => $this->user_id], $data);

        session()->flash('message', $this->user_id ? 'Usuario actualizado.' : 'Usuario creado.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = ''; // No mostramos la contraseña actual
        $this->openModal();
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'Usuario eliminado.');
    }
}