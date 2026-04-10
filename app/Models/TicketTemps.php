<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketTemps extends Model
{
    use HasFactory;

    protected $table = 'ticket_temps';

    const UPDATED_AT = null;

    protected $fillable = [
        'ticket_id',
        'collaborateur_id',
        'duree',
        'commentaire',
        'date_travail',
    ];

    protected $casts = [
        'date_travail' => 'date',
    ];

    // Relations
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'collaborateur_id');
    }

    // Duree en heures formaté
    public function getDureeFormatteeAttribute(): string
    {
        $heures = intdiv($this->duree, 60);
        $minutes = $this->duree % 60;
        return sprintf('%dh%02d', $heures, $minutes);
    }
}
