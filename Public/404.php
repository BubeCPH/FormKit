<?php
//session_start(); 
defined('DS') or define('DS', '/');

defined('PUBLICPATH') or define('PUBLICPATH', dirname(realpath(__FILE__)));
defined('PUBLICHTMLPATH') or define('PUBLICHTMLPATH', '/public/');

defined('BASEPATH') or define('BASEPATH', dirname(realpath(__FILE__)) . '/../');
defined('COREPATH') or define('COREPATH', BASEPATH . 'Kalna/');
defined('CONFIGPATH') or define('CONFIGPATH', COREPATH . 'Config/');
defined('LIBPATH') or define('LIBPATH', COREPATH . 'Library/');
defined('INCL_LIBPATH') or define('INCL_LIBPATH', LIBPATH . 'Includes/');
defined('VIEWPATH') or define('VIEWPATH', COREPATH . 'View/');

defined('PKGPATH') or define('PKGPATH', COREPATH . 'TIME/');
defined('PKGMODEL') or define('PKGMODEL', PKGPATH . 'Models/');
defined('PKGVMODEL') or define('PKGVMODEL', PKGPATH . 'ViewModels/');
defined('PKGVIEW') or define('PKGVIEW', PKGPATH . 'Views/');
defined('PKGCONTROLLER') or define('PKGCONTROLLER', PKGPATH . 'Controllers/');
require_once LIBPATH . 'HTML.php';
require_once LIBPATH . 'GeneralFunctions.php';
require_once LIBPATH . 'DBAbstraction.php';
require_once LIBPATH . 'SessionRegistry.php';

