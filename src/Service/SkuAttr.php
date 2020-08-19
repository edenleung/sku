<?php

declare(strict_types=1);

namespace xiaodi\Sku\Service;

use think\Model;
use xiaodi\Sku\Contract\SkuAttrContract;

class SkuAttr implements SkuAttrContract
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function get($id)
    {
        return $this->model->where('id', $id)->find();
    }

    public function name($name)
    {
        return $this->model->where('name', $name)->find();
    }

    public function create($name, array $data = [])
    {
        $data['name'] = $name;
        return $this->model->create($data);
    }

    public function getParent(array $children): array
    {
        $pids = $this->model->whereIn('id', $children)->column('pid');

        $parents = $this->model->whereIn('id', $pids)->select()->toArray();

        return $parents;
    }

    public function getData(array $ids): array
    {
        return $this->model->whereIn('id', $ids)->field('id, name, pid')->order('pid asc')->select()->toArray();
    }
}
