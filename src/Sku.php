<?php

declare(strict_types=1);

namespace Sku;

use Exception;
use Sku\Model\Model;

class Sku
{
    protected $goods;

    protected $attrs;

    protected $price;

    protected $stock;

    protected $sku_g;

    protected $sku_a;

    protected $sku_av;

    public function __construct(Model $sku_g, Model $sku_a, Model $sku_av)
    {
        $this->sku_g = $sku_g;
        $this->sku_a = $sku_a;
        $this->sku_av = $sku_av;
    }

    public function goods($goods)
    {
        $this->goods = $goods;

        return $this;
    }

    public function attrs(...$attrs)
    {
        foreach($attrs as $attr) {
            $this->attr($attr);
        }

        return $this;
    }

    public function attr($attr)
    {
        $this->attrs[] = $attr;
        return $this;
    }

    public function price($value)
    {
        $this->price = $value * 100;
        return $this;
    }

    public function stock($value)
    {
        $this->stock = $value;
        return $this;
    }

    public function add()
    {
        return $this->sku_g->save([
            'goods_id' => $this->goods,
            'stock' => $this->stock,
            'price' => $this->price,
            'attr_value_ids' => $this->formatAttrValueIds()
        ]);

    }

    protected function save()
    {
    }

    public function update()
    {
        $row = $this->sku_g->where([
            'id' => $this->row,
            'goods_id' => $this->goods
        ])->find();

        if ($row) {
            $this->price && $row->price = $this->price;
            $this->stock && $row->stock = $this->stock;
            $this->attrs && $row->attr_value_ids = $this->formatAttrValueIds();
            $row->save();
        } else {
            throw new Exception('sku not found');
        }

    }

    public function delete($value = null)
    {
        $value && $this->row = $value;
        $row = $this->sku_g->where([
            'id' => $this->row
        ])->find();

        return $row->delete();
    }

    public function getRow()
    {
        return $this->row;
    }

    public function row($value)
    {
        $this->row = $value;
        return $this;
    }

    protected function formatAttrValueIds()
    {
        $ids = [];

        foreach($this->attrs as $attr) {
            $ids[] = $attr->id;
        }

        return implode(',', $ids);
    }

    public function get($goods = null)
    {
        $goods && $this->goods = $goods;
        $skus = $this->sku_g->where('goods_id', $this->goods)->select();
        
        return $skus;
    }
}
