<?php
/**
 * The renderer that creates an array of visible menu entries.
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
 */ 
require_once 'HTML/Menu/Renderer.php';

/**
 * The renderer that creates an array of visible menu entries.
 * 
 * The resultant array can be used with e.g. a template engine to produce
 * a completely custom menu look.
 * All menu types except 'rows' are "rendered" into a one-dimensional array
 * of entries:
 * <pre>
 * array(
 *    'entry1',
 *    ...
 *    'entryN'
 * )
 * </pre>
 * while 'rows' produce a two-dimensional array:
 * <pre>
 * array(
 *    array('entry 1 for row 1', ..., 'entry M_1 for row 1'),
 *    ...
 *    array('entry 1 for row N', ..., 'entry M_N for row 1')
 * )
 * </pre>
 * Here entry is
 * <pre> 
 * array(
 *    'url'    => url element of menu entry
 *    'title'  => title element of menu entry
 *    'level'  => entry's depth in the tree structure
 *    'type'   => type of entry, one of HTML_MENU_ENTRY_* constants
 *    // if the nodes in the original menu array contained keys other
 *    // than 'url', 'title' and 'sub', they will be copied here, too
 * )
 * </pre>
 * 
 * @category    HTML
 * @package     HTML_Menu
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: @package_version@
 */
class HTML_Menu_ArrayRenderer extends HTML_Menu_Renderer
{
   /**
    * Generated array
    * @var array
    * @access private
    */
    var $_ary = array();

   /**
    * Array for the current "menu", that is moved into $_ary by finishMenu(), 
    * makes sense mostly for 'rows
    * @var array
    * @access private
    */
    var $_menuAry = array();

    function finishMenu($level)
    {
        if ('rows' == $this->_menuType) {
            $this->_ary[] = $this->_menuAry;
        } else {
            $this->_ary   = $this->_menuAry;
        }
        $this->_menuAry = array();
    }


    function renderEntry($node, $level, $type)
    {
        unset($node['sub']);
        $node['level'] = $level;
        $node['type']  = $type;
        $this->_menuAry[] = $node;
    }


   /**
    * Returns the resultant array
    * 
    * @access public
    * @return array
    */
    function toArray()
    {
        return $this->_ary;
    }
}
?>
