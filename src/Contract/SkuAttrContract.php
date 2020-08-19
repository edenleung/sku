<?php

declare(strict_types=1);

namespace xiaodi\Sku\Contract;

interface SkuAttrContract
{
    public function create($name, array $data = []);

    public function get($id);

    public function name($id);

    public function getData(array $ids): Array;

    /**
     * 获取商品所有sku属性值的父级
     *
     * @param [type] $children
     * @return Array
     */
    public function getParent(array $children): Array;
}
