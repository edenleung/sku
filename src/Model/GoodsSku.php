<?php

declare(strict_types=1);

namespace Sku\Model;

use Sku\Model\Model;

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
