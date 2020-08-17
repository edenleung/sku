<?php

declare(strict_types=1);

namespace xiaodi\Sku\Model;

use xiaodi\Sku\Model\Model;

class SkuGoods extends Model
{
    public function getSkuAttrsAttr($value)
    {
        if ($value) {
          return explode(',', $value);
        }

        return [];
    }
}
