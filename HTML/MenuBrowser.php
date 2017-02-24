<?php
/**
 * Simple filesystem browser that can be used to generate menu hashes
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
* Simple filesystem browser that can be used to generate menu (3) hashes based on the directory structure.
*
* Together with menu (3) and the (userland) cache you can use this
* browser to generate simple fusebox like applications / content systems.
*
* Let the menubrowser scan your document root and generate a menu (3) structure
* hash which maps the directory structure, pass it to menu's setMethod() and optionally
* wrap the cache around all this to save script runs. If you do so, it looks
* like this:
*
* <code>
* // document root directory
* define('DOC_ROOT', '/home/server/www.example.com/');
*
* // instantiate the menubrowser
* $browser = new HTML_MenuBrowser(DOC_ROOT);
*
* // instantiate menu (3)
* $menu = new HTML_Menu($browser->getMenu());
*
* // output the sitemap
* $menu->show('sitemap');
* </code>
*
* Now, use e.g. simple XML files to store your content and additional menu informations
* (title!). Subclass exploreFile() depending on your file format.
*
* @category     HTML
* @package      HTML_Menu
* @author       Ulf Wendel <ulf.wendel@phpdoc.de>
* @version      Release: @package_version@
*/
class HTML_MenuBrowser 
{
   /**
    * Filesuffix of your XML files.
    *
    * @var  string
    * @see  HTML_MenuBrowser()
    */
    var $file_suffix = 'xml';

   /**
    * Number of characters of the file suffix.
    *
    * @var  int
    * @see  HTML_MenuBrowser()
    */
    var $file_suffix_length = 3;

   /**
    * Filename (without suffix) of your index / start pages.
    *
    * @var  string
    * @see  HTML_MenuBrowser()
    */
    var $index = 'index';

   /**
    * Full filename of your index / start pages.
    *
    * @var  string
    * @see  $file_suffix, $index
    */
    var $index_file = '';

   /**
    * Directory to scan.
    *
    * @var  string
    * @see  setDirectory()
    */
    var $dir = '';

   /**
    * Prefix for every menu hash entry.
    *
    * Set the ID prefix if you want to merge the browser menu
    * hash with another (static) menu hash so that there're no
    * name clashes with the ids.
    *
    * @var  string
    * @see  setIDPrefix()
    */
    var $id_prefix = '';

    /**
    * Menu (3)'s setMenu() hash.
    *
    * @var  array
    */
    var $menu = array();

   /**
    * Creates the object and optionally sets the directory to scan.
    *
    * @param    string  Directory to scan
    * @param    string  Filename of index pages
    * @param    string  Suffix for files containing the additional data
    * @see      $dir
    */
    function __construct($dir = '', $index = '', $file_suffix = '')
    {
        if ($dir) {
            $this->dir = $dir;
        }
        if ($index) {
            $this->index = $index;
        }
        if ($file_suffix) {
            $this->file_suffix = $file_suffix;
        }

        $this->index_file = $this->index . '.' . $this->file_suffix;
        $this->file_suffix_length = strlen($this->file_suffix);
    }

   /**
    * Creates the object and optionally sets the directory to scan.
    *
    * @param    string  Directory to scan
    * @param    string  Filename of index pages
    * @param    string  Suffix for files containing the additional data
    * @see      $dir
    */
    function HTML_MenuBrowser($dir = '', $index = '', $file_suffix = '')
    {
        self::__construct($dir, $index, $file_suffix);
    }

   /**
    * Sets the directory to scan.
    *
    * @param    string  directory to scan
    * @access   public
    */
    function setDirectory($dir) 
    {
        $this->dir = $dir;
    }


   /**
    * Sets the prefix for every id in the menu hash.
    *
    * @param    string
    * @access   public
    */
    function setIDPrefix($prefix) 
    {
        $this->id_prefix = $prefix;
    }


   /**
    * Returns a hash to be used with menu(3)'s setMenu().
    *
    * @param    string  directory to scan
    * @param    string  id prefix
    * @access   public
    */
    function getMenu($dir = '', $prefix = '') 
    {
        if ($dir) {
            $this->setDirectory($dir);
        }
        if ($prefix) {
            $this->setIDPrefix($prefix);
        }

        // drop the result of previous runs
        $this->files = array();

        $this->menu = $this->browse($this->dir);
        $this->menu = $this->addFileInfo($this->menu);

        return $this->menu;
    }


   /**
    * Recursive function that does the scan and builds the menu (3) hash.
    *
    * @param    string  directory to scan
    * @param    integer entry id - used only for recursion
    * @param    boolean ??? - used only for recursion
    * @return   array
    */
    function browse($dir, $id = 0, $noindex = false)
    {
        $struct = array();
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ('.' == $file || '..' == $file) {
                continue;
            }
            $ffile = $dir . $file;
            if (is_dir($ffile)) {
                $ffile .= '/';
                if (file_exists($ffile . $this->index_file)) {
                    $id++;
                    $struct[$this->id_prefix . $id]['url'] = $ffile . $this->index_file;

                    $sub = $this->browse($ffile, $id + 1, true);
                    if (0 != count($sub)) {
                        $struct[$this->id_prefix . $id]['sub'] = $sub;
                    }
                }
            } else {
                if ($this->file_suffix == substr($file, strlen($file) - $this->file_suffix_length, $this->file_suffix_length)
                    && !($noindex && $this->index_file == $file) )
                {
                    $id++;
                    $struct[$this->id_prefix . $id]['url'] = $dir . $file;
                }
            }
        }
        return $struct;
    }


   /**
    * Adds further informations to the menu hash gathered from the files in it
    *
    * @param    array   Menu hash to examine
    * @return   array   Modified menu hash with the new informations
    */
    function addFileInfo($menu) 
    {
        // no foreach - it works on a copy - the recursive
        // structure requires already lots of memory
        reset($menu);
        while (list($id, $data) = each($menu)) {
            $menu[$id] = array_merge($data, $this->exploreFile($data['url']));
            if (isset($data['sub'])) {
                $menu[$id]['sub'] = $this->addFileInfo($data['sub']);
            }
        }

        return $menu;
    }


   /**
    * Returns additional menu informations decoded in the file that appears in the menu.
    *
    * You should subclass this method to make it work with your own
    * file formats. I used a simple XML format to store the content.
    *
    * @param    string  filename
    */
    function exploreFile($file) 
    {
        $xml = join('', @file($file));
        if (!$xml) {
            return array();
        }

        $doc = xmldoc($xml);
        $xpc = xpath_new_context($doc);

        $menu = xpath_eval($xpc, '//menu');
        $node = &$menu->nodeset[0];

        return array('title' => $node->content);
    }
}
?>
