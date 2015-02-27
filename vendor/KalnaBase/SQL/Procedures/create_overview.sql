/*************** update_registration ***************/

DELIMITER $$

DROP PROCEDURE IF EXISTS kalnadk_udv_time.create_overview;$$

CREATE PROCEDURE kalnadk_udv_time.create_overview(p_user_id INT, p_start_date DATE, p_end_date DATE)
BEGIN

    DECLARE debuging INT;

    DECLARE p_action INT DEFAULT 2;

    DECLARE l_start_time TIME;
    DECLARE l_end_time TIME;
    DECLARE l_description VARCHAR(100);
    DECLARE l_any_change INT DEFAULT FALSE;

    DECLARE v_done INT DEFAULT FALSE;
    DECLARE v_last_id INT DEFAULT 0;
    DECLARE v_date DATE;
    DECLARE v_start_time TIME;
    DECLARE v_end_time TIME;

    DECLARE v_activity_id INT;
    DECLARE v_code VARCHAR(100);
    DECLARE v_description VARCHAR(100);
    DECLARE c_activities CURSOR FOR SELECT reg.activity_id, act.code, act.description
                                    FROM kalnadk_udv_time.registrations reg
                                       , kalnadk_udv_time.activities act
                                    WHERE reg.activity_id = act.id
                                    AND   reg.user_id = p_user_id
                                    AND   reg.start_date BETWEEN p_start_date AND p_end_date
                                    GROUP BY reg.activity_id, act.code, act.description
                                    ORDER BY act.code;
    DECLARE c_summeries CURSOR FOR  SELECT summery_type, start_date, activity_id, summery_seconds
                                    FROM kalnadk_udv_time.vregistration_summeries
                                    WHERE summery_type = 'activity'
                                    AND   user_id = p_user_id
                                    AND   start_date BETWEEN p_start_date AND p_end_date
                                    AND   activity_id = v_activity_id
                                    UNION
                                    SELECT summery_type, start_date, NULL, summery_seconds
                                    FROM kalnadk_udv_time.vregistration_summeries
                                    WHERE summery_type = 'day'
                                    AND   user_id = p_user_id
                                    AND   start_date BETWEEN p_start_date AND p_end_date
                                    ORDER BY start_date;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;

    SET debuging = FALSE;

    OPEN c_activities;
    read_loop_v: LOOP

        FETCH c_activities INTO v_activity_id, v_code, v_description;

        IF v_done THEN
            LEAVE read_loop_v;
        END IF;

        

    END LOOP;
    CLOSE c_activities;

END$$
