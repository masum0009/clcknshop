<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */
namespace DebugKit\Panel;

use Cake\Cache\Cache;
use DebugKit\Cache\Engine\DebugEngine;
use DebugKit\DebugPanel;

/**
 * A panel for spying on cache engines.
 */
class CachePanel extends DebugPanel
{

    /**
     * The cache spy instances used.
     *
     * @var DebugEngine[]
     */
    protected $_instances = [];

    /**
     * Initialize - install cache spies.
     *
     * @return void
     */
    public function initialize()
    {
        foreach (Cache::configured() as $name) {
            $config = Cache::getConfig($name);
            if (isset($config['className']) && $config['className'] instanceof DebugEngine) {
                $instance = $config['className'];
            } elseif (isset($config['className'])) {
                Cache::drop($name);
                $instance = new DebugEngine($config);
                Cache::setConfig($name, $instance);
            }
            if (isset($instance)) {
                $this->_instances[$name] = $instance;
            }
        }
    }

    /**
     * Get the data for this panel
     *
     * @return array
     */
    public function data()
    {
        $metrics = [];
        foreach ($this->_instances as $name => $instance) {
            $metrics[$name] = $instance->metrics();
        }

        return [
            'metrics' => $metrics,
        ];
    }
}
