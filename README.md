# Sku

## 安装

```bash
$ composer require xiaodi/sku:dev-master
```

## 使用

```php
use xiaodi\Sku\Service\SkuAttr;
use xiaodi\Sku\Service\SkuGoods;

use app\model\SkuAttr;
use app\model\SkuGoods;

$sku_a = new SkuAttr(new \app\model\SkuAttr);
$sku_a->create('内存');
$sku_a->create('存储');

$sku_a->create('4g', [
	'pid' => 1
]);

$sku_a->create('128G', [
	'pid' => 2
]);

$sku_a->create('8g', [
	'pid' => 1
]);

$sku_a->create('256G', [
	'pid' => 2
]);

$sku_g = new SkuAttr(new \app\model\SkuGoods);

$sku = new Sku($sku_g, $sku_a);

// add
$mem = $sku_a->name('4g');
$storage = $sku_a->name('128G');
$sku->goods(1)->attrs($mem, $storage)->stock(1000)->price(1999)->add();

// update
$mem = $sku_a->name('8g');
$storage = $sku_a->name('256G');
$sku->goods(1)->attrs($mem, $storage)->stock(2000)->price(2999)->update(1);

// delete
$sku->delete(1);

// vant
$data = $sku->goods(1)->vant();
dump($data);
```

## Tables
```sql
CREATE TABLE `sku_goods` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`goods_id` INT(11) NOT NULL,
	`sku_attrs` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`price` INT(11) NOT NULL DEFAULT '0',
	`stock_num` INT(11) NOT NULL DEFAULT '0',
	`create_time` INT(11) NOT NULL DEFAULT '0',
	`update_time` INT(11) NOT NULL DEFAULT '0',
	`delete_time` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB;
```

```sql
CREATE TABLE `sku_attr` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`pid` INT(11) NULL DEFAULT '0',
	`create_time` INT(11) NOT NULL DEFAULT '0',
	`update_time` INT(11) NOT NULL DEFAULT '0',
	`delete_time` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `name_pid` (`name`, `pid`)
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
;
```
