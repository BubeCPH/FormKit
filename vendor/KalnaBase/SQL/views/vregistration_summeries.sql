DROP VIEW vregistration_summeries;

CREATE 
VIEW vregistration_summeries AS
    SELECT vas.user_id AS user_id,
            vas.activity_id AS activity_id,
            vas.code AS code,
            vas.description AS description,
            vas.start_date AS date,
            vas.summery_seconds AS seconds,
            'activity' AS type
    FROM vactivities_summeries vas 
    UNION ALL 
    SELECT vas.user_id AS user_id,
            NULL AS activity_id,
            NULL AS code,
            NULL AS description,
            vas.start_date AS date,
            SUM(vas.summery_seconds) AS seconds,
            'day' AS type
    FROM vactivities_summeries vas
    GROUP BY vas.user_id , vas.start_date 
    UNION ALL 
    SELECT vas.user_id AS user_id,
            vas.activity_id AS activity_id,
            vas.code AS code,
            vas.description AS description,
            NULL AS date,
            SUM(vas.summery_seconds) AS seconds,
            'activitytotal' AS type
    FROM vactivities_summeries vas
    GROUP BY vas.user_id , vas.activity_id
    UNION ALL 
    SELECT reg.user_id AS user_id,
            NULL AS activity_id,
            NULL AS code,
            NULL AS description,
            reg.start_date AS date,
            MIN(time_to_sec(reg.start_time)) AS seconds,
            'starttime' AS type
    FROM registrations reg
    GROUP BY start_date
    UNION ALL 
    SELECT reg.user_id AS user_id,
            NULL AS activity_id,
            NULL AS code,
            NULL AS description,
            reg.start_date AS date,
            MAX(time_to_sec(reg.end_time)) AS seconds,
            'endtime' AS type
    FROM registrations reg
    GROUP BY date
    ORDER BY date, type, activity_id, code, user_id