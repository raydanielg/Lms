<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class UserFirebaseSessions extends Model
{
    protected $table = "user_firebase_sessions";
    protected $fillable = ["user_id", "fcm_token", "token", "ip"];
}
