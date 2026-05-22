<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 1. IMPORTAMOS EL TRAIT

// INYECCIÓN: Agregamos 'role', 'created_by' y 'updated_by' 
#[Fillable(['name', 'email', 'password', 'role', 'created_by', 'updated_by'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{

// 2. INYECTAMOS EL TRAIT AQUÍ ADENTRO
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable; 

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
        ];
    }

    /**
     * Licitaciones creadas por este usuario.
     */
    public function tenders()
    {
        return $this->hasMany(Tender::class, 'created_by');
    }

    /**
     * Clientes registrados por este usuario.
     */
    public function clients()
    {
        return $this->hasMany(Client::class, 'created_by');
    }
}
