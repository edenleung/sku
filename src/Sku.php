<?php

declare(strict_types=1);

namespace xiaodi\Sku;

use Exception;

use xiaodi\Sku\Contract\SkuAttrContract;
use xiaodi\Sku\Contract\SkuGoodsContract;

class Sku
{
    protected $goods;

    protected $attrs;

    protected $price;

    protected $stock;

    protected $sku_g;

    protected $sku_a;

    protected $row;

    protected $data = [];

    public function __construct(SkuGoodsContract $sku_g, SkuAttrContract $sku_a)
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
        $this->attrs[] = $attr;

        return $this;
    }

    public function getAttr()
    {
        return $this->attrs;
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
        $this->data = [
            'goods_id' => $this->goods,
            'sku_attrs' => $this->stock,
            'price' => $this->price,
            'sku_attrs' => implode(',', $this->attrs)
        ];

        return $this->save();
    }

    protected function save()
    {
        if ($this->attrs) {
            $temp = [];
            foreach ($this->attrs as $attr) {
                $attr = $this->sku_a->get($attr);
                $temp[] = $attr->id . ':' . $attr->pid;
            }

            $this->data['sku_attrs'] = implode(',', $temp);
        }

        $this->data['price'] = $this->data['price'] * 100;
        return $this->row ? $this->sku_g->update($this->row, $this->data) : $this->sku_g->create($this->data);
    }

    public function update($id = null, array $data = [])
    {
        $id && $this->row = $id;
        $row = $this->sku_g->get($this->row);

        if ($row) {
            $this->price && $this->data['price'] = $this->price;
            $this->stock && $this->data['stock_num'] = $this->stock;
            $this->attrs && $this->data['sku_attrs'] = implode(',', $this->attrs);
            return $this->save();
        } else {
            throw new Exception('sku not found');
        }
    }

    public function delete($value = null)
    {
        $value && $this->row = $value;

        return $this->sku_g->delete($this->row);
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

    public function getSku()
    {
        return $this->sku_g->sku($this->goods);
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

                if (!empty($children)) {
                    $temp['v'] = $children;
                }

                $tree[] = $temp;
            }
        }

        return $tree;
    }

    public function getAttrValue()
    {
        return $this->sku_g->attrs($this->goods);
    }

    // 所有sku规格类目与其值的从属关系，比如商品有颜色和尺码两大类规格，颜色下面又有红色和蓝色两个规格值。
    // 可以理解为一个商品可以有多个规格类目，一个规格类目下可以有多个规格值。
    public function tree()
    {
        $attrs = $this->getAttrValue();

        $ids = [];
        foreach ($attrs as $attr) {
            $item = explode(',', $attr);
            foreach ($item as $v) {
                $ids = array_merge($ids, explode(':', $v));
            }
        }

        $ids = array_unique($ids);
        $attrs = $this->sku_a->getData($ids);

        $tree = $this->formatTree($attrs);

        return $tree;
    }

    // 所有 sku 的组合列表，比如红色、M 码为一个 sku 组合，红色、S 码为另一个组合
    public function list()
    {
        $list = [];
        $skus = $this->getSku();

        foreach ($skus as $sku) {
            $temp = [
                "id" => $sku['id'],
                "price" => $sku['price'],
                "stock_num" => $sku['stock_num'],
            ];

            $attrs = explode(',', $sku['sku_attrs']);

            foreach ($attrs as $attr) {
                list($parent_id, $children_id) = explode(':', $attr);
                $temp["s{$parent_id}"] = $children_id;

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
