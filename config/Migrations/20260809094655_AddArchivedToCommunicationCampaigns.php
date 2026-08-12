<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddArchivedToCommunicationCampaigns extends BaseMigration
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
        $table = $this->table('communication_campaigns');
        $table->addColumn('archived', 'boolean', [
            'default' => null,
            'null' => false,
        ]);
        $table->update();
    }
}
