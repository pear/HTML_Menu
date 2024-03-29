<?php
/**
 * Generates a HTML menu from a multidimensional hash
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

/**#@+
 * Constants for menu entry types
 */
define('HTML_MENU_ENTRY_INACTIVE',      0);
define('HTML_MENU_ENTRY_ACTIVE',        1);
define('HTML_MENU_ENTRY_ACTIVEPATH',    2);
define('HTML_MENU_ENTRY_PREVIOUS',      3);
define('HTML_MENU_ENTRY_NEXT',          4);
define('HTML_MENU_ENTRY_UPPER',         5);
define('HTML_MENU_ENTRY_BREADCRUMB',    6); // like activepath, but for 'urhere' type
/**#@-*/

/**
 * Generates a HTML menu from a multidimensional hash.
 *
 * Special thanks to the original author: Alex Vorobiev  <sasha@mathforum.com>.
 *
 * @category    HTML
 * @package     HTML_Menu
 * @author      Ulf Wendel <ulf.wendel@phpdoc.de>
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: @package_version@
 */
class HTML_Menu
{
   /**#@+
    * @access private
    */
   /**
    * Menu structure as a multidimensional hash.
    * @var  array
    * @see  setMenu(), Menu()
    */
    var $_menu = array();

   /**
    * Mapping from URL to menu path.
    * @var  array
    * @see  getPath()
    */
    var $_urlMap = array();

   /**
    * Path to the current menu item.
    * @var  array
    * @see  get(), getPath()
    */
    var $_path = array();

   /**
    * Menu type: tree, rows, you-are-here.
    * @var  array
    * @see  setMenuType()
    */
    var $_menuType = 'tree';

   /**
    * URL Environment Variable
    * @var  string
    */
    var $_urlEnvVar = 'PHP_SELF';

   /**
    * The URL to use an URL for the current page, instead of the one normally
    * taken from env. variables
    * @var string
    * @see forceCurrentUrl(), getCurrentUrl()
    */
    var $_forcedUrl = '';

   /**
    * Index of the menu item that should be made "current"
    * @var mixed
    * @see forceCurrentIndex()
    */
    var $_forcedIndex = null;

   /**
    * URL of the current page.
    * @see  getCurrentURL(), getPath()
    */
    var $_currentUrl = '';

   /**
    * The renderer being used to output the menu
    * @var HTML_Menu_Renderer
    * @see render()
    */
    var $_renderer = null;

   /**
    * Prefix for menu URLs
    * @var string
    * @see setUrlPrefix()
    */
    var $_urlPrefix = '';
    /**#@-*/

   /**
    * Initializes the menu, sets the type and menu structure.
    *
    * @param    array   menu structure
    * @param    string  menu type
    * @param    string  env. variable used to determine current URL
    * @see      setMenuType(), setMenu(), setURLEnvVar()
    */
    function __construct($menu = null, $type = 'tree', $urlEnvVar = 'PHP_SELF')
    {
        if (is_array($menu)) {
            $this->setMenu($menu);
        }
        $this->setMenuType($type);
        $this->setURLEnvVar($urlEnvVar);
    }

   /**
    * PHP4-style constructor for backwards compatibility
    *
    * @param    array   menu structure
    * @param    string  menu type
    * @param    string  env. variable used to determine current URL
    * @see      setMenuType(), setMenu(), setURLEnvVar()
    */
    function HTML_Menu($menu = null, $type = 'tree', $urlEnvVar = 'PHP_SELF')
    {
        self::__construct($menu, $type, $urlEnvVar);
    }


   /**
    * Sets the menu structure.
    *
    * The menu structure is defined by a multidimensional hash. This is
    * quite "dirty" but simple and easy to traverse. An example
    * show the structure. To get the following menu:
    *
    * <pre>
    * 1  - Projects
    * 11 - Projects => PHPDoc
    * 12 - Projects => Forms
    * 2  - Stuff
    * </pre>
    *
    * you need the array:
    *
    * <pre>
    * $menu = array(
    *           1 => array(
    *                  'title' => 'Projects',
    *                  'url' => '/projects/index.php',
    *                  'sub' => array(
    *                           11 => array(
    *                                       'title' => 'PHPDoc',
    *                                       ...
    *                                     ),
    *                           12 => array( ... ),
    *                 )
    *             ),
    *           2 => array( 'title' => 'Stuff', 'url' => '/stuff/index.php' )
    *        )
    * </pre>
    *
    * Note the index 'sub' and the nesting. Note also that 1, 11, 12, 2
    * must be unique. The class uses them as ID's.
    *
    * @param    array
    * @access   public
    */
    function setMenu($menu)
    {
        $this->_menu   = $menu;
        $this->_urlMap = array();
    }


