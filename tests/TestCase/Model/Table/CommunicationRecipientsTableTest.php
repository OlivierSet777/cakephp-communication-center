<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Model\Table;

use Cake\TestSuite\TestCase;
use CommunicationCenter\Model\Table\CommunicationRecipientsTable;

/**
 * CommunicationCenter\Model\Table\CommunicationRecipientsTable Test Case
 */
class CommunicationRecipientsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \CommunicationCenter\Model\Table\CommunicationRecipientsTable
     */
    protected $CommunicationRecipients;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'plugin.CommunicationCenter.CommunicationRecipients',
        'plugin.CommunicationCenter.CommunicationCampaigns',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CommunicationRecipients') ? [] : ['className' => CommunicationRecipientsTable::class];
        $this->CommunicationRecipients = $this->getTableLocator()->get('CommunicationRecipients', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CommunicationRecipients);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \CommunicationCenter\Model\Table\CommunicationRecipientsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $recipient = $this->CommunicationRecipients->newEntity([
            'communication_campaign_id' => null,
            'external_id' => '',
            'rendered_message' => '',
        ]);

        $this->assertTrue($recipient->hasErrors());
        $this->assertArrayHasKey(
            'communication_campaign_id',
            $recipient->getErrors(),
        );
        $this->assertArrayHasKey(
            'external_id',
            $recipient->getErrors(),
        );
        $this->assertArrayHasKey(
            'rendered_message',
            $recipient->getErrors(),
        );
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \CommunicationCenter\Model\Table\CommunicationRecipientsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $recipient = $this->CommunicationRecipients->newEntity([
            'communication_campaign_id' => 999999,
            'external_id' => 'test',
            'rendered_message' => 'Test message',
            'status' => 'pending',
        ]);

        $this->assertFalse(
            $this->CommunicationRecipients->save($recipient),
        );

        $this->assertArrayHasKey(
            'communication_campaign_id',
            $recipient->getErrors(),
        );
    }
}
