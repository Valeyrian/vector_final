<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'projet_id',
        'titre',
        'description',
        'statut',
        'priorite',
        'type',
        'temps_estime',
        'temps_passe',
        'validation_status',
        'approuve_client',
        'motif_refus',
    ];

    // Relations
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    public function collaborateurs()
    {
        return $this->belongsToMany(User::class, 'ticket_collaborateur', 'ticket_id', 'collaborateur_id');
    }

    public function commentaires()
    {
        return $this->hasMany(TicketCommentaire::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    public function tempsEnregistres()
    {
        return $this->hasMany(TicketTemps::class, 'ticket_id');
    }

    public function getTotalTempsAttribute(): int
    {
        return $this->tempsEnregistres->sum('duree');
    }

    public function getPrioriteColorAttribute(): string
    {
        return match ($this->priorite) {
            'urgente' => '#e74c3c',
            'haute' => '#e67e22',
            'moyenne' => '#3498db',
            'basse' => '#2ecc71',
            default => '#95a5a6',
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'nouveau' => '#3498db',
            'en_cours' => '#e67e22',
            'en_attente_client' => '#f1c40f',
            'termine' => '#2ecc71',
            'a_valider' => '#8e44ad',
            'valide' => '#16a085',
            'refuse' => '#c0392b',
            default => '#95a5a6',
        };
    }
}
