CREATE TABLE `android_metadata` (
  `locale` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `manufacturer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cart` (
  `id` varchar(0) DEFAULT NULL,
  `product_id` varchar(0) DEFAULT NULL,
  `quantity` varchar(0) DEFAULT NULL,
  `created_at` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cash_registers` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `register_name` varchar(50) NOT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  UNIQUE KEY `unique_register_name` (`register_name`),
  UNIQUE KEY `id` (`id`,`register_name`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `customer_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `sale_date` datetime DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `customer_sales_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` varchar(50) NOT NULL,
  `price` varchar(200) DEFAULT NULL,
  `is_kg` tinyint(1) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `customer_transactions` (
  `id` varchar(0) DEFAULT NULL,
  `customer_id` varchar(0) DEFAULT NULL,
  `transaction_type` varchar(0) DEFAULT NULL,
  `amount` varchar(0) DEFAULT NULL,
  `transaction_date` varchar(0) DEFAULT NULL,
  `description` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `customer_name` varchar(255) DEFAULT NULL,
  `total_debt` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `parent_groups` (
  `id` varchar(0) DEFAULT NULL,
  `group_name` varchar(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `product_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `parent_group_id` int(11) DEFAULT NULL,
  `show_on_sales_page` tinyint(4) DEFAULT NULL,
  `price` decimal(2,1) DEFAULT NULL,
  `show_on_sale_page` tinyint(4) DEFAULT NULL,
  UNIQUE KEY `id` (`id`,`group_name`,`show_on_sales_page`,`price`,`show_on_sale_page`),
  UNIQUE KEY `id_2` (`id`,`group_name`,`parent_group_id`,`show_on_sales_page`,`price`,`show_on_sale_page`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `products` (
  `id` bigint(20) DEFAULT NULL,
  `product_name` varchar(29) DEFAULT NULL,
  `barcode` bigint(20) DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `profit_margin` decimal(4,1) DEFAULT NULL,
  `product_group` varchar(1) DEFAULT NULL,
  `unit` varchar(4) DEFAULT NULL,
  `image_path` varchar(512) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `hizli_urun` tinyint(4) DEFAULT NULL,
  `is_kg` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `register_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `sale_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `sale_code` varchar(255) NOT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

CREATE TABLE `sales_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `sale_code` varchar(255) NOT NULL,
  `is_kg` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

CREATE TABLE `sales_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `sale_type` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_sale_id` int(11) DEFAULT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

CREATE TABLE `sqlite_sequence` (
  `name` varchar(14) DEFAULT NULL,
  `seq` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` tinyint(4) NOT NULL,
  `username` varchar(17) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `color` varchar(7) DEFAULT '#FFFFFF',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`username`,`password`),
  UNIQUE KEY `id_2` (`id`,`username`,`password`,`color`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

