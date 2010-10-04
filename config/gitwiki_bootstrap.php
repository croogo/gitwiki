<?php
/**
 * Documentation (in markdown format) path
 */
    Configure::write('Gitwiki.documentation_path', ROOT . DS . 'plugins' . DS . 'gitwiki' . DS . 'documentation' . DS);
    Configure::write('Gitwiki.ignore', array(
        '.git',
        '.DS_Store',
        '.',
        '..',
    ));

/**
 * Cache configuration
 */
    $cacheConfig = array(
        'duration' => '+1 hour',
        'path' => CACHE,
        'engine' => 'File',
    );
    Cache::config('gitwiki_index', $cacheConfig);

/**
 * Routes info
 */
    Configure::write('Gitwiki.route_prefix', 'gitwiki');

/**
 * Hooks
 */
    Croogo::hookRoutes('Gitwiki');
?>