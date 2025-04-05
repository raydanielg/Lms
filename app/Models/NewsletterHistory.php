<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterHistory extends Model
{
    protected $table = 'newsletters_history';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

}
