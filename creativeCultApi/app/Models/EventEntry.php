<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventEntry extends Model
{
    use HasFactory;
    protected $table = 'event_entries';

    public function has_participant()
    {
        return $this->hasOne(Client::class, 'client_id', 'user_id');
    }

    public function has_event()
    {
        return $this->hasOne(EventDetail::class, 'id', 'event_id');
    }



}
