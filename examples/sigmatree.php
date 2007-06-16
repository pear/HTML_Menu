<?php
/**
 * Usage example for HTML_Menu with SigmaTree renderer
 *
 * @category    HTML
 * @package     HTML_Menu
 * @author      Alexey Borzov <avb@php.net>
 * @version     CVS: $Id$
 * @ignore
 */

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/SigmaTreeRenderer.php';
require_once 'HTML/Template/Sigma.php';
require_once './data/menu.php';

$menu =& new HTML_Menu($data);
$menu->forceCurrentUrl('/item1.2.2.php');

$types = array('tree', 'sitemap');

$tpl =& new HTML_Template_Sigma('./templates');
$tpl->loadTemplateFile('sigmatree.html', true, true);
$renderer =& new HTML_Menu_SigmaTreeRenderer($tpl);

foreach ($types as $type) {
    $tpl->setVariable('type', $type);
    $menu->render($renderer, $type);
    $tpl->parse('type_loop');
}

$rendererCustom =& new HTML_Menu_SigmaTreeRenderer($tpl, 'tree_');
$menu->forceCurrentUrl('/item1.2.2.2.php');
$menu->render($rendererCustom, 'tree');

$tpl->show();
?>
