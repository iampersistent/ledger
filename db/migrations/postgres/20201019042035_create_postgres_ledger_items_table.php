<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePostgresLedgerItemsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('ledger_items')
            ->addColumn('amount', 'jsonb', ['null' => true])
            ->addColumn('description', 'string', ['limit' => 255])
            ->addColumn('entry_id', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('product_id', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('quantity', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('reference_number', 'string', ['limit' => 255])
            ->addColumn('taxes', 'jsonb', ['null' => true])
            ->addColumn('total', 'jsonb', ['null' => true])
            ->addTimestamps()
            ->addForeignKey('entry_id', 'ledger_entries', 'id', ['delete' => 'CASCADE'])
            ->create();

        $this->execute("
            ALTER TABLE ledger_items
            ADD CONSTRAINT amount_format_check
            CHECK ( 
                amount ? 'amount' AND 
                amount ? 'currency' AND 
                jsonb_typeof(amount->'amount') = 'number' AND 
                jsonb_typeof(amount->'currency') = 'string' 
            )
        "); 

        $this->execute("
            ALTER TABLE ledger_items
            ADD CONSTRAINT taxes_format_check
            CHECK ( 
                taxes ? 'amount' AND 
                taxes ? 'currency' AND 
                jsonb_typeof(taxes->'amount') = 'number' AND 
                jsonb_typeof(taxes->'currency') = 'string' )
            )
        "); 

        $this->execute("
            ALTER TABLE ledger_items
            ADD CONSTRAINT total_format_check
            CHECK ( 
                total ? 'amount' AND 
                total ? 'currency' AND 
                jsonb_typeof(total->'amount') = 'number' AND 
                jsonb_typeof(total->'currency') = 'string' 
            )
        "); 

        $schema = $this->getAdapter()->getOption('schema');
        $this->execute("
            CREATE OR REPLACE FUNCTION {$schema}.update_timestamp()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");

        $this->execute("
            CREATE TRIGGER update_ledger_items_timestamp
            BEFORE UPDATE ON ledger_items
            FOR EACH ROW
            EXECUTE FUNCTION {$schema}.update_timestamp();
        ");
    }
}
