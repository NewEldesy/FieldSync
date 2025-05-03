-- Table pour stocker les préférences SMS des utilisateurs
CREATE TABLE IF NOT EXISTS `user_sms_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `visit_reminder` tinyint(1) NOT NULL DEFAULT 1,
  `visit_confirmation` tinyint(1) NOT NULL DEFAULT 1,
  `visit_cancellation` tinyint(1) NOT NULL DEFAULT 1,
  `daily_summary` tinyint(1) NOT NULL DEFAULT 1,
  `team_updates` tinyint(1) NOT NULL DEFAULT 0,
  `reminder_time` int(11) NOT NULL DEFAULT 60,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table pour stocker l'historique des SMS envoyés
CREATE TABLE IF NOT EXISTS `sms_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `type` varchar(50) NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
