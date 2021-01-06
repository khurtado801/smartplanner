<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LessonsModification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['id', 'lesson_id', 'user_id', 'content'];

}


