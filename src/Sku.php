<?php

declare(strict_types=1);

namespace xiaodi\Sku;

use Exception;
use xiaodi\Sku\Model\Model;

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
        foreach ($attrs as $attr) {
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

    public function add(array $data = [])
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

    public function update(array $data = [])
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

        foreach ($this->attrs as $attr) {
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

    // 所有sku规格类目与其值的从属关系，比如商品有颜色和尺码两大类规格，颜色下面又有红色和蓝色两个规格值。
    // 可以理解为一个商品可以有多个规格类目，一个规格类目下可以有多个规格值。
    public function tree()
    {
        $tree = [];
        $skus = $this->sku_g->where('goods_id', $this->goods)->select();

        foreach ($skus as $sku) {
            $attrvs = $sku->attr_value_ids;
            $sku_avs = $this->sku_av->whereIn('id', $attrvs)->select();
            foreach ($sku_avs as $sku_av) {
                $attr = $this->sku_a->where('id', $sku_av->attr_id)->find();
                $tree[$sku_av->attr_id]['k'] = $attr->name;
                $tree[$sku_av->attr_id]['k_s'] = "s{$attr->id}";
                $tree[$sku_av->attr_id]['v'][] = [
                    'id' => $sku_av->id,
                    'name' => $sku_av->name,
                    'imgUrl' => '',
                    'previewImgUrl' => ''
                ];
            }
        }

        $tree = array_values($tree);

        return $tree;
    }

    // 所有 sku 的组合列表，比如红色、M 码为一个 sku 组合，红色、S 码为另一个组合
    public function list()
    {
        $list = [];
        $skus = $this->sku_g->where('goods_id', $this->goods)->select();

        foreach ($skus as $sku) {

            $temp = [
                "id" => $sku->id,
                "price" => $sku->price,
                "stock_num" => $sku->stock,
            ];

            $attrvs = $sku->attr_value_ids;
            $sku_avs = $this->sku_av->whereIn('id', $attrvs)->select();

            foreach ($sku_avs as $sku_av) {
                $attr = $this->sku_a->where('id', $sku_av->attr_id)->find();
                $temp["s{$attr->id}"] = $sku_av->id;
            }

            $list[] = $temp;
        }

        return $list;
    }

    public function vant()
    {
        return [
            'tree' => $this->tree(),
            'lisst' => $this->list()
        ];
    }
}
