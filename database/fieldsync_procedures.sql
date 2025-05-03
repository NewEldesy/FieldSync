-- Script pour créer des procédures stockées utiles
-- Ces procédures facilitent les opérations courantes sur la base de données

DELIMITER //

-- Procédure pour obtenir les visites d'un utilisateur dans une période donnée
CREATE PROCEDURE IF NOT EXISTS `get_user_visits_in_period`(
    IN user_id INT,
    IN start_date DATE,
    IN end_date DATE
)
BEGIN
    SELECT 
        v.id, v.title, v.description, v.location, v.date, 
        v.start_time, v.end_time, v.status, v.created_at
    FROM 
        visits v
    JOIN 
        visit_participants vp ON v.id = vp.visit_id
    WHERE 
        vp.user_id = user_id
        AND v.date BETWEEN start_date AND end_date
    ORDER BY 
        v.date ASC, v.start_time ASC;
END //

-- Procédure pour obtenir les disponibilités communes entre plusieurs utilisateurs
CREATE PROCEDURE IF NOT EXISTS `get_common_availabilities`(
    IN user_ids VARCHAR(255),
    IN target_date DATE
)
BEGIN
    SET @sql = CONCAT('
        SELECT 
            a1.start_time, a1.end_time
        FROM 
            availabilities a1
        WHERE 
            a1.date = ''', target_date, '''
            AND a1.user_id IN (', user_ids, ')
            AND a1.status = ''available''
        GROUP BY 
            a1.start_time, a1.end_time
        HAVING 
            COUNT(DISTINCT a1.user_id) = (
                SELECT COUNT(DISTINCT user_id) 
                FROM availabilities 
                WHERE date = ''', target_date, '''
                AND user_id IN (', user_ids, ')
            )
        ORDER BY 
            a1.start_time ASC
    ');
    
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

-- Procédure pour créer une visite avec des participants
CREATE PROCEDURE IF NOT EXISTS `create_visit_with_participants`(
    IN p_title VARCHAR(255),
    IN p_description TEXT,
    IN p_location VARCHAR(255),
    IN p_date DATE,
    IN p_start_time TIME,
    IN p_end_time TIME,
    IN p_status VARCHAR(20),
    IN p_created_by INT,
    IN p_team_id INT,
    IN p_participants VARCHAR(255)
)
BEGIN
    DECLARE visit_id INT;
    
    -- Insérer la visite
    INSERT INTO visits (
        title, description, location, date, start_time, end_time, 
        status, created_by, team_id, created_at
    ) VALUES (
        p_title, p_description, p_location, p_date, p_start_time, p_end_time, 
        p_status, p_created_by, p_team_id, NOW()
    );
    
    SET visit_id = LAST_INSERT_ID();
    
    -- Insérer les participants
    SET @sql = CONCAT('
        INSERT INTO visit_participants (visit_id, user_id, status, created_at)
        SELECT 
            ', visit_id, ', id, ''pending'', NOW()
        FROM 
            users
        WHERE 
            id IN (', p_participants, ')
    ');
    
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    -- Retourner l'ID de la visite créée
    SELECT visit_id AS new_visit_id;
END //

-- Procédure pour marquer toutes les notifications d'un utilisateur comme lues
CREATE PROCEDURE IF NOT EXISTS `mark_all_notifications_as_read`(
    IN p_user_id INT
)
BEGIN
    UPDATE notifications
    SET is_read = 1
    WHERE user_id = p_user_id AND is_read = 0;
    
    SELECT ROW_COUNT() AS notifications_updated;
END //

-- Procédure pour obtenir les statistiques d'un utilisateur
CREATE PROCEDURE IF NOT EXISTS `get_user_statistics`(
    IN p_user_id INT
)
BEGIN
    -- Statistiques des visites
    SELECT 
        COUNT(*) AS total_visits,
        SUM(CASE WHEN v.status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_visits,
        SUM(CASE WHEN v.status = 'pending' THEN 1 ELSE 0 END) AS pending_visits,
        SUM(CASE WHEN v.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_visits,
        SUM(CASE WHEN v.date < CURDATE() THEN 1 ELSE 0 END) AS past_visits,
        SUM(CASE WHEN v.date >= CURDATE() THEN 1 ELSE 0 END) AS upcoming_visits
    FROM 
        visit_participants vp
    JOIN 
        visits v ON vp.visit_id = v.id
    WHERE 
        vp.user_id = p_user_id;
    
    -- Statistiques des notifications
    SELECT 
        COUNT(*) AS total_notifications,
        SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS unread_notifications
    FROM 
        notifications
    WHERE 
        user_id = p_user_id;
    
    -- Statistiques des SMS
    SELECT 
        COUNT(*) AS total_sms,
        SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) AS sent_sms,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_sms,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) AS failed_sms
    FROM 
        sms_notifications
    WHERE 
        user_id = p_user_id;
    
    -- Statistiques des disponibilités
    SELECT 
        COUNT(*) AS total_availabilities,
        SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) AS available_days,
        SUM(CASE WHEN status = 'busy' THEN 1 ELSE 0 END) AS busy_days,
        SUM(CASE WHEN status = 'away' THEN 1 ELSE 0 END) AS away_days
    FROM 
        availabilities
    WHERE 
        user_id = p_user_id;
END //

DELIMITER ;
