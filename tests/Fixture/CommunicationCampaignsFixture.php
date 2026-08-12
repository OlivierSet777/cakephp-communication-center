<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CommunicationCampaignsFixture
 */
class CommunicationCampaignsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'provider' => 'Lorem ipsum dolor sit amet',
                'channel' => 'Lorem ipsum dolor sit amet',
                'message_template' => 'Lorem ipsum dolor sit amet...',
                'status' => 'Lorem ipsum dolor sit amet',
                'archived' => false,
                'recipients_count' => 1,
                'processed_count' => 1,
                'created' => '2026-08-08 14:09:47',
                'modified' => '2026-08-08 14:09:47',
            ],
        ];
        parent::init();
    }
}
