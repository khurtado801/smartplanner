<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BloomWebActivity extends Model
{
    //
    protected $table = 'blooms_webbs_actvities';
    
    public function activity() {
        return $this->belongsTo(Activity::class);
    }
}
