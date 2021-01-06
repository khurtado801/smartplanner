<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bloom extends Model {
    //
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    // protected $table = 'blooms';
    // protected $fillable=['name', 'description'];

    /**
     * Get Webb from Bloom
     */
    public function lessonSequence() {
        return $this->hasMany('App\LessonSequence');
    }

}
