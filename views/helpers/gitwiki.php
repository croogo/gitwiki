<?php
/**
 * Gitwiki Helper
 *
 * Copyright 2010, Fahad Ibnay Heylaal <contact@fahad19.com>
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Fahad Ibnay Heylaal <contact@fahad19.com>
 * @copyright Copyright 2010, Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link http://www.croogo.org
 */
class GitwikiHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
    public $helpers = array(
        'Html',
        'Layout',
    );
/**
 * Holds the Markdown_Parser object
 * 
 * @var object
 * @access public
 */
    public $parser;
/**
 * Convert markdown to html
 *
 * @param  string $text Text in markdown format
 * @return string
 */
    public function transform($text = null) {
        if (!class_exists('Markdown_Parser')) {
            App::import('Vendor', 'Gitwiki.MarkdownParser');
            $this->parser = new Markdown_Parser;
        }
        return $this->parser->transform($text);
    }
/**
 * Shows threaded menu
 *
 * @param  array  $thread
 * @param  string $route
 * @return string 
 */
    public function menu($thread, $route) {
        $thread = Set::merge(array(
            '00-home' => array(
                'title' => 'Home',
                'path' => '00.md',
                'route' => '',
                'children' => array(),
        )), $thread);
        return $this->__menu($thread, $route);
    }
/**
 * Private method for menu()
 *
 * @param  array  $thread
 * @param  string $route
 * @return string
 */
    private function __menu($thread, $route) {
        $output = '<ul>';
        foreach ($thread AS $item) {
            $output .= '<li>';
            $url = '/' . Configure::read('Gitwiki.route_prefix');
            if (strlen($item['route']) > 0) {
                $url .= '/' . $item['route'];
            }
            $output .= $this->Html->link($item['title'], $url, array(
                'class' => ($route == $item['route']) ? 'selected' : false,
            ));
            if (isset($item['children']) && count($item['children']) > 0) {
                $output .= $this->__menu($item['children'], $route);
            }
            $output .= '</li>';
        }
        $output .= '</ul>';
        return $output;
    }
}
?>