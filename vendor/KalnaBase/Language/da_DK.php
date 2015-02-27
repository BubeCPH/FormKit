<?php

//------------------
//Language: Danish
//------------------
$language_strings = array(
    'UTIL' => array(
        'DATE_FORMAT' => array(
            'SHORT' => '#{dd}-#{mm}-#{yyyy}',
            'LONG' => '#{d}. #{month} #{yyyy}',
            'SHORTDAY' => '#{dn}',
            'LONGDAY' => '#{dayname}'
        ),
        'DAYS' => array(
            'SHORT' => array(
                'SAT' => 'L&oslash;r',
                'SUN' => 'S&oslash;n',
                'MON' => 'Man',
                'TUE' => 'Tirs',
                'WED' => 'Ons',
                'THU' => 'Tors',
                'FRI' => 'Fre'
            ),
            'LONG' => array(
                'SATURDAY' => 'L&oslash;rdag',
                'SUNDAY' => 'S&oslash;ndag',
                'MONDAY' => 'Mandag',
                'TUESDAY' => 'Tirsdag',
                'WEDNESDAY' => 'Onsdag',
                'THURSDAY' => 'Torsdag',
                'FRIDAY' => 'Fredag'
            )
        ),
        'MONTHS' => array(
            'SHORT' => array(
                'JAN' => 'Jan',
                'FEB' => 'Feb',
                'MAR' => 'Mar',
                'APR' => 'Apr',
                'MAY' => 'Maj',
                'JUN' => 'Jun',
                'JUL' => 'Jul',
                'AUG' => 'Aug',
                'SEP' => 'Sep',
                'OCT' => 'Okt',
                'NOV' => 'Nov',
                'DEC' => 'Dec'
            ),
            'LONG' => array(
                'JANUARY' => 'Januar',
                'FEBRUARY' => 'Februar',
                'MARCH' => 'Marts',
                'APRIL' => 'April',
                'MAY' => 'Maj',
                'JUNE' => 'Juni',
                'JULY' => 'Juli',
                'AUGUST' => 'August',
                'SEPTEMBER' => 'September',
                'OCTOBER' => 'Oktober',
                'NOVEMBER' => 'November',
                'DECEMBER' => 'December'
            )
        )
    ),
    'GLOBAL' => array(
        'PAGE_TITLE' => 'Kalna TimeKeeper',
        'HEADER_TITLE' => 'My website header title',
        'SITE_NAME' => 'Kalna TimeKeeper',
        'SLOGAN' => 'Når tiden er vigtig',
        'HEADING' => 'Heading'
    ),
    'LOGIN' => array(
        'HEADER' => 'Log ind',
        'USERNAME' => 'E-mailadresse',
        'PASSWORD' => 'Adgangskode',
        'SIGN_IN' => 'Log ind',
        'SIGNED_IN_AS' => 'Logget ind som',
        'SIGN_OUT' => 'Log ud',
        'HELP' => 'Brug for hjælp?',
        'FORGOT' => 'Glemt din adgangskode?',
        'NEW_ACCOUNT' => 'Ny bruger, registrer dig her'
    ),
    'MENU' => array(
        'TIME' => array(
            'REGISTRATIONS' => 'Registreringer',
            'ACTIVITIES' => 'Aktiviteter',
            'OVERVIEW' => array(
                'HEADING' => 'Oversigt',
                'WEEK' => 'Ugentlig',
                'MONTH' => 'Månedlig'
            ),
            'SETTINGS' => 'Indstillinger'
        )
    ),
    'PAGE' => array(
        'TIME' => array(
            'REGISTRATIONS' => array(
                'ACTIVITY' => array(
                    'HEADING' => 'Foretrukne aktiviteter',
                    'START' => 'Start',
                    'STOP' => 'Stop',
                    'ADD' => 'Tilføj'
                ),
                'REGISTRATION' => array(
                    'HEADING' => 'Registreringer',
                    'TABLEHEADING' => array(
                        'START_TIME' => 'Start',
                        'END_TIME' => 'Slut',
                        'ELAPSED_TIME' => 'Tids-<br>forbrug',
                        'ACTIVITY_CODE' => 'Aktivitets-<br>kode',
                        'DESCRIPTION' => 'Beskrivelse'
                    ),
                    'TOOLTIP' => array(
                        'DELETE' => 'Slet registrering',
                        'CONFIRM_DELETE' => 'Bekræft sletning',
                        'SAVE_CHANGES' => 'Gem ændringer',
                        'UNDO' => 'Fortryd'
                    ),
                ),
                'SUMMERY' => array(
                    'HEADING' => 'Dagsoversigt / opsummering',
                    'TABLEHEADING' => array(
                        'ACTIVITY_CODE' => 'Aktivitets-<br>kode',
                        'DESCRIPTION' => 'Aktivitets-<br>beskrivelse',
                        'ELAPSED_TIME' => 'Tids-<br>forbrug',
                        'ELAPSED_TIME_DEC' => 'Tidsforbrug<br>(hundrededele)'
                    ),
                    'TOTAL' => 'Total'
                )
            ),
            'ACTIVITIES' => array(
                'FAVORITE_ACTIVITIES' => array(
                    'HEADING' => 'Activiteter der vises ved registrering'
                ),
                'ACTIVITIES' => array(
                    'HEADING' => 'Activiteter',
                    'TABLEHEADING' => array(
                        'CODE' => 'Kode',
                        'DESCRIPTION' => 'Beskrivelse',
                        'DISPLAY' => 'Vis ved<br>registrering',
                        'SORT' => 'Sortér',
                        'ACTIONS' => ''
                    ),
                    'FAVORITE' => array(
                        'YES' => 'Vises',
                        'NO' => 'Skjules'
                    ),
                    'TOOLTIP' => array(
                        'FAVORITE_YES' => 'Vises, klik for at skjule',
                        'FAVORITE_NO' => 'Skjules, klik for at vise',
                        'MOVE' => 'Flyt op eller ned',
                        'DELETE' => 'Slet registrering',
                        'CONFIRM_DELETE' => 'Bekræft sletning',
                        'SAVE_CHANGES' => 'Gem ændringer',
                        'ADD_ACTIVITY' => 'Tilføj aktivitet',
                        'ADD_SUB_ACTIVITY' => 'Tilknyt del-aktivitet',
                        'UNDO' => 'Fortryd'
                    ),
                )
            ),
            'OVERVIEW' => array(
                'WEEK' => 'Uge',
                'YEAR' => 'År',
                'DATE_PERIOD' => array(
                    'FROM' => 'Fra',
                    'TO' => 'Til',
                    'PREVIOUS' => 'forrige',
                    'NEXT' => 'næste'
                ),
                'TABLE' => array(
                    'HEADING' => array(
                        'DATE_FORMAT' => '#{d}.<br>#{mon}',
                        'TOOLTIP' => 'Gå til dagens registreringer',
                        'HEADING' => 'Oversigt over periodens registreringer'
                    ),
                    'ROWHEADINGS' => array(
                        'STARTING_TIME' => 'Starttid',
                        'ENDING_TIME' => 'Sluttid',
                        'DAY_SUMMERY' => 'Tidsforbrug'
                    ),
                    'TOOLTIP' => array(
                        'FAVORITE_YES' => 'Vises, klik for at skjule',
                        'FAVORITE_NO' => 'Skjules, klik for at vise',
                        'MOVE' => 'Flyt op eller ned',
                        'DELETE' => 'Slet registrering',
                        'CONFIRM_DELETE' => 'Bekræft sletning',
                        'SAVE_CHANGES' => 'Gem ændringer',
                        'UNDO' => 'Fortryd'
                    ),
                )
            )
        )
    )
);
