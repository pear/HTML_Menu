<?php
/**
 * Abstract base class for HTML_Menu renderers
 * 
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category    HTML
 * @package     HTML_Menu
 * @author      Alexey Borzov <avb@php.net>
 * @copyright   2001-2007 The PHP Group
 * @license     http://www.php.net/license/3_01.txt PHP License 3.01
 * @version     CVS: $Id$
 * @link        http://pear.php.net/package/HTML_Menu
 */

/**
 * Abstract base class for HTML_Menu renderers
 *
 * @category    HTML
 * @package     HTML_Menu
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: @package_version@
 * @abstract
 */
class HTML_Menu_Renderer
{
   /**
    * Type of the menu being rendered
    * @var string
    * @access private
    */
    var $_menuType;

   /**
    * Sets the type of the menu being rendered.
    *
    * This method will throw an error if the renderer is not designed
    * to render a specific menu type.
    *
    * @access public
    * @param  string menu type
    * @throws PEAR_Error
    */
    function setMenuType($menuType)
    {
        $this->_menuType = $menuType;
    }


   /**
    * Finish the menu
    *
    * @access public
    * @param  int    current depth in the tree structure
    * @abstract
    */
    function finishMenu($level)
    {
    }


   /**
    * Finish the tree level (for types 'tree' and 'sitemap')
    *  
    * @access public
    * @param  int    current depth in the tree structure
    * @abstract
    */
    function finishLevel($level)
    {
    }


   /**
    * Finish the row in the menu
    *
    * @access public
    * @param  int    current depth in the tree structure
    * @abstract
    */
    function finishRow($level)
    {
    }


   /**
    * Renders the element of the menu
    *
    * @access public
    * @param array   Element being rendered
    * @param int     Current depth in the tree structure
    * @param int     Type of the element (one of HTML_MENU_ENTRY_* constants)
    * @abstract
    */
    function renderEntry($node, $level, $type)
    {
    }
}

?>
