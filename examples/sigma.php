<?php
/**
 * Usage example for HTML_Menu with Sigma renderer
 *
 * @category    HTML
 * @package     HTML_Menu
 * @author      Alexey Borzov <avb@php.net>
 * @version     CVS: $Id$
 * @ignore
 */

require_once 'HTML/Menu.php';
require_once 'HTML/Template/Sigma.php';
require_once 'HTML/Menu/SigmaRenderer.php';
require_once './data/menu.php';

$types = array('tree', 'urhere', 'prevnext', 'rows', 'sitemap');

$menu = new HTML_Menu($data);
$menu->forceCurrentUrl('/item1.2.2.2.php');

$tpl = new HTML_Template_Sigma('./templates');
$tpl->loadTemplateFile('sigma.html');
$renderer = new HTML_Menu_SigmaRenderer($tpl);

foreach ($types as $type) {
    $tpl->setVariable('type', $type);
    $menu->render($renderer, $type);
    $tpl->parse('type_loop');
}

$treeRenderer = new HTML_Menu_SigmaRenderer($tpl, 'tree_');
$menu->render($treeRenderer, 'tree');

$rowsRenderer = new HTML_Menu_SigmaRenderer($tpl, 'rows_');
$menu->render($rowsRenderer, 'rows');

$tpl->show();
?>
