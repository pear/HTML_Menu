<?php
/**
 * Usage example for HTML_Menu, DirectTree renderer
 *
 * @category    HTML
 * @package     HTML_Menu
 * @author      Alexey Borzov <avb@php.net>
 * @version     CVS: $Id$
 * @ignore
 */

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/DirectTreeRenderer.php';
require_once __DIR__ . '/data/menu.php';

$types = array('tree', 'sitemap');

$menu = new HTML_Menu($data);
$menu->forceCurrentUrl('/item1.2.2.php');

foreach ($types as $type) {
    echo "\n<h1>Trying menu type &quot;{$type}&quot;</h1>\n";
    $renderer = new HTML_Menu_DirectTreeRenderer();
    $menu->render($renderer, $type);
    echo $renderer->toHtml();
}
?>