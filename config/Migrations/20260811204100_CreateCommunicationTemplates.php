<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateCommunicationTemplates extends BaseMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('communication_templates');

        $table
            ->addColumn('name', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('channel', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('subject', 'string', [
                'limit' => 255,
                'default' => null,
                'null' => true,
            ])
            ->addColumn('message_template', 'text', [
                'null' => false,
            ])
            ->addColumn('active', 'boolean', [
                'default' => true,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => false,
            ])
            ->addIndex([
                'channel',
            ])
            ->addIndex([
                'active',
            ])
            ->create();
    }
}
