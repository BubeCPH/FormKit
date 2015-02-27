/*************** stop ***************/

DELIMITER $$

DROP PROCEDURE IF EXISTS kalnadk_udv_time.stop_registration;$$

CREATE PROCEDURE kalnadk_udv_time.stop_registration(p_user_id INT, p_reg_id INT, p_date DATE, p_time TIME)
BEGIN
    DECLARE v_start_date DATE;
    DECLARE v_act_id INT;
    DECLARE v_uid INT;
    DECLARE v_date DATE;

    IF p_date IS NOT NULL THEN
        SET v_date = p_date;
    ELSE
        SET v_date = CURDATE();
    END IF;

    SELECT start_date, activity_id, user_id INTO v_start_date, v_act_id, v_uid 
    FROM kalnadk_udv_time.registrations 
    WHERE id = p_reg_id AND user_id = p_user_id;

    IF v_start_date = v_date THEN
        UPDATE kalnadk_udv_time.registrations
        SET end_date = v_date
          , end_time = p_time
          , state = 'ENDED'
          , updated_at = now()
          , updated_by_user_id = p_user_id
        WHERE id = p_reg_id;
    ELSE
        UPDATE kalnadk_udv_time.registrations 
        SET end_date = v_start_date
          , end_time = '23:59:59'
          , state = 'ENDED'
          , updated_at = now()
          , updated_by_user_id = p_user_id
        WHERE id = p_reg_id;

        INSERT INTO kalnadk_udv_time.registrations (user_id, start_date, start_time, end_date, end_time, activity_id, state, created_at, created_by_user_id) 
        VALUES (p_user_id, v_date, '00:00:00', v_date, p_time, v_act_id, 'STARTED', NOW(), p_user_id);
    END IF;
END$$