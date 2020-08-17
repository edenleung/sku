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
