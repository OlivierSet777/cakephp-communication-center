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
                'message_template' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'status' => 'Lorem ipsum dolor sit amet',
                'recipients_count' => 1,
                'processed_count' => 1,
                'created' => '2026-08-08 14:09:47',
                'modified' => '2026-08-08 14:09:47',
            ],
        ];
        parent::init();
    }
}
