<?php
/**
 * Gitwiki Activation
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
class GitwikiActivation {
/**
 * onActivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
    public function beforeActivation(&$controller) {
        return true;
    }
/**
 * Called after activating the plugin in ExtensionsPluginsController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    public function onActivation(&$controller) {
        $controller->Croogo->addAco('Gitwiki');
        $controller->Croogo->addAco('Gitwiki/index', array('registered', 'public'));
    }
/**
 * onDeactivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
    public function beforeDeactivation(&$controller) {
        return true;
    }
/**
 * Called after deactivating the plugin in ExtensionsPluginsController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    public function onDeactivation(&$controller) {
        $controller->Croogo->removeAco('Gitwiki');
    }
}
?>