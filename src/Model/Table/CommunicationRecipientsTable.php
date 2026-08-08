<?php
declare(strict_types=1);

namespace CommunicationCenter\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CommunicationRecipients Model
 *
 * @property \CommunicationCenter\Model\Table\CommunicationCampaignsTable&\Cake\ORM\Association\BelongsTo $CommunicationCampaigns
 * @method \CommunicationCenter\Model\Entity\CommunicationRecipient newEmptyEntity()
 * @method \CommunicationCenter\Model\Entity\CommunicationRecipient newEntity(array $data, array $options = [])
 * @method array<\CommunicationCenter\Model\Entity\CommunicationRecipient> newEntities(array $data, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationRecipient get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \CommunicationCenter\Model\Entity\CommunicationRecipient findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationRecipient patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\CommunicationCenter\Model\Entity\CommunicationRecipient> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationRecipient|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \CommunicationCenter\Model\Entity\CommunicationRecipient saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\CommunicationCenter\Model\Entity\CommunicationRecipient>|\Cake\Datasource\ResultSetInterface<\CommunicationCenter\Model\Entity\CommunicationRecipient>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\CommunicationCenter\Model\Entity\CommunicationRecipient>|\Cake\Datasource\ResultSetInterface<\CommunicationCenter\Model\Entity\CommunicationRecipient> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\CommunicationCenter\Model\Entity\CommunicationRecipient>|\Cake\Datasource\ResultSetInterface<\CommunicationCenter\Model\Entity\CommunicationRecipient>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\CommunicationCenter\Model\Entity\CommunicationRecipient>|\Cake\Datasource\ResultSetInterface<\CommunicationCenter\Model\Entity\CommunicationRecipient> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CommunicationRecipientsTable extends Table
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

        $this->setTable('communication_recipients');
        $this->setDisplayField('external_id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CommunicationCampaigns', [
            'foreignKey' => 'communication_campaign_id',
            'joinType' => 'INNER',
            'className' => 'CommunicationCenter.CommunicationCampaigns',
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
            ->nonNegativeInteger('communication_campaign_id')
            ->notEmptyString('communication_campaign_id');

        $validator
            ->scalar('external_id')
            ->maxLength('external_id', 255)
            ->requirePresence('external_id', 'create')
            ->notEmptyString('external_id');

        $validator
            ->scalar('firstname')
            ->maxLength('firstname', 255)
            ->allowEmptyString('firstname');

        $validator
            ->scalar('lastname')
            ->maxLength('lastname', 255)
            ->allowEmptyString('lastname');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 50)
            ->allowEmptyString('phone');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('rendered_message')
            ->requirePresence('rendered_message', 'create')
            ->notEmptyString('rendered_message');

        $validator
            ->scalar('status')
            ->maxLength('status', 30)
            ->notEmptyString('status');

        $validator
            ->dateTime('processed')
            ->allowEmptyDateTime('processed');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add(
            $rules->isUnique([
                'communication_campaign_id',
                'external_id',
            ]),
            [
                'errorField' => 'communication_campaign_id',
                'message' => 'This recipient already exists in this campaign.',
            ],
        );

        $rules->add(
            $rules->existsIn(
                ['communication_campaign_id'],
                'CommunicationCampaigns',
            ),
            [
                'errorField' => 'communication_campaign_id',
            ],
        );

        return $rules;
    }
}
