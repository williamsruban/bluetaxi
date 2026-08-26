<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'rc_book', 'road_tax', 'driving_licence', 'permit', 'drivers_batch'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

