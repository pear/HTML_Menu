<?php
/**
 * The renderer that generates HTML for the menu all by itself.
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
 * @author      Ulf Wendel <ulf.wendel@phpdoc.de>
 * @author      Sebastian Bergmann <sb@sebastian-bergmann.de>
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
 * The renderer that generates HTML for the menu all by itself.
 * 
 * Inspired by HTML_Menu 1.0 code
 * 
 * @category    HTML
 * @package     HTML_Menu
 * @author      Ulf Wendel <ulf.wendel@phpdoc.de>
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: @package_version@
 */
class HTML_Menu_DirectRenderer extends HTML_Menu_Renderer
{
   /**#@+
    * @access private
    */
   /**
    * Generated HTML for the menu
    * @var string
    */
    var $_html = '';

   /**
    * Generated HTML for the current menu "table"
    * @var string
    */
    var $_tableHtml = '';
    
   /**
    * Generated HTML for the current menu "row"
    * @var string
    */
    var $_rowHtml = '';

   /**
    * The HTML that will wrap around menu "table"
    * @see setMenuTemplate()
    * @var array
    */
    var $_menuTemplate = array('<table border="1">', '</table>');

   /**
    * The HTML that will wrap around menu "row"
    * @see setRowTemplate()
    * @var array
    */
    var $_rowTemplate = array('<tr>', '</tr>');

   /**
    * Templates for menu entries
    * @see setEntryTemplate()
    * @var array
    */
    var $_entryTemplates = array(
        HTML_MENU_ENTRY_INACTIVE    => '<td>{indent}<a href="{url}">{title}</a></td>',
        HTML_MENU_ENTRY_ACTIVE      => '<td>{indent}<b>{title}</b></td>',
        HTML_MENU_ENTRY_ACTIVEPATH  => '<td>{indent}<b><a href="{url}">{title}</a></b></td>',
        HTML_MENU_ENTRY_PREVIOUS    => '<td><a href="{url}">&lt;&lt; {title}</a></td>',
        HTML_MENU_ENTRY_NEXT        => '<td><a href="{url}">{title} &gt;&gt;</a></td>',
        HTML_MENU_ENTRY_UPPER       => '<td><a href="{url}">^ {title} ^</a></td>',
        HTML_MENU_ENTRY_BREADCRUMB  => '<td><a href="{url}">{title}</a> &gt;&gt; </td>'
    );
    /**#@-*/

    function finishMenu($level)
    {
        $this->_html     .=  $this->_menuTemplate[0] . $this->_tableHtml . $this->_menuTemplate[1];
        $this->_tableHtml = '';
    }

    function finishRow($level)
    {
        $this->_tableHtml .= $this->_rowTemplate[0] . $this->_rowHtml . $this->_rowTemplate[1];
        $this->_rowHtml    = '';
    }

    function renderEntry($node, $level, $type)
    {
        $keys = array('{indent}');
        if ('tree' == $this->_menuType || 'sitemap' == $this->_menuType) {
            $values = array(str_repeat('&nbsp;&nbsp;&nbsp;', $level));
        } else {
            $values = array('');
        }
        foreach ($node as $k => $v) {
            if ('sub' != $k && is_scalar($v)) {
                $keys[]   = '{' . $k . '}';
                $values[] = $v;
            }
        }
        $this->_rowHtml .= str_replace($keys, $values, $this->_entryTemplates[$type]);
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


   /**
    * Sets the menu template (HTML that wraps around rows)
    *  
    * @access public
    * @param  string    this will be prepended to the rows HTML
    * @param  string    this will be appended to the rows HTML
    */
    function setMenuTemplate($prepend, $append)
    {
        $this->_menuTemplate = array($prepend, $append);
    }


   /**
    * Sets the row template (HTML that wraps around entries)
    *  
    * @access public
    * @param  string    this will be prepended to the entries HTML
    * @param  string    this will be appended to the entries HTML
    */
    function setRowTemplate($prepend, $append)
    {
        $this->_rowTemplate = array($prepend, $append);
    }


   /**
    * Sets the template for menu entry.
    * 
    * The template should contain at least the {title} placeholder, can also contain
    * {url} and {indent} placeholders, depending on entry type.
    * 
    * @access public
    * @param  mixed     either type (one of HTML_MENU_ENTRY_* constants) or an array 'type' => 'template'
    * @param  string    template for this entry type if $type is not an array
    */
    function setEntryTemplate($type, $template = null)
    {
        if (is_array($type)) {
            // array_merge() will not work here: the keys are numeric
            foreach ($type as $typeId => $typeTemplate) {
                if (isset($this->_entryTemplates[$typeId])) {
                    $this->_entryTemplates[$typeId] = $typeTemplate;
                }
            }
        } else {
            $this->_entryTemplates[$type] = $template;
        }
    }
}
?>
