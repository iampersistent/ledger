<?php

use Phinx\Migration\AbstractMigration;

class CreatePostgresLedgersTable extends AbstractMigration
{
    public function up()
    {
        $this->table('ledgers')
            ->addColumn('balance', 'jsonb', ['default' => '{"amount": 0, "currency": "USD"}'])
            ->addTimestamps()
            ->create();

        $this->execute("
            ALTER TABLE ledgers
            ADD CONSTRAINT balance_format_check
            CHECK (
                balance ? 'amount' AND
                balance ? 'currency' AND
                jsonb_typeof(balance->'amount') = 'number' AND
                jsonb_typeof(balance->'currency') = 'string'
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
            CREATE TRIGGER update_ledgers_timestamp
            BEFORE UPDATE ON ledgers
            FOR EACH ROW
            EXECUTE FUNCTION {$schema}.update_timestamp();
        ");
    }

    public function down()
    {
        $this->table('ledgers')->drop()->save();
    }
}
