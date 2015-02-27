/*************** update_registration ***************/

DELIMITER $$

DROP PROCEDURE IF EXISTS kalnadk_udv_time.update_registration;$$

-- CREATE PROCEDURE kalnadk_udv_time.update_registration(p_user_id INT, p_registration_id INT, p_start_time TIME, p_end_time TIME, p_description VARCHAR(100))
CREATE PROCEDURE kalnadk_udv_time.update_registration(p_user_id INT, p_registration_id INT, p_start_time INT, p_end_time INT, p_description VARCHAR(100))
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
    DECLARE v_description VARCHAR(100);
    DECLARE v_cursor CURSOR FOR SELECT start_date, start_time, end_time, activity_id, description
                                FROM kalnadk_udv_time.registrations 
                                WHERE id = p_registration_id AND user_id = p_user_id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;

    SET debuging = FALSE;

    OPEN v_cursor;
    read_loop_v: LOOP

        FETCH v_cursor INTO v_date, v_start_time, v_end_time, v_activity_id, v_description;

        IF v_done THEN
            LEAVE read_loop_v;
        END IF;

        IF p_start_time IS NOT NULL THEN
            SET l_start_time = SEC_TO_TIME(p_start_time);

            IF l_start_time != MAKETIME(HOUR(v_start_time), MINUTE(v_start_time), 0) AND p_start_time != v_start_time THEN
                UPDATE kalnadk_udv_time.registrations 
                SET start_time = l_start_time
                  , updated_at = now()
                  , updated_by_user_id = p_user_id 
                WHERE id = p_registration_id;

                SET v_start_time = l_start_time;
                SET l_any_change = TRUE;
            END IF;
        END IF;

        IF p_end_time IS NOT NULL AND v_end_time IS NOT NULL THEN
            SET l_end_time = SEC_TO_TIME(p_end_time);

            IF l_end_time != MAKETIME(HOUR(v_end_time), MINUTE(v_end_time), 0) AND p_end_time != v_end_time THEN
                UPDATE kalnadk_udv_time.registrations 
                SET end_time = l_end_time
                  , updated_at = now()
                  , updated_by_user_id = p_user_id 
                WHERE id = p_registration_id;

                SET v_end_time = l_end_time;
                SET l_any_change = TRUE;
            END IF;
        ELSEIF p_end_time IS NOT NULL AND v_end_time IS NULL THEN
            SET l_end_time = SEC_TO_TIME(p_end_time);

            UPDATE kalnadk_udv_time.registrations 
            SET end_time = l_end_time
              , updated_at = now()
              , updated_by_user_id = p_user_id 
            WHERE id = p_registration_id;

            SET v_end_time = l_end_time;
            SET l_any_change = TRUE;
        ELSEIF p_end_time IS NULL AND v_end_time IS NOT NULL THEN
            UPDATE kalnadk_udv_time.registrations 
            SET end_time = NULL
              , updated_at = now()
              , updated_by_user_id = p_user_id 
            WHERE id = p_registration_id;

            SET v_end_time = NULL;
            SET l_any_change = TRUE;
        END IF;

        IF (TRIM(p_description) IS NOT NULL AND TRIM(p_description) != '') AND (TRIM(p_description) != v_description OR v_description IS NULL) THEN
            UPDATE kalnadk_udv_time.registrations 
            SET description = p_description
              , updated_at = now()
              , updated_by_user_id = p_user_id 
            WHERE id = p_registration_id;

            SET v_description = p_description;
        ELSEIF (p_description IS NULL OR TRIM(p_description) = '') AND v_description IS NOT NULL THEN
            UPDATE kalnadk_udv_time.registrations 
            SET description = NULL
              , updated_at = now()
              , updated_by_user_id = p_user_id 
            WHERE id = p_registration_id;

            SET v_description = NULL;
        END IF;
        
        IF l_any_change = FALSE THEN
            SELECT p_registration_id AS id;
            LEAVE read_loop_v;
        END IF;

        block_s: BEGIN
            DECLARE s_done INT DEFAULT FALSE;
            DECLARE s_count INT DEFAULT 0;
            DECLARE s_id INT;
            DECLARE s_start_date DATE;
            DECLARE s_start_time TIME;
            DECLARE s_end_date DATE;
            DECLARE s_end_time TIME;
            DECLARE s_activity_id INT;
            DECLARE s_description VARCHAR(100);
            DECLARE s_cursor CURSOR FOR SELECT id, start_date, start_time, end_time, activity_id, description
                                        FROM kalnadk_udv_time.registrations 
                                        WHERE user_id = p_user_id 
                                          AND start_date = v_date
                                          AND state != 'DELETED'
                                          AND id != p_registration_id 
                                        ORDER BY start_time;
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET s_done = TRUE;

            DROP TEMPORARY TABLE IF EXISTS changed_ids;
            CREATE TEMPORARY TABLE changed_ids (
                id INT
            ) ENGINE = HEAP;
            INSERT INTO changed_ids VALUES (p_registration_id);


            IF debuging THEN
                DROP TEMPORARY TABLE IF EXISTS debug;
                CREATE TEMPORARY TABLE debug (
                    string VARCHAR(4000)
                ) ENGINE = HEAP;
                INSERT INTO debug VALUES ('START');
            END IF;


            OPEN s_cursor;
            read_loop_s: LOOP
            IF debuging THEN
                INSERT INTO debug VALUES ('read_loop_s: LOOP');
            END IF;
                FETCH s_cursor INTO s_id, s_start_date, s_start_time, s_end_time, s_activity_id, s_description;
                IF debuging THEN
                    INSERT INTO debug VALUES ('FETCH s_cursor INTO s_id, s_start_date, s_start_time, s_end_time, s_activity_id, s_description;');
                END IF;

                IF s_done THEN
                    LEAVE read_loop_s;
                END IF;
                IF debuging THEN
                    INSERT INTO debug VALUES (CONCAT_WS(',',s_id, s_start_date, s_start_time, s_end_time, s_activity_id, s_description));
                    INSERT INTO debug VALUES (CONCAT_WS(',',v_date, v_start_time, v_end_time, v_activity_id, v_description));
                END IF;
                IF v_start_time > s_start_time AND v_start_time < s_end_time AND v_end_time IS NULL AND p_action IN (1,2) THEN
                    IF debuging THEN
                        INSERT INTO debug VALUES ('IF v_start_time > s_start_time AND v_start_time < s_end_time AND v_end_time IS NULL AND p_action IN (1,2) THEN');
                    END IF;
                    UPDATE kalnadk_udv_time.registrations 
                    SET end_time = SUBTIME(v_start_time, '00:00:01')
                      , end_date = v_date 
                      , state = 'ENDED'
                      , updated_at = now()
                      , updated_by_user_id = p_user_id 
                    WHERE id = s_id;

                    INSERT INTO changed_ids VALUES (s_id);

                    LEAVE read_loop_s;

                ELSEIF v_start_time > s_start_time AND v_start_time < s_end_time AND v_end_time IS NULL AND p_action = 0 THEN
                    IF debuging THEN
                        INSERT INTO debug VALUES ('ELSEIF v_start_time > s_start_time AND v_start_time < s_end_time AND v_end_time IS NULL AND p_action = 0 THEN');
                    END IF;
                    UPDATE kalnadk_udv_time.registrations 
                    SET start_time = ADDTIME(s_end_time, '00:00:01')
                      , updated_at = now()
                      , updated_by_user_id = p_user_id
                    WHERE id = p_registration_id;


                    LEAVE read_loop_s;

                ELSEIF v_start_time > s_start_time AND v_end_time < s_end_time THEN
                    IF debuging THEN
                        INSERT INTO debug VALUES ('ELSEIF v_start_time > s_start_time AND v_end_time < s_end_time THEN');
                    END IF;
                    INSERT INTO kalnadk_udv_time.registrations 
                               (user_id, activity_id, state, start_date, start_time, end_date, end_time, description, created_at, created_by_user_id)
                        VALUES (p_user_id, s_activity_id, 'ENDED', v_date, v_end_time, v_date, s_end_time, s_description, now(), p_user_id);     

                    UPDATE kalnadk_udv_time.registrations 
                    SET end_time = SUBTIME(v_start_time, '00:00:01')
                      , end_date = v_date 
                      , state = 'ENDED'
                      , updated_at = now()
                      , updated_by_user_id = p_user_id
                    WHERE id = s_id;

                    INSERT INTO changed_ids VALUES (s_id);

                    LEAVE read_loop_s;


                ELSEIF v_start_time <= s_start_time AND v_end_time > s_start_time AND v_end_time < s_end_time AND p_action IN (1,2) THEN
                    IF debuging THEN
                        INSERT INTO debug VALUES ('ELSEIF v_start_time <= s_start_time AND v_end_time > s_start_time AND v_end_time < s_end_time AND p_action IN (1,2) THEN');
                    END IF;
                    UPDATE kalnadk_udv_time.registrations 
                    SET start_time = ADDTIME(v_end_time, '00:00:01')
                      , updated_at = now()
                      , updated_by_user_id = p_user_id
                    WHERE id = s_id;

                    INSERT INTO changed_ids VALUES (s_id);

                    LEAVE read_loop_s;

                ELSEIF v_start_time <= s_start_time AND v_end_time > s_start_time AND v_end_time < s_end_time AND p_action = 0 THEN
                    IF debuging THEN
                        INSERT INTO debug VALUES ('ELSEIF v_start_time <= s_start_time AND v_end_time > s_start_time AND v_end_time < s_end_time AND p_action = 0 THEN');
                    END IF;
                    UPDATE kalnadk_udv_time.registrations 
                    SET end_time = SUBTIME(s_start_time, '00:00:01')
                      , end_date = v_date 
                      , state = 'ENDED'
                      , updated_at = now()
                      , updated_by_user_id = p_user_id
                    WHERE id = p_registration_id;

                    LEAVE read_loop_s;


                ELSEIF v_start_time > s_start_time AND v_start_time < s_end_time AND v_end_time >= s_end_time AND p_action IN (1,2) THEN
                    IF debuging THEN
                        INSERT INTO debug VALUES ('ELSEIF v_start_time > s_start_time AND v_start_time < s_end_time AND v_end_time >= s_end_time AND p_action IN (1,2) THEN');
                    END IF;
                    UPDATE kalnadk_udv_time.registrations 
                    SET end_time = SUBTIME(v_start_time, '00:00:01')
                      , end_date = v_date 
                      , state = 'ENDED'
                      , updated_at = now()
                      , updated_by_user_id = p_user_id
                    WHERE id = s_id;

                    INSERT INTO changed_ids VALUES (s_id);

