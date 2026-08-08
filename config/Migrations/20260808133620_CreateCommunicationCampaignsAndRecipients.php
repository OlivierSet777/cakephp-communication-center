<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateCommunicationCampaignsAndRecipients extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/guides/writing-migrations/migration-methods.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $campaigns = $this->table('communication_campaigns');

        $campaigns
            ->addColumn('name', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('provider', 'string', [
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('channel', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('message_template', 'text', [
                'null' => false,
            ])
            ->addColumn('status', 'string', [
                'limit' => 30,
                'default' => 'draft',
                'null' => false,
            ])
            ->addColumn('recipients_count', 'integer', [
                'default' => 0,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('processed_count', 'integer', [
                'default' => 0,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
            ])
            ->addIndex(['status'])
            ->addIndex(['provider'])
            ->addIndex(['channel'])
            ->create();

        $recipients = $this->table('communication_recipients');

        $recipients
            ->addColumn('communication_campaign_id', 'integer', [
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('external_id', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('firstname', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('lastname', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('phone', 'string', [
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('rendered_message', 'text', [
                'null' => false,
            ])
            ->addColumn('status', 'string', [
                'limit' => 30,
                'default' => 'pending',
                'null' => false,
            ])
            ->addColumn('processed', 'datetime', [
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
            ])
            ->addIndex([
                'communication_campaign_id',
                'status',
            ])
            ->addIndex([
                'communication_campaign_id',
                'external_id',
            ], [
                'unique' => true,
            ])
            ->addForeignKey(
                'communication_campaign_id',
                'communication_campaigns',
                'id',
                [
                    'delete' => 'CASCADE',
                    'update' => 'NO_ACTION',
                ],
            )
            ->create();
    }
}
