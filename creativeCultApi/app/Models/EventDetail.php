<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDetail extends Model
{
    use HasFactory;
    protected $table = "event_details";

    public function has_entry()
    {
        return $this->hasMany(EventEntry::class, 'event_id', 'id');
    }

}
