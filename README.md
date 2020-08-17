# Sku

## 安装

```bash
$ composer require xiaodi/sku:dev-master
```

## 使用

```php


SkuAttr::create([
    'name' => '内存',
]);

SkuAttr::create([
    'name' => '存储',
]);

SkuAttr::create([
    'name' => '4g',
    'pid' => 1
]);

SkuAttr::create([
    'name' => '256G',
    'pid' => 2
]);

$sku_g = new SkuGoods;
$sku_a = new SkuAttr();
$sku = new Sku($sku_g, $sku_a);

// add
$mem = SkuAttr::getByName('4g');
$storage = SkuAttr::getByName('256G');
$sku->goods(1)->attrs($mem, $storage)->stock(1000)->price(1999)->add();

// update
$mem = SkuAttr::getByName('8g');
$storage = SkuAttr::getByName('512G');
$sku->goods(1)->attrs($mem, $storage)->stock(2000)->price(2999)->update(1);

// delete
$sku->delete(1);
```
