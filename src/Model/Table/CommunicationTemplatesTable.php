<?php
declare(strict_types=1);

namespace CommunicationCenter\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CommunicationTemplates Model
 *
 * @method \CommunicationCenter\Model\Entity\CommunicationTemplate newEmptyEntity()
 * @method \CommunicationCenter\Model\Entity\CommunicationTemplate newEntity(array $data, array $options = [])
 * @method array<\CommunicationCenter\Model\Entity\CommunicationTemplate> newEntities(array $data, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationTemplate get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \CommunicationCenter\Model\Entity\CommunicationTemplate findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationTemplate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\CommunicationCenter\Model\Entity\CommunicationTemplate> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationTemplate|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationTemplate saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CommunicationTemplatesTable extends Table
{
    /**
     * Initialize method.
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('communication_templates');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('channel')
            ->maxLength('channel', 50)
            ->requirePresence('channel', 'create')
            ->notEmptyString('channel');

        $validator
            ->scalar('subject')
            ->maxLength('subject', 255)
            ->allowEmptyString('subject');

        $validator
            ->scalar('message_template')
            ->requirePresence('message_template', 'create')
            ->notEmptyString('message_template');

        $validator
            ->boolean('active')
            ->notEmptyString('active');

        return $validator;
    }
}
