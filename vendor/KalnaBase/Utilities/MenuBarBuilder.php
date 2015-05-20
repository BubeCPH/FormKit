<?php

namespace KalnaBase\Utilities;
use KalnaBase\Classes;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MenuBarBuilder
 *
 * @author:     Claus Hjort Bube <cb at kalna.dk>
 * @org_author: 
 * @created:    07-04-2015
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       MenuBarBuilder
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
class MenuBarBuilder {

    private $page;
    private $menuBarItemArray = [];
    private $menu = "";

    // the constructor!
    public function __construct() {
    }

    public function add($id, $sequence, $text, $link = NULL) {
        $this->menuArray[$id] = new Classes\MenuBarItem($id, $sequence, $text, $link);
        return $this;
    }

    public function addChild($parentId, $id, $sequence, $text, $link = NULL) {
        $parent = $this->menuArray[$parentId];
        $parent->addChild(new Classes\MenuBarItem($id, $sequence, $text, $link));
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

?>
