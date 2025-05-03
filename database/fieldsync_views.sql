-- Script pour créer des vues utiles dans la base de données
-- Ces vues facilitent les requêtes complexes fréquemment utilisées

USE `fieldsync`;

-- Vue pour obtenir les visites à venir avec leurs participants
CREATE OR REPLACE VIEW `upcoming_visits_view` AS
SELECT 
    v.id AS visit_id,
    v.title,
    v.description,
    v.location,
    v.date,
    v.start_time,
    v.end_time,
    v.status AS visit_status,
    v.created_by,
    v.team_id,
    u.id AS user_id,
    u.name AS user_name,
    u.email AS user_email,
    u.phone AS user_phone,
    vp.status AS participant_status
FROM 
    visits v
JOIN 
    visit_participants vp ON v.id = vp.visit_id
JOIN 
    users u ON vp.user_id = u.id
WHERE 
    v.date >= CURDATE()
ORDER BY 
    v.date ASC, v.start_time ASC;

-- Vue pour obtenir les disponibilités des utilisateurs par jour
CREATE OR REPLACE VIEW `user_availabilities_view` AS
SELECT 
    a.id AS availability_id,
    a.user_id,
    u.name AS user_name,
    u.email AS user_email,
    a.date,
    a.start_time,
    a.end_time,
    a.status
FROM 
    availabilities a
JOIN 
    users u ON a.user_id = u.id
ORDER BY 
    a.date ASC, a.start_time ASC;

-- Vue pour obtenir les statistiques des visites par utilisateur
CREATE OR REPLACE VIEW `user_visit_stats_view` AS
SELECT 
    u.id AS user_id,
    u.name AS user_name,
    COUNT(DISTINCT vp.visit_id) AS total_visits,
    SUM(CASE WHEN v.status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_visits,
    SUM(CASE WHEN v.status = 'pending' THEN 1 ELSE 0 END) AS pending_visits,
    SUM(CASE WHEN v.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_visits,
    SUM(CASE WHEN v.date < CURDATE() THEN 1 ELSE 0 END) AS past_visits,
    SUM(CASE WHEN v.date >= CURDATE() THEN 1 ELSE 0 END) AS upcoming_visits
FROM 
    users u
LEFT JOIN 
    visit_participants vp ON u.id = vp.user_id
LEFT JOIN 
    visits v ON vp.visit_id = v.id
GROUP BY 
    u.id, u.name;

-- Vue pour obtenir les statistiques des notifications par utilisateur
CREATE OR REPLACE VIEW `user_notification_stats_view` AS
SELECT 
    u.id AS user_id,
    u.name AS user_name,
    COUNT(n.id) AS total_notifications,
    SUM(CASE WHEN n.is_read = 0 THEN 1 ELSE 0 END) AS unread_notifications,
    COUNT(s.id) AS total_sms,
    SUM(CASE WHEN s.status = 'sent' THEN 1 ELSE 0 END) AS sent_sms,
    SUM(CASE WHEN s.status = 'failed' THEN 1 ELSE 0 END) AS failed_sms
FROM 
    users u
LEFT JOIN 
    notifications n ON u.id = n.user_id
LEFT JOIN 
    sms_notifications s ON u.id = s.user_id
GROUP BY 
    u.id, u.name;

-- Vue pour obtenir les équipes avec le nombre de membres
CREATE OR REPLACE VIEW `teams_with_members_view` AS
SELECT 
    t.id AS team_id,
    t.name AS team_name,
    t.description,
    u.name AS created_by_name,
    COUNT(tm.id) AS member_count
FROM 
    teams t
JOIN 
    users u ON t.created_by = u.id
LEFT JOIN 
    team_members tm ON t.id = tm.team_id
GROUP BY 
    t.id, t.name, t.description, u.name;
