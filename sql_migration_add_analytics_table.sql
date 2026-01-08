-- Migration Script: Add form_analytics table for tracking form views, fills, and submissions
-- This table tracks analytics data for forms

CREATE TABLE IF NOT EXISTS `form_analytics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `store_client_id` int(11) NOT NULL,
  `event_type` enum('view','fill','submit') NOT NULL DEFAULT 'view',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_form_id` (`form_id`),
  KEY `idx_store_client_id` (`store_client_id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_form_store` (`form_id`, `store_client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

