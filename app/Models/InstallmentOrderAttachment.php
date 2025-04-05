<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class InstallmentOrderAttachment extends Model
{
    protected $table = 'installment_order_attachments';
    public $timestamps = false;
    protected $guarded = ['id'];


}
