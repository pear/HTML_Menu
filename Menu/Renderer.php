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
// | Author: Alexey Borzov <avb@php.net>                                  |
// +----------------------------------------------------------------------+
//
// $Id$
//

/**
 * An abstract base class for HTML_Menu renderers
 *
 * XXX: Do we really need startMenu()/finishMenu() and startRow()/finishRow() pairs?
 * XXX: Most probably this will work with only finishMenu() and finishRow(), have
 * XXX: to check this with Sigma renderer
 * 
 * @package  HTML_Menu
 * @version  $Revision$
 * @author   Alexey Borzov <avb@php.net>
 * @abstract
 */
class HTML_Menu_Renderer
{
   /**
    * Type of the menu being rendered
    * @var string
    */
    var $_menuType;

   /**
    * Sets the type of the menu being rendered.
    *
    * @access public
    * @param  string menu type
    */
    function setMenuType($menuType)
    {
        $this->_menuType = $menuType;
    }


   /**
    * Start a new menu
    *
    * @access public
    */
    function startMenu()
    {
    }


   /**
    * Finish the menu
    *
    * @access public
    */
    function finishMenu()
    {
    }


   /**
    * Start a new row in the menu
    *
    * @access public
    */
    function startRow()
    {
    }


   /**
    * Finish the row in the menu
    *
    * @access public
    */
    function finishRow()
    {
    }


   /**
    * Renders the element of the menu
    *
    * @access public
    * @param array   Element being rendered
    * @param int     Current depth in the tree structure
    * @param int     Type of the element (one of HTML_MENU_ENTRY_* constants)
    */
    function renderEntry(&$node, $level, $type)
    {
    }
}

?>
