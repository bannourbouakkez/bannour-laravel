<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientsession extends Model
{
    use HasFactory;

    protected $guarded = ['SessionID','created_at','updated_at'];
    protected $primaryKey = 'SessionID';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
