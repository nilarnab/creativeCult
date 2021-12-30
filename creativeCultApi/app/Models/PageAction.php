<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageAction extends Model
{
    use HasFactory;
    protected $table = 'page_actionid';

    public function page_action()
    {
        return $this->hasOne(Permission::class, 'action_id', 'action_id');
    }
}
