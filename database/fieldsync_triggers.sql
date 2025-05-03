-- Script pour créer des déclencheurs (triggers) dans la base de données
-- Ces déclencheurs automatisent certaines actions lors des modifications de données

DELIMITER //

-- Trigger pour enregistrer les activités lors de la création d'une visite
CREATE TRIGGER IF NOT EXISTS `after_visit_insert` 
AFTER INSERT ON `visits` 
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, created_at)
    VALUES (NEW.created_by, 'create', 'visit', NEW.id, 
            CONCAT('{"title":"', NEW.title, '","date":"', NEW.date, '"}'), 
            NOW());
END //

-- Trigger pour enregistrer les activités lors de la modification d'une visite
CREATE TRIGGER IF NOT EXISTS `after_visit_update` 
AFTER UPDATE ON `visits` 
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, created_at)
    VALUES (NEW.created_by, 'update', 'visit', NEW.id, 
            CONCAT('{"title":"', NEW.title, '","date":"', NEW.date, '","status":"', NEW.status, '"}'), 
            NOW());
END //

-- Trigger pour enregistrer les activités lors de la suppression d'une visite
CREATE TRIGGER IF NOT EXISTS `before_visit_delete` 
BEFORE DELETE ON `visits` 
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, created_at)
    VALUES (OLD.created_by, 'delete', 'visit', OLD.id, 
            CONCAT('{"title":"', OLD.title, '","date":"', OLD.date, '"}'), 
            NOW());
END //

-- Trigger pour créer une notification lorsqu'un utilisateur est ajouté à une visite
CREATE TRIGGER IF NOT EXISTS `after_visit_participant_insert` 
AFTER INSERT ON `visit_participants` 
FOR EACH ROW
BEGIN
    DECLARE v_title VARCHAR(255);
    DECLARE v_date DATE;
    DECLARE v_creator_id INT;
    
    -- Récupérer les informations de la visite
    SELECT title, date, created_by INTO v_title, v_date, v_creator_id
    FROM visits WHERE id = NEW.visit_id;
    
    -- Créer une notification pour le participant
    INSERT INTO notifications (user_id, title, message, type, related_id, created_at)
    VALUES (NEW.user_id, 'Nouvelle invitation à une visite', 
            CONCAT('Vous avez été invité à participer à la visite "', v_title, '" le ', v_date), 
            'visit_invitation', NEW.visit_id, NOW());
END //

-- Trigger pour créer une notification lorsqu'une disponibilité est modifiée
CREATE TRIGGER IF NOT EXISTS `after_availability_update` 
AFTER UPDATE ON `availabilities` 
FOR EACH ROW
BEGIN
    -- Si le statut a changé, créer des notifications pour les membres de l'équipe
    IF NEW.status != OLD.status THEN
        -- Cette partie nécessiterait une procédure stockée plus complexe pour notifier les membres de l'équipe
        -- Pour simplifier, nous enregistrons juste l'activité
        INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, created_at)
        VALUES (NEW.user_id, 'update', 'availability', NEW.id, 
                CONCAT('{"date":"', NEW.date, '","status":"', NEW.status, '"}'), 
                NOW());
    END IF;
END //

-- Trigger pour mettre à jour la date de modification lors de la mise à jour d'un utilisateur
CREATE TRIGGER IF NOT EXISTS `before_user_update` 
BEFORE UPDATE ON `users` 
FOR EACH ROW
BEGIN
    SET NEW.updated_at = NOW();
END //

-- Trigger pour créer des notifications SMS lorsqu'une visite est confirmée
CREATE TRIGGER IF NOT EXISTS `after_visit_status_update` 
AFTER UPDATE ON `visits` 
FOR EACH ROW
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE participant_id INT;
    DECLARE participant_phone VARCHAR(20);
    DECLARE cur CURSOR FOR 
        SELECT vp.user_id, u.phone
        FROM visit_participants vp
        JOIN users u ON vp.user_id = u.id
        WHERE vp.visit_id = NEW.id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Si le statut a changé à 'confirmed' ou 'cancelled'
    IF NEW.status != OLD.status AND (NEW.status = 'confirmed' OR NEW.status = 'cancelled') THEN
        OPEN cur;
        
        read_loop: LOOP
            FETCH cur INTO participant_id, participant_phone;
            IF done THEN
                LEAVE read_loop;
            END IF;
            
            -- Vérifier si l'utilisateur a activé les notifications SMS pour ce type d'événement
            IF EXISTS (
                SELECT 1 FROM user_sms_preferences 
                WHERE user_id = participant_id 
                AND active = 1 
                AND (
                    (NEW.status = 'confirmed' AND visit_confirmation = 1) OR 
                    (NEW.status = 'cancelled' AND visit_cancellation = 1)
                )
            ) THEN
                -- Créer une notification SMS
                INSERT INTO sms_notifications (
                    user_id, phone, message, status, type, related_id, created_at
                ) VALUES (
                    participant_id,
                    participant_phone,
                    CASE 
                        WHEN NEW.status = 'confirmed' THEN 
                            CONCAT('FieldSync: La visite "', NEW.title, '" du ', NEW.date, ' à ', NEW.start_time, ' a été confirmée.')
                        ELSE 
                            CONCAT('FieldSync: La visite "', NEW.title, '" du ', NEW.date, ' a été annulée.')
                    END,
                    'pending',
                    CASE 
                        WHEN NEW.status = 'confirmed' THEN 'visit_confirmation'
                        ELSE 'visit_cancellation'
                    END,
                    NEW.id,
                    NOW()
                );
            END IF;
        END LOOP;
        
        CLOSE cur;
    END IF;
END //

DELIMITER ;
