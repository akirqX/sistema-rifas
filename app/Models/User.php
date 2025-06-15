<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'phone',
        'cpf',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'cpf', // Esconde o CPF por padrão ao converter o modelo para array/JSON
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get all of the orders for the User.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Retorna o telefone do usuário com máscara de privacidade.
     */
    public function getMaskedPhone(): ?string
    {
        if (!$this->phone) {
            return null;
        }
        // Remove tudo que não for número
        $cleaned = preg_replace('/[^0-9]/', '', $this->phone);

        // Formato (XX) XXXXX-XXXX
        if (strlen($cleaned) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) *****-$3', $cleaned);
        }
        // Formato (XX) XXXX-XXXX
        if (strlen($cleaned) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) ****-$3', $cleaned);
        }

        return $this->phone; // Retorna original se não bater com os formatos
    }

    /**
     * Retorna o CPF do usuário com máscara de privacidade.
     */
    public function getMaskedCpf(): ?string
    {
        if (!$this->cpf) {
            return null;
        }
        // Remove tudo que não for número
        $cleaned = preg_replace('/[^0-9]/', '', $this->cpf);

        if (strlen($cleaned) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '***.$2.$3-**', $cleaned);
        }

        return $this->cpf; // Retorna original se não bater com o formato
    }
}
