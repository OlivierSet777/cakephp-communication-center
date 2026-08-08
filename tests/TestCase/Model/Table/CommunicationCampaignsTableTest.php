<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Model\Table;

use Cake\TestSuite\TestCase;
use CommunicationCenter\Model\Table\CommunicationCampaignsTable;

/**
 * CommunicationCenter\Model\Table\CommunicationCampaignsTable Test Case
 */
class CommunicationCampaignsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \CommunicationCenter\Model\Table\CommunicationCampaignsTable
     */
    protected $CommunicationCampaigns;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'plugin.CommunicationCenter.CommunicationCampaigns',
        'plugin.CommunicationCenter.CommunicationRecipients',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CommunicationCampaigns') ? [] : ['className' => CommunicationCampaignsTable::class];
        $this->CommunicationCampaigns = $this->getTableLocator()->get('CommunicationCampaigns', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CommunicationCampaigns);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \CommunicationCenter\Model\Table\CommunicationCampaignsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $campaign = $this->CommunicationCampaigns->newEntity([
            'name' => '',
            'provider' => '',
            'channel' => '',
            'message_template' => '',
        ]);

        $this->assertTrue($campaign->hasErrors());
        $this->assertArrayHasKey('name', $campaign->getErrors());
        $this->assertArrayHasKey('provider', $campaign->getErrors());
        $this->assertArrayHasKey('channel', $campaign->getErrors());
        $this->assertArrayHasKey('message_template', $campaign->getErrors());
    }
}
