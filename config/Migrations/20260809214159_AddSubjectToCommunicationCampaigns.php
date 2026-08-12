<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddSubjectToCommunicationCampaigns extends BaseMigration
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
        $table->addColumn('subject', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addIndex([
            'subject',

            ], [
            'name' => 'BY_SUBJECT',
            'unique' => false,
        ]);
        $table->update();
    }
}
