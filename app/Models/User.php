<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'role',
        'state',
        'company',
        'adresse',
        'code_postal',
        'ville',
        'pays',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Accesseur pour le nom complet
    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->surname}";
    }

    // Vérifications de rôle
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCollaborateur(): bool
    {
        return $this->role === 'collaborateur';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    // Relations
    // Relations pour les clients
    public function projetsClient()
    {
        return $this->belongsToMany(Projet::class, 'projet_client', 'client_id', 'projet_id');
    }

    public function contratsClient()
    {
        return $this->belongsToMany(Contrat::class, 'contrat_client', 'client_id', 'contrat_id');
    }

    // Relations pour les collaborateurs
    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'projet_collaborateur', 'collaborateur_id', 'projet_id');
    }

    public function ticketsCollaborateur()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_collaborateur', 'collaborateur_id', 'ticket_id');
    }

    public function commentaires()
    {
        return $this->hasMany(TicketCommentaire::class, 'auteur_id');
    }

    public function tempsTickets()
    {
        return $this->hasMany(TicketTemps::class, 'collaborateur_id');
    }
}
