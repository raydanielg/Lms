<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Traits\ReviewTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//use App\Http\Controllers\Api\Traits\ReviewTrait;

class BundleReviewController extends Controller
{
    use ReviewTrait;

    public function store()
    {
        return $this->store() ;
    }
}
