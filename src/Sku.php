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

    public function __construct(Model $sku_g, Model $sku_a)
    {
        $this->sku_g = $sku_g;
        $this->sku_a = $sku_a;
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
        if (is_numeric($attr)) {
            $this->attrs[] = $attr;
        } else {
            $this->attrs[] = $attr->id;
        }

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
            'stock_num' => $this->stock,
            'price' => $this->price,
            'sku_attrs' => $this->attrs
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
            $this->stock && $row->stock_num = $this->stock;
            $this->attrs && $row->sku_attrs = $this->attrs;
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

    public function get($goods = null)
    {
        $goods && $this->goods = $goods;
        $skus = $this->sku_g->where('goods_id', $this->goods)->select();

        return $skus;
    }

    public function getSku()
    {
        return $this->sku_g->where('goods_id', $this->goods)->select();
    }

    public function getAttrs()
    {
        $sku_attrs = $this->getSku()->column('sku_attrs');
        $children = $this->getUnique($sku_attrs);
        $parent = $this->getParentAttr($children);
        $ids = array_merge($children, $parent);
        $attrs = $this->sku_a->whereIn('id', $ids)->field('id, name, pid')->order('pid asc')->select()->toArray();

        $tree = $this->formatTree($attrs);
        return $tree;
    }

    public function formatTree($data, $pid = 0)
    {
        $tree = [];

        foreach ($data as $item) {
            if ($item['pid'] == $pid) {

                if ($pid == 0) {
                    $temp = [
                        'k' => $item['name'],
                        'k_s' => "s{$item['id']}"
                    ];
                } else {
                    $temp = [
                        'id' => $item['id'],
                        'name' => $item['name']
                    ];
                }

                $children = $this->formatTree($data, $item['id']);

                if (!empty($children)){
                    $temp['v'] = $children;
                }

                $tree[] = $temp;
            }
        }

        return $tree;
    }

    private function getUnique($arrs)
    {
        $arr_unique = [];

        foreach ($arrs as $arr) {
            $arr_unique = array_merge($arr, $arr_unique);
        }


        return array_unique($arr_unique);
    }

    public function getParentAttr($attrs)
    {
        $pids = $this->sku_a->whereIn('id', $attrs)->column('pid');

        return array_unique($pids);
    }

    // 所有sku规格类目与其值的从属关系，比如商品有颜色和尺码两大类规格，颜色下面又有红色和蓝色两个规格值。
    // 可以理解为一个商品可以有多个规格类目，一个规格类目下可以有多个规格值。
    public function tree()
    {
        $tree = $this->getAttrs();
        return $tree;
    }

    // 所有 sku 的组合列表，比如红色、M 码为一个 sku 组合，红色、S 码为另一个组合
    public function list()
    {
        $list = [];
        $skus = $this->getSku();

        foreach ($skus as $sku) {
            $temp = [
                "id" => $sku->id,
                "price" => $sku->price,
                "stock_num" => $sku->stock_num,
            ];

            $sku_as = $this->sku_a->alias('a')->join([
                '(select * from sku_attr)' => 'b'
            ], 'a.pid = b.id')->whereIn('a.id', $sku->sku_attrs)->field('a.*, b.name as parent_name, b.id  as parent_id')->select();

            foreach ($sku_as as $sku_a) {
                $temp["s{$sku_a->parent_id}"] = $sku_a->id;
            }

            $list[] = $temp;
        }

        return $list;
    }

    public function vant()
    {
        return [
            'tree' => $this->tree(),
            'list' => $this->list()
        ];
    }
}
