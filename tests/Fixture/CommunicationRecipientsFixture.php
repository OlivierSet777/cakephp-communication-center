<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CommunicationRecipientsFixture
 */
class CommunicationRecipientsFixture extends TestFixture
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
                'communication_campaign_id' => 1,
                'external_id' => 'Lorem ipsum dolor sit amet',
                'firstname' => 'Lorem ipsum dolor sit amet',
                'lastname' => 'Lorem ipsum dolor sit amet',
                'phone' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet',
                'rendered_message' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'status' => 'Lorem ipsum dolor sit amet',
                'processed' => '2026-08-08 14:10:13',
                'created' => '2026-08-08 14:10:13',
                'modified' => '2026-08-08 14:10:13',
            ],
        ];
        parent::init();
    }
}