   /**
    * Sets the type of the menu.
    *
    * Available types are: 'tree', 'rows', 'urhere', 'prevnext', 'sitemap'.
    *
    * @param    string type name
    * @access   public
    */
    function setMenuType($menuType)
    {
        $menuType = strtolower($menuType);
        if (in_array($menuType, array('tree', 'rows', 'urhere', 'prevnext', 'sitemap'))) {
            $this->_menuType = $menuType;
        } else {
            $this->_menuType = 'tree';
        }
    }


   /**
    * Sets the environment variable to use to get the current URL.
    *
    * @param    string  environment variable for current URL
    * @access   public
    */
    function setURLEnvVar($urlEnvVar)
    {
        $this->_urlEnvVar = $urlEnvVar;
    }


   /**
    * Returns the HTML menu.
    *
    * @param    string  Menu type: tree, urhere, rows, prevnext, sitemap
    * @return   string  HTML of the menu
    * @access   public
    * @see render()
    */
    function get($menuType = '')
    {
        include_once 'HTML/Menu/DirectRenderer.php';
        $renderer = new HTML_Menu_DirectRenderer();
        $this->render($renderer, $menuType);
        return $renderer->toHtml();
    }


   /**
    * Prints the HTML menu.
    *
    * @access   public
    * @param    string  Menu type: tree, urhere, rows, prevnext, sitemap
    * @see      get(), render()
    */
    function show($menuType = '')
    {
        print $this->get($menuType);
    }


   /**
    * Renders the menu.
    *
    * @access public
    * @param  HTML_Menu_Renderer    Renderer to use
    * @param  string                Type of the menu
    * @throws PEAR_Error
    */
    function render($renderer, $menuType = '')
    {
        if ('' != $menuType) {
            $this->setMenuType($menuType);
        }
        $this->_renderer = $renderer;
        // the renderer will throw an error if it is unable to process this menu type
        $res = $this->_renderer->setMenuType($this->_menuType);
        if (is_object($res) && is_a($res, 'PEAR_Error')) {
            return $res;
        }

        // storing to a class variable saves some recursion overhead
        $this->_path = $this->getPath();

        switch ($this->_menuType) {
            case 'rows':
                $this->_renderRows($this->_menu);
        		break;

            case 'prevnext':
                $this->_renderPrevNext($this->_menu);
                break;

            case 'urhere':
                $this->_renderURHere($this->_menu);
                break;

            default:
                $this->_renderTree($this->_menu);
        } // switch
    }


   /**
    * Finds the type for the node.
    *
    * @access private
    * @param mixed   Node id
    * @param string  Node 'url' attribute
    * @param int     Level in the tree
    * @return int    Node type (one of HTML_MENU_ENTRY_* constants)
    */
    function _findNodeType($nodeId, $nodeUrl, $level)
    {
        $nodeUrl = $this->_prefixUrl($nodeUrl);
        if ($this->_currentUrl == $nodeUrl) {
            // menu item that fits to this url - 'active' menu item
            return HTML_MENU_ENTRY_ACTIVE;
        } elseif (isset($this->_path[$level]) && $this->_path[$level] == $nodeId) {
            // processed menu item is part of the path to the active menu item
            return 'urhere' == $this->_menuType? HTML_MENU_ENTRY_BREADCRUMB: HTML_MENU_ENTRY_ACTIVEPATH;
        } else {
            // not selected, not a part of the path to the active menu item
            return HTML_MENU_ENTRY_INACTIVE;
        }
    }


