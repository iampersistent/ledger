<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateItemsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('ledger_items')
            ->addColumn('amount', 'json', ['null' => true])
            ->addColumn('description', 'string', ['limit' => 255])
            ->addColumn('entry_id', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('product_id', 'string', ['limit' => 255])
            ->addColumn('reference_number', 'string', ['limit' => 255])
            ->addColumn('taxes', 'json', ['null' => true])
            ->addColumn('total', 'json', ['null' => true])
            ->create();
    }
}
