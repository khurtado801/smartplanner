<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MapLessonUser extends Model
{
    protected $table='map_lessons_users';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['id','ulessons_id','keyconcept_id','learningtarget_id','id','name'];
}


