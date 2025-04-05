<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\DB;

trait CascadeDeletes
{

    public function delete()
    {
        DB::beginTransaction();

        if ($this->morphsFunctions) {

            foreach ($this->morphsFunctions as $morph) {
                $this->{$morph}()->delete();
            }
        }

        $result = parent::delete();

        if (!$result)
            throw new \RuntimeException('Model deletion failed');

        DB::commit();

        return $result;
    }
}
