<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use File;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'country';

}
