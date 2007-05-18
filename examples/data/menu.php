<?php
/**
 * Menu data for HTML_Menu usage examples
 *
 * @category    HTML
 * @package     HTML_Menu
 * @author      Alexey Borzov <avb@php.net>
 * @version     CVS: $Id$
 * @ignore
 */

$data = array(
    1 => array(
        'title' => 'Menu item 1', 
        'url' => '/item1.php',
        'desc' => 'a special element',
        'sub' => array(
            11 => array('title' => 'Menu item 1.1', 'url' => '/item1.1.php', 'desc' => 'nothing special here'),
            12 => array(
                'title' => 'Menu item 1.2', 
                'url' => '/item1.2.php',
                'desc' => 'a very special element',
                'sub' => array(
                    121 => array('title' => 'Menu item 1.2.1', 'url' => '/item1.2.1.php'),
                    122 => array(
                        'title' => 'Menu item 1.2.2', 
                        'url' => '/item1.2.2.php',
                        'desc' => 'a really very special element',
                        'sub' => array(
                            1221 => array('title' => 'Menu item 1.2.2.1', 'url' => '/item1.2.2.1.php'),
                            1222 => array('title' => 'Menu item 1.2.2.2', 'url' => '/item1.2.2.2.php')
                        )
                    ),
                    123 => array('title' => 'Menu item 1.2.3', 'url' => '/item1.2.3.php'),
                )
            ),
            13 => array(
                'title' => 'Menu item 1.3', 
                'url' => '/item1.3.php',
                'sub' => array(
                    131 => array('title' => 'Menu item 1.3.1', 'url' => '/item1.3.1.php'),
                    132 => array('title' => 'Menu item 1.3.2', 'url' => '/item1.3.2.php'),
                )
            )
        )
    ),
    2 => array(
        'title' => 'Menu item 2', 
        'url' => '/item2.php',
        'sub' => array(
            21 => array('title' => 'Menu item 2.1', 'url' => '/item2.1.php'),
            22 => array(
                'title' => 'Menu item 2.2',
                'url' => '/item2.2.php',
                'sub' => array(
                    221 => array('title' => 'Menu item 2.2.1', 'url' => '/item2.2.1.php')
                )
            )
        )
    ),
    3 => array('title' => 'Menu item 3', 'url' => '/item3.php'),
    4 => array(
        'title' => 'Menu item 4', 
        'url' => '/item4.php',
        'sub' => array(
            41 => array('title' => 'Menu item 4.1', 'url' => '/item4.1.php'),
            42 => array('title' => 'Menu item 4.2', 'url' => '/item4.2.php'),
            43 => array('title' => 'Menu item 4.3', 'url' => '/item4.3.php')
        )
    )
);

?>