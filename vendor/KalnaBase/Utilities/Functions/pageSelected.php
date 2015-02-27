<?php
/**
 * Description of pageSelected
 *
 * Returning 'selected' if currect page equals the requested
 * 
 * @param $page string The requested page
 *
 * @return string 'selected' if there's a match 
 * 
 */
function pageSelected($page) {
    if ($_SESSION['page'] == $page) {
        return 'selected';
    }
}

?>
