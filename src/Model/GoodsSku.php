<?php

declare(strict_types=1);

namespace xiaodi\Sku\Model;

use xiaodi\Sku\Model\Model;

class GoodsSku extends Model
{
    public function getAttrValueIdsAttr($value)
    {
        if ($value) {
          return explode(',', $value);
        }

        return [];
    }
}
