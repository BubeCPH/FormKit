<?php

namespace KalnaBase\Classes;

/**
 * Description of MenuBar
 *
 * @author:     Claus Hjort Bube <cb at kalna.dk>
 * @org_author: 
 * @created:    07-04-2015
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       MenuBar
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
class MenuBarItem {

    public $id;
    public $sequence;
    public $childs = [];
    public $text;
    public $link;

    // the constructor!
    public function __construct($id, $sequence, $text, $link = NULL) {
        $this->id = $id;
        $this->sequence = $sequence;
        $this->text = $text;
        $this->link = $link;
    }

    public function addChild(MenuBarItem $child) {
        $this->childs[] = $child;
    }

    // the toString
    public function __toString() {
        $return = '<li>';
        if (count($this->childs) === 0) {
            $return .= '<a class="menu_trigger" href="'.$this->link.'">'.$this->text.'</a>';            
        } elseif (count($this->childs) >= 0) {
            $return .= '<a class="menu_trigger" href="#">'.$this->text.'</a>';
            $return .= '<ul class="menu">';
            foreach ($childs as $child) {
                $return .= '<li>';
                $return .= '<a class="punkt" href="'.$this->link.'">'.$child->text.'</a>';
                $return .= '</li>';
            }
            $return .= '</ul>';
        }
        return $return;
    }

}

?>
