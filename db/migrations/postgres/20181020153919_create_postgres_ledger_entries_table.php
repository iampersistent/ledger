<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class CreatePostgresLedgerEntriesTable extends AbstractMigration
{
    public function up()
    {
        $this->table('ledger_entries')
            ->addColumn('credit', 'jsonb', ['null' => true])
            ->addColumn('date', 'date')
            ->addColumn('debit', 'jsonb', ['null' => true])
            ->addColumn('description', 'string', ['limit' => 255])
            ->addColumn('ledger_id', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('line', 'integer', ['null' => false, 'signed' => false, 'limit' => MysqlAdapter::INT_MEDIUM])
            ->addColumn('product_id', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('reference_number', 'string', ['limit' => 255])
            ->addColumn('running_balance', 'jsonb', ['null' => true])
            ->addColumn('type', 'string', ['limit' => 255])
            ->addTimestamps()
            ->addForeignKey('ledger_id', 'ledgers', 'id', ['delete' => 'CASCADE'])
            ->create();

        $this->execute("
            ALTER TABLE ledger_entries
            ADD CONSTRAINT credit_format_check
            CHECK (
                credit ? 'amount' AND
                credit ? 'currency' AND
                jsonb_typeof(credit->'amount') = 'number' AND
                jsonb_typeof(credit->'currency') = 'string'
            )
        ");

        $this->execute("
            ALTER TABLE ledger_entries
            ADD CONSTRAINT debit_format_check
            CHECK (
                debit ? 'amount' AND
                debit ? 'currency' AND
                jsonb_typeof(debit->'amount') = 'number' AND
                jsonb_typeof(debit->'currency') = 'string'
            )
        ");

        $this->execute("
            ALTER TABLE ledger_entries
            ADD CONSTRAINT running_balance_format_check
            CHECK (
                running_balance ? 'amount' AND
                running_balance ? 'currency' AND
                jsonb_typeof(running_balance->'amount') = 'number' AND
                jsonb_typeof(running_balance->'currency') = 'string'
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
            CREATE TRIGGER update_ledger_entries_timestamp
            BEFORE UPDATE ON ledger_entries
            FOR EACH ROW
            EXECUTE FUNCTION {$schema}.update_timestamp();
        ");
    }

    public function down()
    {
        $this->table('ledger_entries')->drop()->save();
    }
}
