<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    use HasFactory;
    
    protected $guarded = ['DateID','created_at','updated_at'];
    protected $primaryKey = 'DateID';

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class,'date_id');
    }


}
