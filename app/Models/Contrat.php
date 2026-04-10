<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contrat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'heures_totales',
        'heures_consommees',
        'taux_horaire',
        'montant_total',
        'conditions',
        'statut',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'heures_totales' => 'decimal:2',
        'heures_consommees' => 'decimal:2',
        'taux_horaire' => 'decimal:2',
        'montant_total' => 'decimal:2',
    ];

    // Relations
    public function clients()
    {
        return $this->belongsToMany(User::class, 'contrat_client', 'contrat_id', 'client_id');
    }

    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'contrat_projet', 'contrat_id', 'projet_id');
    }
}
