<?php

namespace KalnaBase\Utilities;

/**
 * Description of MenuBuilder
 *
 * @author:     Claus Hjort Bube <cb at kalna.dk>
 * @org_author: 
 * @created:    29-06-2014
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       MenuBuilder
 * @version:    0.1
 * @desc:       class for 
 * 
 * @param
 * - foo are required
 * - bar are optional
 * 
 * @example
 * $m = new email ( "hello there",                           // foo
 *                  "how are you?"                           // bar
 *                );
 * 
 * $m->method();
 */
class MenuBuilder {

    private $page;
    private $menuArray = [];
    private $menu = "";

    // the constructor!
    public function __construct($page) {
        $this->page = $page;
    }

    public function add($text, $link = NULL) {
        $this->menuArray[$text] = $link;
        return $this;
    }

    public function __toString() {
        $this->menu = '<ul id="header-main-menu" class="row">';
        foreach ($this->menuArray as $text => $link) {
            if (strpos($link, $this->page->view . '/' . $this->page->section) !== FALSE) {
                $this->menu .= '<li class="selected"><a href="' . $link . '">' . $text . '</a></li>';
            } elseif (!empty ($link)) {
                $this->menu .= '<li><a href="' . $link . '">' . $text . '</a></li>';
            } else {
                $this->menu .= '<li class="menu-sticky"><span>' . $text . '</span></li>'; 
            }
        }
        $this->menu .= '</ul>';
        return $this->menu;
    }

    public static function sanitize($data) {
        return mysql_real_escape_string($data);
    }

}
