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
 */
namespace DebugKit\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use DebugKit\Model\Entity\Panel;

/**
 * The panels table collects the information for each panel on
 * each request.
 *
 * @method Panel get($primaryKey, $options = [])
 * @method Panel newEntity($data = null, array $options = [])
 * @method Panel[] newEntities(array $data, array $options = [])
 * @method Panel save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method Panel patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method Panel[] patchEntities($entities, array $data, array $options = [])
 * @method Panel findOrCreate($search, callable $callback = null)
 */
class PanelsTable extends Table
{
    use LazyTableTrait;

    /**
     * initialize method
     *
     * @param array $config Config data.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->belongsTo('DebugKit.Requests');
        $this->ensureTables(['DebugKit.Requests', 'DebugKit.Panels']);
    }

    /**
     * Find panels by requestid
     *
     * @param \Cake\ORM\Query $query The query
     * @param array $options The options to use.
     * @return \Cake\ORM\Query The query.
     * @throws \RuntimeException
     */
    public function findByRequest(Query $query, array $options)
    {
        if (empty($options['requestId'])) {
            throw new \RuntimeException(__d('debug_kit', 'Missing request id in {0}.', 'findByRequest()'));
        }

        return $query->where(['Panels.request_id' => $options['requestId']])
            ->order(['Panels.title' => 'ASC']);
    }

    /**
     * DebugKit tables are special.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'debug_kit';
    }
}
