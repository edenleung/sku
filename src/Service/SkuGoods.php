<?php

declare(strict_types=1);

namespace xiaodi\Sku\Service;

use think\Model;
use xiaodi\Sku\Contract\SkuGoodsContract;

class SkuGoods implements SkuGoodsContract
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function get($id)
    {
        return $this->model->where('id', $id)->find($id);
    }

    public function create(array $data)
    {
        return $this->model->save($data);
    }

    public function update($id, array $data)
    {
        return $this->get($id)->save($data);
    }

    public function delete($id)
    {
        return $this->get($id)->delete();
    }

    public function sku($goods): Array
    {
        return $this->model->where('goods_id', $goods)->select()->toArray();
    }

    public function attrs($goods): Array
    {
        $skus = $this->sku($goods);
        $sku_attrs = array_column($skus, 'sku_attrs');

        return array_unique($sku_attrs);
    }
}