   /**
    * Renders the tree menu ('tree' and 'sitemap')
    *
    * @access private
    * @param  array     (sub)menu being rendered
    * @param  int       current depth in the tree structure
    */
    function _renderTree($menu, $level = 0)
    {
        foreach ($menu as $node_id => $node) {
            $type = $this->_findNodeType($node_id, $node['url'], $level);

            $this->_renderer->renderEntry($node, $level, $type);
            $this->_renderer->finishRow($level);

            // follow the subtree if the active menu item is in it or if we
            // want the full menu or if node expansion is forced (request #4391)
            if (isset($node['sub']) && ('sitemap' == $this->_menuType ||
                HTML_MENU_ENTRY_INACTIVE != $type || !empty($node['forceExpand']))) {

                $this->_renderTree($node['sub'], $level + 1);
            }
        }
        $this->_renderer->finishLevel($level);
        if (0 == $level) {
            $this->_renderer->finishMenu($level);
        }
    }


   /**
    * Renders the 'urhere' menu
    *
    * @access private
    * @param  array     (sub)menu being rendered
    * @param  int       current depth in the tree structure
    */
    function _renderURHere($menu, $level = 0)
    {
        foreach ($menu as $node_id => $node) {
            $type = $this->_findNodeType($node_id, $node['url'], $level);

            if (HTML_MENU_ENTRY_INACTIVE != $type) {
                $this->_renderer->renderEntry($node, $level, $type);
                // follow the subtree if the active menu item is in it
                if (isset($node['sub'])) {
                    $this->_renderURHere($node['sub'], $level + 1);
                }
            }
        }
        if (0 == $level) {
            $this->_renderer->finishRow($level);
            $this->_renderer->finishMenu($level);
        }
    }


   /**
    * Renders the 'rows' menu
    *
    * @access private
    * @param  array     (sub)menu being rendered
    * @param  int       current depth in the tree structure
    */
    function _renderRows($menu, $level = 0)
    {
        $submenu = false;

        foreach ($menu as $node_id => $node) {
            $type = $this->_findNodeType($node_id, $node['url'], $level);

            $this->_renderer->renderEntry($node, $level, $type);

            // follow the subtree if the active menu item is in it
            if (HTML_MENU_ENTRY_INACTIVE != $type && isset($node['sub'])) {
                $submenu = $node['sub'];
            }
        }

        // every (sub)menu has its own table
        $this->_renderer->finishRow($level);
        $this->_renderer->finishMenu($level);

        // go deeper if neccessary
        if ($submenu) {
            $this->_renderRows($submenu, $level + 1);
        }
    }


   /**
    * Renders the 'prevnext' menu
    *
    * @access private
    * @param  array     (sub)menu being rendered
    * @param  int       current depth in the tree structure
    * @param  int       flag indicating whether to finish processing
    *                   (0 - continue, 1 - this is "next" node, 2 - stop)
    */
    function _renderPrevNext($menu, $level = 0, $flagStop = 0)
    {
        static $last_node = array(), $up_node = array();

        foreach ($menu as $node_id => $node) {
            if (0 != $flagStop) {
                // add this item to the menu and stop recursion - (next >>) node
                if ($flagStop == 1) {
                    $node['url'] = $this->_prefixUrl($node['url']);
                    $this->_renderer->renderEntry($node, $level, HTML_MENU_ENTRY_NEXT);
                    $flagStop = 2;
                }
                break;

            } else {
                $type = $this->_findNodeType($node_id, $node['url'], $level);
                if (HTML_MENU_ENTRY_ACTIVE == $type) {
                    $flagStop = 1;

                    // WARNING: if there's no previous take the first menu entry - you might not like this rule!
                    if (0 == count($last_node)) {
                        reset($this->_menu);
                        list($node_id, $last_node) = each($this->_menu);
                        $last_node['url'] = $this->_prefixUrl($last_node['url']);
                    }
                    $this->_renderer->renderEntry($last_node, $level, HTML_MENU_ENTRY_PREVIOUS);

                    // WARNING: if there's no up take the first menu entry - you might not like this rule!
                    if (0 == count($up_node)) {
                        reset($this->_menu);
                        list($node_id, $up_node) = each($this->_menu);
                        $up_node['url'] = $this->_prefixUrl($up_node['url']);
                    }
                    $this->_renderer->renderEntry($up_node, $level, HTML_MENU_ENTRY_UPPER);
                }
            }

            // remember the last (<< prev) node
            $last_node = $node;

            if (isset($node['sub'])) {
                if (HTML_MENU_ENTRY_INACTIVE != $type) {
                    $up_node = $node;
                }
                $flagStop = $this->_renderPrevNext($node['sub'], $level + 1, $flagStop);
            }
        }

        if (0 == $level) {
            $this->_renderer->finishRow($level);
            $this->_renderer->finishMenu($level);
        }
        return $flagStop;
    }


