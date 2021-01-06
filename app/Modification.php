<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Modification extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function lessonSequence() {
        return $this->hasMany('App\LessonSequence');
    }

    /**
     * Map
     */
    public function maplessonmodification() {
        return $this->hasMany('App\FullSequence', 'modification_id');
    }
    
    public static function getModificationsByStandards(Request $request) {
        $modificationsPost = DB::table('full_sequence as fs')
            ->join('modification as mod', 'fs.modification_id', '=', 'mod.id')
            ->select('fs.modification_id', 'mod.name')
            ->distinct()
            ->orderBy('mod.name', 'ASC')
            ->where('fs.bloom_id', '=', $request->bloom_id)
            ->where('fs.webb_id', '=', $request->webb_id)
            ->where('fs.activity_id', '=', $request->activity_id)
            ->where('fs.delivery_id', '=', $request->delivery_id)
            ->where('fs.beyond_id', '=', $request->beyond_id)
            ->get();

            return $modificationsPost;
    
    }
}