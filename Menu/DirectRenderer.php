<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Ulf Wendel <ulf.wendel@phpdoc.de>                           |
// |          Sebastian Bergmann <sb@sebastian-bergmann.de>               |
// |          Alexey Borzov <avb@php.net>                                 |
// +----------------------------------------------------------------------+
//
// $Id$
//

require_once 'HTML/Menu/Renderer.php';

/**
 * The renderer that generates HTML for the menu itself.
 * 
 * Based on HTML_Menu 1.0 code
 * XXX: no output customization for now (except for subclassing this). BAD.
 * 
 * @version  $Revision$
 * @author   Ulf Wendel <ulf.wendel@phpdoc.de>
 * @author   Alexey Borzov <avb@php.net>
 * @access   public
 * @package  HTML_Menu
 */
class HTML_Menu_DirectRenderer extends HTML_Menu_Renderer
{
   /**
    * Generated HTML for the menu
    * @var string
    */
    var $_html = '';

    function startMenu()
    {
        $this->_html .= '<table border="1">';
    }

    function finishMenu()
    {
        $this->_html .= '</table>';
    }

    function startRow()
    {
        $this->_html .= '<tr>';
    }

    function finishRow()
    {
        $this->_html .= '</tr>';
    }

    function renderEntry(&$node, $level, $type)
    {
        if ('tree' == $this->_menuType || 'sitemap' == $this->_menuType) {
            $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
        } else {
            $indent = '';
        }

        // draw the <td></td> cell depending on the type of the menu item
        switch ($type) {
            case HTML_MENU_ENTRY_INACTIVE:
                // plain menu item 
                $this->_html .= '<td>' . $indent . '<a href="' . $node['url'] . '">' .
                                $node['title'] . '</a></td>';
                break;

            case HTML_MENU_ENTRY_ACTIVE:
                // selected (active) menu item
                $this->_html .= '<td>' . $indent . '<b>' . $node['title'] . '</b></td>';
                break;

            case HTML_MENU_ENTRY_ACTIVEPATH:
                // part of the path to the selected (active) menu item
                $this->_html .= '<td>' . $indent . '<b><a href="' . $node['url'] . '">' . 
                                $node['title'] . '</a></b></td>';
                break;

            case HTML_MENU_ENTRY_BREADCRUMB:
                // part of the path to the selected (active) menu item
                $this->_html .= '<td>' . $indent . '<a href="' . $node['url'] . '">' . 
                                $node['title'] . '</a> &gt;&gt; </td>';
                break;

            case HTML_MENU_ENTRY_PREVIOUS:
                // << previous url
                $this->_html .= '<td>' . $indent . '<a href="' . $node['url'] . '">&lt;&lt; ' . 
                                $node['title'] . '</a></td>';
                break;

            case HTML_MENU_ENTRY_NEXT:
                // next url >>
                $this->_html .= '<td>' . $indent . '<a href="' . $node['url'] . '">' . 
                                $node['title'] . ' &gt;&gt;</a></td>';
                break;

            case HTML_MENU_ENTRY_UPPER:
                // up url ^^
                $this->_html .= '<td>' . $indent . '<a href="' . $node['url'] . '">^ ' . 
                                $node['title'] . ' ^</a></td>';
                break;
        }
    }


   /**
    * returns the HTML generated for the menu
    *
    * @access public
    * @return string
    */
    function toHtml()
    {
        return $this->_html;
    } // end func toHtml
}

?>
