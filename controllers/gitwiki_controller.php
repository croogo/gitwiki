<?php
/**
 * Gitwiki Controller
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
class GitwikiController extends GitwikiAppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'Gitwiki';
/**
 * Use cache
 *
 * @var boolean
 * @access public
 */
    public $useCache = true;
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Setting');
/**
 * Helpers used by the Controller
 *
 * @var array
 * @access public
 */
    public $helpers = array('Gitwiki.Gitwiki');

    public function index() {
        $routes = $this->__generate('routes', array(
            'cache' => array(
                'name' => 'gitwiki_routes',
            ),
        ));
        $titles = $this->__generate('titles', array(
            'routes' => $routes,
            'cache' => array(
                'name' => 'gitwiki_titles',
            ),
        ));
        $thread = $this->__generate('thread', array(
            'titles' => $titles,
            'cache' => array(
                'name' => 'gitwiki_thread',
            ),
        ));
        $route = implode('/', $this->params['pass']);
        if (!isset($routes[$route])) {
            $this->redirect('/' . Configure::read('Gitwiki.route_prefix'));
        }
        $title = $titles[$route];
        $body = $this->__body(Configure::read('Gitwiki.documentation_path'), $routes, $route);
        $breadcrumbs = $this->__breadcrumbs($route, $titles);
        $title_for_layout = ucfirst(Configure::read('Gitwiki.route_prefix'));
	if (strlen($route) > 0) {
	    foreach ($breadcrumbs AS $breadcrumb) {
		$title_for_layout .= ' &raquo; ' . $breadcrumb;
	    }
	}
        $children = $this->__children($routes[$route], $thread);
        $this->set(compact('routes', 'titles', 'nest', 'thread', 'route', 'title', 'body', 'breadcrumbs', 'children', 'title_for_layout'));
    }

    private function __body($path, $routes, $route) {
        $body = '';
        $fullPath = $path . $routes[$route];
        if (file_exists($fullPath)) {
            $body = file_get_contents($fullPath);
            $bodyE = explode("\n", $body);
            if (strstr($bodyE['0'], '# ')) {
                unset($bodyE['0']);
                $body = trim(implode("\n", $bodyE));
            }
        }
        return $body;
    }

    private function __children($path, $thread) {
        $path = str_replace('/00.md', '', $path);
        $extractPath = str_replace('/', '.children.', $path) . '.children';
        return Set::classicExtract($thread, $extractPath);
    }

    private function __breadcrumbs($route, $titles) {
        $breadcrumbs = array();
        $routeE = explode('/', $route);
        $checkRoute = '';
        $i = 0;
        foreach ($routeE AS $r) {
            if ($i == 0) {
                $checkRoute = $r;
            } else {
                $checkRoute .= '/' . $r;
            }
            $breadcrumbs[$checkRoute] = $titles[$checkRoute];
            $i++;
        }
        return $breadcrumbs;
    }

    private function __generate($find, $options = array()) {
        $_options = array(
            'path' => Configure::read('Gitwiki.documentation_path'),
            'routes' => array(),
            'titles' => array(),
            'cache' => array(
                'config' => 'gitwiki_index',
            ),
        );
        $options = Set::merge($_options, $options);
        
        if ($this->useCache && isset($options['cache']['name'])) {
            $cacheName = $options['cache']['name'];
            $results = Cache::read($cacheName, $options['cache']['config']);
            if ($results) {
                return $results;
            } else {
                $results = array();
                if ($find == 'routes') {
                    $results = $this->__generateRoutes($options['path']);
                } elseif ($find == 'titles') {
                    $results = $this->__generateTitles($options['path'], $options['routes']);
                } elseif ($find == 'thread') {
                    $results = $this->__generateThread($options['path'], $options['titles']);
                }
                Cache::write($cacheName, $results, $options['cache']['config']);
                return $results;
            }
        }

        $results = array();
        if ($find == 'routes') {
            $results = $this->__generateRoutes($options['path']);
        } elseif ($find == 'titles') {
            $results = $this->__generateTitles($options['path'], $options['routes']);
        } elseif ($find == 'thread') {
            $results = $this->__generateThread($options['path'], $options['titles']);
        }
        return $results;
    }

    private function __generateRoutes($path, $documentationRoot = null) {
        if (!$documentationRoot) {
            $documentationRoot = $path;
        }
        $list = array();
        $dirItems = scandir($path);
        $dirItems = Set::merge($dirItems, array('00.md'));
        foreach ($dirItems AS $dirItem) {
            if (in_array($dirItem, Configure::read('Gitwiki.ignore'))) {
                continue;
            }
            $fullPath = $path . $dirItem;
            if (is_dir($fullPath)) {
                $subdirItems = $this->__generateRoutes($fullPath . DS, $documentationRoot);
                $list = Set::merge($list, $subdirItems);
            } else {
                $shortPath = str_replace($documentationRoot, '', $fullPath);
                $list[$this->__pathToRoute($shortPath)] = $shortPath;
            }
        }
        asort($list);
        return $list;
    }

    private function __pathToRoute($path) {
        $pathE = explode('/', $path);
        $output = array();
        foreach ($pathE AS $p) {
            $p = str_replace('.md', '', $p);
            $pE = explode('-', $p);
            unset($pE['0']);
            $p = implode('-', $pE);
            if (strlen($p) != 0) {
                $output[] = $p;
            }
        }
        $output = implode('/', $output);
        return $output;
    }

    private function __generateTitles($path, $routes = array()) {
        if (count($routes) == 0) {
            $routes = $this->__generateRoutes($path);
        }
        $list = array();
        foreach ($routes AS $route => $shortPath) {
            $fullPath = $path . $shortPath;
            $title = $this->__routeToTitle($route);
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $contentE = explode("\n", $content);
                if (isset($contentE['0']) && strlen($contentE['0']) > 0 && strstr($contentE['0'], '# ')) {
                    $title = str_replace('# ', '', $contentE['0']);
                }
            }
            $list[$route] = $title;
        }
        return $list;
    }

    private function __routeToTitle($route) {
        $routeE = explode('/', $route);
        $title = str_replace('-', ' ', ucfirst(array_pop($routeE)));
        return $title;
    }

    private function __generateThread($path = null, $titles = array()) {
        if (!$path) {
            $path = Configure::read('Gitwiki.documentation_path');
        }
        if (substr($path, strlen($path) - 1, strlen($path)) == '/') {
            $path = substr($path, 0, strlen($path) - 1);
        }
        $dirItems = scandir($path);
        sort($dirItems);
        $thread = array();
        foreach ($dirItems AS $dirItem) {
            if (in_array($dirItem, Set::merge(Configure::read('Gitwiki.ignore'), array('00.md')))) {
                continue;
            }
            $shortPath = str_replace(Configure::read('Gitwiki.documentation_path'), '', $path . DS . $dirItem);
            $route = $this->__pathToRoute($shortPath);
            if (isset($titles[$route])) {
                $title = $titles[$route];
            } else {
                $title = $this->__routeToTitle($route);
            }
            $thread[$dirItem] = array(
                'title' => $title,
                'path' => $shortPath,
                'route' => $route,
                'children' => array(),
            );
            if (is_dir($path . DS . $dirItem)) {
                $thread[$dirItem]['children'] = $this->__generateThread($path . DS . $dirItem, $titles);
            }
        }
        return $thread;
    }

}
?>