DELIMITER $$

DROP PROCEDURE IF EXISTS kalnadk_udv_time.stop_all_registrations;$$

CREATE DEFINER=`kalnadk_klndb`@`93.167.60.181` PROCEDURE kalnadk_udv_time.stop_all_registrations(p_user_id INT, p_date DATE, p_time TIME)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_start_date DATE;
    DECLARE v_start_time TIME;
    DECLARE v_act_id INT;
    DECLARE v_reg_id INT;
    DECLARE v_date DATE;
    DECLARE v_dummy INT;
    DECLARE cur CURSOR FOR  SELECT start_date, start_time, activity_id, id 
                            FROM kalnadk_udv_time.registrations 
                            WHERE user_id = p_user_id 
                            AND end_date IS NULL 
                            AND end_time IS NULL;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    IF p_date IS NOT NULL THEN
        SET v_date = p_date;
    ELSE
        SET v_date = CURDATE();
    END IF;
    
    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO v_start_date, v_start_time, v_act_id, v_reg_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        CASE WHEN v_start_date = v_date THEN
                UPDATE kalnadk_udv_time.registrations
                SET end_date = v_date
                  , end_time = p_time
                  , state = 'ENDED'
                  , updated_at = now()
                  , updated_by_user_id = p_user_id
                WHERE id = v_reg_id;
             WHEN v_start_date < v_date AND DATE_SUB(v_date, INTERVAL 16 HOUR) > CAST(CONCAT(v_start_date,' ',v_start_time) AS DATETIME) THEN
                UPDATE kalnadk_udv_time.registrations 
                SET end_date = v_start_date
                  , end_time = v_start_time
                  , state = 'ENDED'
                  , updated_at = now()
                  , updated_by_user_id = p_user_id
                WHERE id = v_reg_id;
             WHEN v_start_date < v_date THEN
                UPDATE kalnadk_udv_time.registrations 
                SET end_date = v_start_date
                  , end_time = '23:59:59'
                  , state = 'ENDED'
                  , updated_at = now()
                  , updated_by_user_id = p_user_id
                WHERE id = v_reg_id;

                INSERT INTO kalnadk_udv_time.registrations (user_id, start_date, start_time, end_date, end_time, activity_id, state, created_at, created_by_user_id) 
                VALUES (p_user_id, v_date, '00:00:00', v_date, p_time, v_act_id, 'STARTED', NOW(), p_user_id);
             ELSE
                SET v_dummy = 1;
        END CASE;
    END LOOP;
    
    CLOSE cur;
END