$html = new HTML();
$gf = new GeneralFunctions();
$firephp = FirePHP::getInstance(true);
$registry = SessionRegistry::getInstance();
?>
<!DOCTYPE html>
<html lang="da">
    <head>
        <meta name="GENERATOR" content="<?= $config->config_values['header']['meta']['GENERATOR'] ?>" />
        <meta name="AUTHOR" content="<?= $config->config_values['header']['meta']['AUTHOR'] ?>" />
        <meta http-equiv="content-type" content="<?= $config->config_values['header']['meta']['content-type'] ?>" />
        <meta http-equiv="content-script-type" content="<?= $config->config_values['header']['meta']['content-script-type'] ?>" />
        <title>
            <?
            if (isset($_SESSION['ongoing_start_time'])) {
                echo $_SESSION['ongoing_start_time'] . " " . $_SESSION['ongoing_description'];
            } else {
                echo $config->config_values['header']['title'];
            }
            list($d, $m, $y) = explode("-", $_SESSION['date']);
            $_SESSION['sql_date'] = $y . "-" . $m . "-" . $d;
            $sql_date = date("Y-m-d"); //$_SESSION['sql_date'];
            ?>
        </title>
        <!-- jQuery -->
        <?= HTML::linkExternalJS('http://code.jquery.com/jquery-latest.min.js'); ?>
        <?= HTML::linkExternalJS('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.8/jquery-ui.min.js'); ?>
        <?= HTML::linkJS('tablesorter/jquery.tablesorter.min'); ?>
        <?= HTML::linkJS('tablesorter/jquery.metadata'); ?>
        <?= HTML::linkJS('calendarPicker/jquery.calendarPicker'); ?>
        <?= HTML::linkJS('calendarPicker/jquery.mousewheel'); ?>
        <?= HTML::linkJS('countdown/jquery.countdown.min'); ?>
        <?= HTML::linkJSCSS('calendarPicker/jquery.tablesorter.pager'); ?>
        <?= HTML::linkCSS('style') ?>
        <?= HTML::linkCSS('button_style') ?>
        <?= HTML::linkJSCSS('calendarPicker/jquery.calendarPicker'); ?>

        <script type="text/javascript">
            function logout() {
                $('#actionInput').val('Log ud');
                //alert('test1');
                //$('#actionForm').action = '/';
                //$('#actionForm').method = 'POST';
                //$('#actionForm').target = '_self';
                $('#actionForm').submit();
                                
            }
            function submitAction(area,action,value,id) {
                $('#areaInput').val(area);
                $('#actionInput').val(action);
                $('#valueInput').val(value);
                $('#idInput').val(id);
                //alert('test1');
                //$('#actionForm').action = '/';
                //$('#actionForm').method = 'POST';
                //$('#actionForm').target = '_self';
                $('#actionForm').submit();
                                
            }
            function changeText(tag,newtext){
                tag.innerHTML = newtext;
            }
        </script>
        <script type="text/javascript">
            function submitForm(action,value,formid,type) {
                $('#'+formid+'_action').val(action);
                $('#'+formid+'_value').val(value);
                /*if (type == 'Fav') {
                    $('#'+formid).action = '<? //= $base;     ?>GemFavoritter.php';
                } else {
                    $('#'+formid).action = '<? //= $base;     ?>GemRegistrering.php';
                }*/
                $('#'+formid).method = 'POST';
                $('#'+formid).target = '_self';
                $('#'+formid).submit();
            }
            function changeText2Input(tag,inputfield,id,value){
                var inputid = inputfield+id;
                //var opdatid = '#opdat_'+id;
                var input = document.getElementById(inputid);
                input.style.display = 'inline';
                input.focus();
                if(value === undefined) {
                    input.value = tag.innerHTML;
                } else {
                    input.value = value;
                }
                tag.innerHTML = '';
                tag.onclick = '';
                //$(opdatid).removeClass('disabled');
                removeDisabledClass(id);
                if(tag !== undefined) {
                    tag.removeClass();
                }
                    
                //
            }
            function changeText2Textarea(tag,inputfield,id,value){
                var inputid = inputfield+id;
                //var opdatid = '#opdat_'+id;
                var input = document.getElementById(inputid);
                //var opdatFunk = 'submitForm("action","Opdater","tp_"'+id+'");'
                input.style.display = 'inline';
                input.focus();
                if(value === undefined) {
                    input.value = tag.innerHTML;
                } else {
                    input.value = value;
                }
                tag.innerHTML = '';
                tag.onclick = '';
                //$(opdatid).removeClass('disabled');
                removeDisabledClass(id);
                if(tag !== undefined) {
                    tag.removeClass();
                }
                //tag.removeClass('changeText2Input');
            }
            function removeDisabledClass(id){
                var opdatid = '#opdat_'+id;
                $(opdatid).removeClass('disabled');
            }
            function showRemovedEntries(span){
                if (span.innerHTML == 'vis slettede'){
                    span.innerHTML = 'skjul slettede';
                    span.click = 'showRemovedEntries(this)';
                    $('.slettet').show("fast");
                                                                        
                } else {
                    span.innerHTML = 'vis slettede';
                    span.click = 'showRemovedEntries(this)';
                    $('.slettet').hide("fast");
                }
            }
        </script>
    </head>
    <body>
        <div id="splash">
            <div id="page">
                <div id="content">
                    <div class="bookingHead">
                        <?= "<h1>kalna t:me<span>" . "OOPS" . "</span></h1>"; ?>

                       <!-- <form action="<?= CONTOLLERPATH . 'changeDate' ?>" method="post" id="menuForm">-->
                        <?php //echo $menuMglBrugernavn;    ?>
                        <div class="menu">
                            <!--<input type="hidden" name="action" id="action_menu" />
                            <input type="hidden" name="value" id="value_menu" />-->
                            <span class="menuItems">
                                <span class="menuElement <?= $gf->pageSelected('Favoritter') ?>" onclick="submitAction('Global', 'ChangePage', 'Favoritter')">
                                    Rediger<br />favoritter
                                </span>
                                <span class="menuElement <?= $gf->pageSelected('Registration') ?>" onclick="submitAction('Global', 'ChangePage', 'Registration')">
                                    Registrer<br />arbejde
                                </span>
                                <span class="menuElement <?= $gf->pageSelected('Oversigt') ?>" onclick="submitAction('Global', 'ChangePage', 'Oversigt')">
                                    Vis<br />oversigt
                                </span>
                                <span class="menuElement <?= $gf->pageSelected('Opsaetning') ?>" onclick="submitAction('Global', 'ChangePage', 'Opsaetning')">
                                    Ops&aelig;t-<br />ning
                                </span><br />
                                <span class="menuElement" onclick="calendarPicker1.changeDate(new Date())">
                                    G&aring; til<br />&nbsp;&nbsp;"I dag"&nbsp;&nbsp;
                                </span>
                                <span class="menuElement" onclick="submitAction('Global', 'Logout')">
                                    &nbsp;Log&nbsp;<br />ud
                                </span>
                            </span>
                        </div>
                        <?= $menuBlokTid; ?>
                        <div id="dsel1" style="width:400px"></div>
                        <input type="hidden" name="chosen_date_new" id="new_date" />
                        <?php
                        $date_m_1 = $registry->get('date_m') - 1;
                        ?>
                        <script type="text/javascript">
                            var calendarPicker1 = $("#dsel1").calendarPicker({
                                date: new Date(<?= $registry->get('date_y') . "," . $date_m_1 . "," . $registry->get('date_d'); ?>) ,
                                useWheel:true,
                                days:5,
                                callback:function(cal) {
                                    var t = new Date();
                                    var d = cal.currentDate;
                                    var date_d = cal.currentDate.getDate();
                                    var date_m = cal.currentDate.getMonth();
                                    var date_y = cal.currentDate.getFullYear();
                                    if (date_d < 10){
                                        date_d = "0" + date_d
                                    }
                                    date_m = date_m+1
                                    if (date_m < 10){
                                        date_m = "0" + date_m
                                    }
                                    if (d.getYear() == t.getYear() && d.getMonth() == t.getMonth() && d.getDate() > t.getDate()||
                                        d.getYear() == t.getYear() && d.getMonth() > t.getMonth()||
                                        d.getYear() > t.getYear()){
                                        // DO NOTHING
                                    } else if ($("#new_date").val() != date_d + "-" + date_m + "-" + date_y
                                        && $("#new_date").val() != ""){
                                        $("#areaInput").val("Global");
                                        $("#actionInput").val("changeDate");
                                        $("#valueInput").val(date_d + "-" + date_m + "-" + date_y);
                                        $("#actionForm").submit();
                                    } else {
                                        $("#new_date").val(date_d + "-" + date_m + "-" + date_y);
                                    }
                                }
                            });
                        </script>
                        <!--</form>-->
                    </div>




                </div>
            </div>
            <div id="footer">
                &copy; 2011-<?= date("Y"); ?> kalna & Bube design. all rights reserved.
            </div>
        </div><!-- make it happen -->
        <script type="text/javascript">
            $(document).ready(function() 
            { 
                /*$("#day_summery").tablesorter( {sortList: [[2,1]]} ); 
                 $("#time_plan").tablesorter(); */
            } 
        );
        </script>
        <form id="actionForm" action="<?= CONTOLLERPATH . 'action' ?>" method="post">
            <table  class="tablesorter">
                <thead>
                    <tr>
                        <th>user_id</th>
                        <th>area</th>
                        <th>action</th>
                        <th>value</th>
                        <th>id</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="user_id" value="<?= $UID; ?>"/></td>
                        <td><input type="text" name="area" id="areaInput" /></td>
                        <td><input type="text" name="action" id="actionInput" /></td>
                        <td><input type="text" name="value" id="valueInput" /></td>
                        <td><input type="text" name="id" id="idInput" value="<?= $registry->test(); ?>" /></td>
                    </tr>
                    <tr>
                        <td colspan="1">Callback:</td>
                        <td colspan="4"><?= $registry->get('callback') . '<br>' ?> <?php print_r($registry->getAll()); ?></td>
                    </tr>
                    <tr>
                        <td colspan="1">Step:</td>
                        <td colspan="4"><?= $registry->get('steps'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="1">Debug message:</td>
                        <td colspan="4"><?= $registry->get('dbms'); ?></td>
                    </tr>
                </tbody>
            </table>
        </form>        
    </body>
</html>