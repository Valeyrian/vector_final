<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketCommentaire extends Model
{
    use HasFactory;

    protected $table = 'ticket_commentaires';

    const UPDATED_AT = null;

    protected $fillable = [
        'ticket_id',
        'auteur_id',
        'contenu',
    ];

    // Relations
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }
}
