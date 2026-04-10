<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Projet extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'statut',
        'heures_consommees',
        'date_debut',
        'date_fin_prevue',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
    ];

    // Relations
    public function clients()
    {
        return $this->belongsToMany(User::class, 'projet_client', 'projet_id', 'client_id');
    }

    public function collaborateurs()
    {
        return $this->belongsToMany(User::class, 'projet_collaborateur', 'projet_id', 'collaborateur_id');
    }

    public function contrats()
    {
        return $this->belongsToMany(Contrat::class, 'contrat_projet', 'projet_id', 'contrat_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'projet_id');
    }
}
