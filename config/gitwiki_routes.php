<?php
    CroogoRouter::connect('/' . Configure::read('Gitwiki.route_prefix') . '/*', array('plugin' => 'gitwiki', 'controller' => 'gitwiki', 'action' => 'index'));
?>