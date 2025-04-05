<?php

namespace App\Http\Controllers\Admin\traits;

use App\Models\ProductBadgeContent;

trait ProductBadgeTrait
{
    public function handleProductBadges($item, $data)
    {
        ProductBadgeContent::query()->where('targetable_id', $item->id)
            ->where('targetable_type', $item->getMorphClass())
            ->delete();

        if (!empty($data['product_badges']) and count($data['product_badges']) > 0) {
            foreach ($data['product_badges'] as $badgeId) {
                ProductBadgeContent::query()->updateOrCreate([
                    'product_badge_id' => $badgeId,
                    'targetable_id' => $item->id,
                    'targetable_type' => $item->getMorphClass(),
                ], []);
            }
        }
    }
}
