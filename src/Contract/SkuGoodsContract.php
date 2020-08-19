<?php

declare(strict_types=1);

namespace xiaodi\Sku\Contract;

interface SkuGoodsContract
{
    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function get($id);

    /**
     * 获取商品所有sku
     *
     * @param [type] $goods
     * @return Array
     */
    public function sku($goods): Array;

    /**
     * 获取商品所有sku下的属性值
     *
     * @param [type] $goods
     * @return Array
     */
    public function attrs($goods): Array;
}
