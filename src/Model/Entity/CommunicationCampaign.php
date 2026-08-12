<?php
declare(strict_types=1);

namespace CommunicationCenter\Model\Entity;

use Cake\ORM\Entity;

/**
 * CommunicationCampaign Entity
 *
 * @property int $id
 * @property string $name
 * @property string $provider
 * @property string $channel
 * @property string $message_template
 * @property string $status
 * @property int $recipients_count
 * @property int $processed_count
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property bool $archived
 * @property string|null $subject
 *
 * @property \CommunicationCenter\Model\Entity\CommunicationRecipient[] $communication_recipients
 */
class CommunicationCampaign extends Entity
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
        'name' => true,
        'provider' => true,
        'channel' => true,
        'message_template' => true,
        'status' => true,
        'recipients_count' => true,
        'processed_count' => true,
        'created' => true,
        'modified' => true,
        'communication_recipients' => true,
        'archived' => true,
        'subject' => true,
    ];
}
