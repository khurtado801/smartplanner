<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use File;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cms extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

}
