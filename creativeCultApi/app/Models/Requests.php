<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    use HasFactory;
    protected $table = "requests";

    public function has_stage()
    {
        return $this->hasOne(StageKeys::class, 'id', 'stage');
    }

    public function has_open_for()
    {
        return $this->hasOne(OpenForKeys::class, 'id', 'open_for');
    }

    public function has_requester()
    {
        return $this->hasOne(Client::class, 'client_id', 'requester_id');
    }

    public function has_acceptor()
    {
        return $this->hasOne(Client::class, 'client_id', 'acceptor_id');
    }

}
