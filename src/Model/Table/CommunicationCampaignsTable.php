<?php
declare(strict_types=1);

namespace CommunicationCenter\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CommunicationCampaigns Model
 *
 * @property \CommunicationCenter\Model\Table\CommunicationRecipientsTable&\Cake\ORM\Association\HasMany $CommunicationRecipients
 * @method \CommunicationCenter\Model\Entity\CommunicationCampaign newEmptyEntity()
 * @method \CommunicationCenter\Model\Entity\CommunicationCampaign newEntity(array $data, array $options = [])
 * @method array<\CommunicationCenter\Model\Entity\CommunicationCampaign> newEntities(array $data, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationCampaign get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \CommunicationCenter\Model\Entity\CommunicationCampaign findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationCampaign patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\CommunicationCenter\Model\Entity\CommunicationCampaign> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationCampaign|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationCampaign saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\CommunicationCenter\Model\Entity\CommunicationCampaign>|\Cake\Datasource\ResultSetInterface<\CommunicationCenter\Model\Entity\CommunicationCampaign>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\CommunicationCenter\Model\Entity\CommunicationCampaign>|\Cake\Datasource\ResultSetInterface<\CommunicationCenter\Model\Entity\CommunicationCampaign> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\CommunicationCenter\Model\Entity\CommunicationCampaign>|\Cake\Datasource\ResultSetInterface<\CommunicationCenter\Model\Entity\CommunicationCampaign>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\CommunicationCenter\Model\Entity\CommunicationCampaign>|\Cake\Datasource\ResultSetInterface<\CommunicationCenter\Model\Entity\CommunicationCampaign> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CommunicationCampaignsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('communication_campaigns');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('CommunicationRecipients', [
            'foreignKey' => 'communication_campaign_id',
            'className' => 'CommunicationCenter.CommunicationRecipients',
        ]);
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
            ->scalar('provider')
            ->maxLength('provider', 100)
            ->requirePresence('provider', 'create')
            ->notEmptyString('provider');

        $validator
            ->scalar('channel')
            ->maxLength('channel', 50)
            ->requirePresence('channel', 'create')
            ->notEmptyString('channel');

        $validator
            ->scalar('message_template')
            ->requirePresence('message_template', 'create')
            ->notEmptyString('message_template');

        $validator
            ->scalar('status')
            ->maxLength('status', 30)
            ->notEmptyString('status');

        $validator
            ->nonNegativeInteger('recipients_count')
            ->notEmptyString('recipients_count');

        $validator
            ->nonNegativeInteger('processed_count')
            ->notEmptyString('processed_count');

        return $validator;
    }
}
