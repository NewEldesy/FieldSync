-- Script pour ajouter des index supplémentaires pour optimiser les performances
-- À exécuter après la création initiale de la base de données

USE `fieldsync`;

-- Index pour optimiser les recherches de visites par date et statut
CREATE INDEX IF NOT EXISTS `visits_date_status_idx` ON `visits` (`date`, `status`);

-- Index pour optimiser les recherches de disponibilités par date et statut
CREATE INDEX IF NOT EXISTS `availabilities_date_status_idx` ON `availabilities` (`date`, `status`);

-- Index pour optimiser les recherches de notifications non lues
CREATE INDEX IF NOT EXISTS `notifications_unread_idx` ON `notifications` (`user_id`, `is_read`);

-- Index pour optimiser les recherches de SMS par statut et type
CREATE INDEX IF NOT EXISTS `sms_status_type_idx` ON `sms_notifications` (`status`, `type`);

-- Index pour optimiser les recherches de participants par statut
CREATE INDEX IF NOT EXISTS `visit_participants_status_idx` ON `visit_participants` (`status`);

-- Index pour optimiser les recherches d'utilisateurs par rôle
CREATE INDEX IF NOT EXISTS `users_role_idx` ON `users` (`role`);

-- Index pour optimiser les recherches de journaux d'activité par date
CREATE INDEX IF NOT EXISTS `activity_logs_date_idx` ON `activity_logs` (`created_at`);
