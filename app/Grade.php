<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use File;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * Get the user that owns the phone.
     */
    public function lesson() {
        return $this->hasMany('App\Lesson');
    }

    public function businesses() {
        return $this->belongsToMany('App\Subject', 'Subjects');
    }

}