   /**
    * Returns the path of the current page in the menu 'tree'.
    *
    * @return   array    path to the selected menu item
    * @see      _buildUrlMap(), $_urlMap
    */
    function getPath()
    {
        $this->_currentUrl = $this->getCurrentURL();
        $this->_buildUrlMap($this->_menu, array());

        // If there is no match for the current URL, try to come up with
        // the best approximation by shortening the url
        while ($this->_currentUrl && !isset($this->_urlMap[$this->_currentUrl])) {
            $this->_currentUrl = substr($this->_currentUrl, 0, -1);
        }

        return isset($this->_urlMap[$this->_currentUrl])? $this->_urlMap[$this->_currentUrl]: array();
    }


   /**
    * Builds the mappings from node url to the 'path' in the menu
    *
    * @access   private
    * @param    array       (sub)menu being processed
    * @param    array       path to the (sub)menu
    * @return   boolean     true if the path to the current page was found, otherwise false.
    * @see      getPath(), $_urlMap
    */
    function _buildUrlMap($menu, $path)
    {
        foreach ($menu as $nodeId => $node) {
            $url = $this->_prefixUrl($node['url']);
            $this->_urlMap[$url] = $path;

            if ($url == $this->_currentUrl) {
                return true;
            }

            if (isset($node['sub']) &&
                $this->_buildUrlMap($node['sub'], array_merge($path, array($nodeId)))) {
                return true;
            }
        }
        return false;
    }


   /**
    * Returns the URL of the currently selected page.
    *
    * The returned string is used for all test against the URL's
    * in the menu structure hash.
    *
    * @access public
    * @return string
    */
    function getCurrentURL()
    {
        if (!empty($this->_forcedUrl)) {
            return $this->_forcedUrl;
        } elseif (!empty($this->_forcedIndex)) {
            return $this->_findUrlByIndex($this->_menu, $this->_forcedIndex);
        } elseif (isset($_SERVER[$this->_urlEnvVar])) {
            return $_SERVER[$this->_urlEnvVar];
        } elseif (isset($GLOBALS[$this->_urlEnvVar])) {
            return $GLOBALS[$this->_urlEnvVar];
        } elseif ($env = getenv($this->_urlEnvVar)) {
            return $env;
        } else {
            return '';
        }
    }


   /**
    * Forces the given URL to be "current"
    *
    * @access public
    * @param  string    Url to use
    */
    function forceCurrentUrl($url)
    {
        $this->_forcedUrl   = $url;
        $this->_forcedIndex = null;
    }


   /**
    * Sets the prefix for the URLs in the menu
    *
    * @param  string
    * @access public
    */
    function setUrlPrefix($prefix)
    {
        if (('' != $prefix) && ('/' != substr($prefix, -1))) {
            $prefix .= '/';
        }
        $this->_urlPrefix = $prefix;
    }


   /**
    * Adds the prefix to the URL (see request #2935)
    *
    * @access   private
    * @param    string  URL
    * @return   string  URL with prefix
    * @see      setUrlPrefix()
    */
    function _prefixUrl($url)
    {
        return $this->_urlPrefix . ((empty($this->_urlPrefix) || '/' != $url[0])? $url: substr($url, 1));
    }


   /**
    * Forces the menu item with the given index to become "current"
    *
    * Per request #3237
    *
    * @param    mixed   Menu item index
    * @access   public
    */
    function forceCurrentIndex($index)
    {
        $this->_forcedIndex = $index;
        $this->_forcedUrl   = '';
    }


   /**
    * Returns the 'url' field of the menu item with the given index
    *
    * @param    array   Menu structure to search
    * @param    mixed   Index
    * @return   string  URL
    * @access   private
    */
    function _findUrlByIndex($menu, $index)
    {
        foreach (array_keys($menu) as $key) {
            if ($key == $index) {
                return $menu[$key]['url'];
            } elseif (!empty($menu[$key]['sub']) && '' != ($url = $this->_findUrlByIndex($menu[$key]['sub'], $index))) {
                return $url;
            }
        }
        return '';
    }
}
?>
