<?php
declare(strict_types=1);

namespace CommunicationCenter\Model\Entity;

use Cake\ORM\Entity;

/**
 * CommunicationTemplate Entity
 *
 * @property int $id
 * @property string $name
 * @property string $channel
 * @property string|null $subject
 * @property string $message_template
 * @property bool $active
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class CommunicationTemplate extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'channel' => true,
        'subject' => true,
        'message_template' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
    ];
}
