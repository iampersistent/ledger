<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class Entry extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('ledger_entries')
            ->addColumn('credit', 'text', ['null' => true])
            ->addColumn('date', 'date')
            ->addColumn('debit', 'text', ['null' => true])
            ->addColumn('description', 'string', ['limit' => 255])
            ->addColumn('ledger_id', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('line', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_MEDIUM])
            ->addColumn('reference_number', 'string', ['limit' => 255])
            ->create();
    }
}
