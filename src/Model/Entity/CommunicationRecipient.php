<?php
declare(strict_types=1);

namespace CommunicationCenter\Model\Entity;

use Cake\ORM\Entity;

/**
 * CommunicationRecipient Entity
 *
 * @property int $id
 * @property int $communication_campaign_id
 * @property string $external_id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $phone
 * @property string|null $email
 * @property string $rendered_message
 * @property string $status
 * @property \Cake\I18n\DateTime|null $processed
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \CommunicationCenter\Model\Entity\CommunicationCampaign $communication_campaign
 */
class CommunicationRecipient extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'communication_campaign_id' => true,
        'external_id' => true,
        'firstname' => true,
        'lastname' => true,
        'phone' => true,
        'email' => true,
        'rendered_message' => true,
        'status' => true,
        'processed' => true,
        'created' => true,
        'modified' => true,
        'communication_campaign' => true,
    ];
}
