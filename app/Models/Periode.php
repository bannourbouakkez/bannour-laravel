<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $guarded = ['PeriodeID','created_at','updated_at'];
    protected $primaryKey = 'PeriodeID';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dates()
    {
        return $this->hasMany(Date::class,'periode_id');
    }

    



    /*
    public static function boot() {
        parent::boot();
        static::deleting(function($periode) {
             $periode->dates()->delete();
        });
    }*/
    /*
    protected static function boot() 
    {
        parent::boot();
        self::deleting(function (Periode $periode) {
            foreach ($periode->dates() as $date) $date->delete();
        });
    }
    */


}
