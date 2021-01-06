<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MapUserTarget extends Model
{
    protected $table='map_users_targets';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['id','ulessons_id','type','key_concept_id','targetids'];
}


