<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'new_email', 'old_email', 'username',
        'first_name', 'last_name', 'contact_number', 'address', 'home_location'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
