<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use File;
use Illuminate\Database\Eloquent\SoftDeletes;

class Educational_quotes extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'educational_quotes';
    protected $fillable = ['quote_line1', 'quote_line2', 'author'];

}