--                     LEAVE read_loop_s;

                ELSEIF v_start_time > s_start_time AND v_start_time < s_end_time AND v_end_time >= s_end_time AND p_action = 0 THEN
                    IF debuging THEN
                        INSERT INTO debug VALUES ('ELSEIF v_start_time > s_start_time AND v_start_time < s_end_time AND v_end_time >= s_end_time AND p_action = 0 THEN');
                    END IF;
                    UPDATE kalnadk_udv_time.registrations 
                    SET start_time = ADDTIME(s_end_time, '00:00:01')
                      , updated_at = now()
                      , updated_by_user_id = p_user_id
                    WHERE id = p_registration_id;

--                     LEAVE read_loop_s;

                ELSEIF SUBTIME(v_start_time, '00:00:59') < s_start_time AND ADDTIME(v_end_time, '00:00:59') > s_end_time AND p_action = 2 THEN
                    IF debuging THEN
                        INSERT INTO debug VALUES ('ELSEIF SUBTIME(v_start_time, ''00:00:59'') < s_start_time AND ADDTIME(v_end_time, ''00:00:59'') > s_end_time AND p_action = 2 THEN');
                    END IF;
                    UPDATE kalnadk_udv_time.registrations 
                    SET state = 'DELETED'
                      , updated_at = now()
                      , updated_by_user_id = p_user_id
                    WHERE id = s_id;

                    INSERT INTO changed_ids VALUES (s_id);

                ELSEIF SUBTIME(v_start_time, '00:00:59') < s_start_time AND ADDTIME(v_end_time, '00:00:59') > s_end_time AND p_action IN (0,1) THEN
                    IF debuging THEN
                        INSERT INTO debug VALUES ('ELSEIF SUBTIME(v_start_time, ''00:00:59'') < s_start_time AND ADDTIME(v_end_time, ''00:00:59'') > s_end_time AND p_action IN (0,1)');
                    END IF;
                    IF s_count = 0 THEN
                        IF debuging THEN
                            INSERT INTO debug VALUES ('IF s_count = 0 THEN');
                        END IF;
                        UPDATE kalnadk_udv_time.registrations 
                        SET end_time = s_start_time
                          , end_date = v_date 
                          , state = 'ENDED'
                          , updated_at = now()
                          , updated_by_user_id = p_user_id
                        WHERE id = p_registration_id;


                    ELSE
                        IF debuging THEN
                            INSERT INTO debug VALUES ('IF s_count != 0 THEN');
                        END IF;
                        IF last_id_v > 0 THEN
                            UPDATE kalnadk_udv_time.registrations
                            SET end_time = s_start_time
                              , end_date = v_date 
                              , state = 'ENDED'
                              , updated_at = now()
                              , updated_by_user_id = p_user_id
                            WHERE id = v_last_id;
                        END IF;

                        INSERT INTO kalnadk_udv_time.registrations 
                               (user_id, activity_id, state, start_date, start_time, description, created_at, created_by_user_id)
                        VALUES (p_user_id, v_activity_id, 'STARTED', v_date, s_end_time, v_description, now(), p_user_id); 

                        SELECT LAST_INSERT_ID() INTO v_last_id;

                        INSERT INTO changed_ids VALUES (v_last_id);

                    END IF;

                    SET s_count = s_count + 1;

                END IF;

            END LOOP read_loop_s;
            CLOSE s_cursor;

            SELECT * FROM changed_ids;
            IF debuging THEN
                SELECT * FROM debug;
            END IF;


        END block_s;

        IF l_any_change THEN
            IF v_last_id > 0 THEN
                UPDATE kalnadk_udv_time.registrations
                SET end_time = v_end_time
                  , end_date = v_date 
                  , state = 'ENDED'
                  , updated_at = now()
                  , updated_by_user_id = p_user_id
                WHERE id = v_last_id;
            END IF;

--             SELECT * FROM changed_ids;
--             SELECT id, start_date, start_time, end_time, activity_id, description
--             FROM kalnadk_udv_time.registrations 
--             WHERE user_id = p_user_id 
--               AND start_date = v_date
--               AND state != 'DELETED'
--               AND id != p_registration_id 
--             ORDER BY start_time;
        END IF;

    END LOOP;
    CLOSE v_cursor;

END$$
