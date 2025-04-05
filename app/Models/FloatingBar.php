<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Http\Request;

class FloatingBar extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'floating_bars';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title', 'description', 'btn_text'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }

    public function getBtnTextAttribute()
    {
        return getTranslateAttributeValue($this, 'btn_text');
    }

    public static function getFloatingBar(Request $request)
    {
        $testPreview = !empty($request->get('preview_floating_bar'));

        $time = time();

        $query = FloatingBar::query();

        $query->where(function ($query) use ($time) {
            $query->whereNull('start_at');
            $query->orWhere('start_at', '<', $time);
        });

        $query->where(function ($query) use ($time) {
            $query->whereNull('end_at');
            $query->orWhere('end_at', '>', $time);
        });

        if (!$testPreview) {
            $query->where('enable', true);
        }

        return $query->first();
    }
}
