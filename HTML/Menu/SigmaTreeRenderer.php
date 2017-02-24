<?php
/**
 * HTML_Template_Sigma-based renderer for 'tree' and 'sitemap' type menus,
 * where menu level is represented by tag nesting.
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
 * HTML_Template_Sigma-based renderer for 'tree' and 'sitemap' type menus,
 * where menu level is represented by tag nesting.
 *
 * @category    HTML
 * @package     HTML_Menu
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: @package_version@
 */
class HTML_Menu_SigmaTreeRenderer extends HTML_Menu_Renderer
{
   /**#@+
    * @access private
    */
   /**
    * Template object used for output
    * @var HTML_Template_Sigma
    */
    var $_tpl;

   /**
    * Prefix for template blocks and placeholders
    * @var string
    */
    var $_prefix;

   /**
    *  
    *
    */
    var $_level = -1;

   /**
    * Mapping from HTML_MENU_ENTRY_* constants to template block names
    * @var array
    */
    var $_typeNames = array(
        HTML_MENU_ENTRY_INACTIVE    => 'inactive',
        HTML_MENU_ENTRY_ACTIVE      => 'active',
        HTML_MENU_ENTRY_ACTIVEPATH  => 'activepath'
    );
    /**#@-*/

   /**
    * Class constructor.
    * 
    * Sets the template object to use and sets prefix for template blocks
    * and placeholders. We use prefix to avoid name collisions with existing 
    * template blocks and it is customisable to allow output of several menus 
    * into one template.
    *
    * @access public
    * @param  HTML_Template_Sigma   template object to use for output
    * @param  string                prefix for template blocks and placeholders
    */
    function __construct($tpl, $prefix = 'mu_')
    {
        $this->_tpl    = $tpl;
        $this->_prefix =  $prefix;
    }

    /**
     * PHP4-style constructor for backwards compatibility
     *
     * @param  HTML_Template_Sigma   template object to use for output
     * @param  string                prefix for template blocks and placeholders
     */
    function HTML_Menu_SigmaTreeRenderer($tpl, $prefix = 'mu_')
    {
        self::__construct($tpl, $prefix);
    }

    function setMenuType($menuType)
    {
        if ('tree' == $menuType || 'sitemap' == $menuType) {
            $this->_menuType = $menuType;
        } else {
            require_once 'PEAR.php';
            return PEAR::raiseError("HTML_Menu_SigmaTreeRenderer: unable to render '$menuType' type menu");
        }
        $this->_level = -1;
    }


    function finishLevel($level)
    {
        // Close the previous entry
        if ($this->_tpl->blockExists($this->_prefix . ($level + 1) . '_entry_close')) {
            $this->_tpl->touchBlock($this->_prefix . ($level + 1) . '_entry_close');
        } else {
            $this->_tpl->touchBlock($this->_prefix . 'entry_close');
        }
        $this->_tpl->parse($this->_prefix . 'tree_loop');
        // Close the level
        if ($this->_tpl->blockExists($this->_prefix . ($level + 1) . '_level_close')) {
            $this->_tpl->touchBlock($this->_prefix . ($level + 1) . '_level_close');
        } else {
            $this->_tpl->touchBlock($this->_prefix . 'level_close');
        }
        $this->_tpl->parse($this->_prefix . 'tree_loop');
    }


    function renderEntry($node, $level, $type)
    {
        // Close the entry if previous was on same or higher level
        if ($this->_level >= $level) {
            if ($this->_tpl->blockExists($this->_prefix . ($level + 1) . '_entry_close')) {
                $this->_tpl->touchBlock($this->_prefix . ($level + 1) . '_entry_close');
            } else {
                $this->_tpl->touchBlock($this->_prefix . 'entry_close');
            }
            $this->_tpl->parse($this->_prefix . 'tree_loop');

        // If the new level is higher then open the level
        } else {
            if ($this->_tpl->blockExists($this->_prefix . ($level + 1) . '_level_open')) {
                $this->_tpl->touchBlock($this->_prefix . ($level + 1) . '_level_open');
            } else {
                $this->_tpl->touchBlock($this->_prefix . 'level_open');
            }
            $this->_tpl->parse($this->_prefix . 'tree_loop');
        }
        // Open the entry
        if ($this->_tpl->blockExists($this->_prefix . ($level + 1) . '_entry_open')) {
            $this->_tpl->touchBlock($this->_prefix . ($level + 1) . '_entry_open');
        } else {
            $this->_tpl->touchBlock($this->_prefix . 'entry_open');
        }
        $this->_tpl->parse($this->_prefix . 'tree_loop');

        if ($this->_tpl->blockExists($this->_prefix . ($level + 1) . '_' . $this->_typeNames[$type])) {
            $blockName = $this->_prefix . ($level + 1) . '_' . $this->_typeNames[$type];
        } else {
            $blockName = $this->_prefix . $this->_typeNames[$type];
        }

        foreach ($node as $k => $v) {
            if ('sub' != $k && $this->_tpl->placeholderExists($this->_prefix . $k, $blockName)) {
                $this->_tpl->setVariable($this->_prefix . $k, $v);
            }
        }
        $this->_tpl->parse($blockName);
        $this->_tpl->parse($this->_prefix . 'tree_loop');

        $this->_level = $level;
    }
}
?>
