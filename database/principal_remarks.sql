-- SQL script to create the principal_remarks table
-- Recommended for phpMyAdmin / Manual Upload

CREATE TABLE IF NOT EXISTS `principal_remarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) unsigned DEFAULT NULL,
  `percentage_min` decimal(5,2) NOT NULL DEFAULT '0.00',
  `percentage_max` decimal(5,2) NOT NULL DEFAULT '100.00',
  `remark` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `principal_remarks_tenant_id_index` (`tenant_id`),
  KEY `principal_remarks_is_active_sort_order_tenant_id_index` (`is_active`,`sort_order`,`tenant_id`),
  KEY `principal_remarks_percentage_min_percentage_max_index` (`percentage_min`,`percentage_max`),
  CONSTRAINT `principal_remarks_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
