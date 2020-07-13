<?php
declare(strict_types=1);

namespace Tests\Functional\Interactor\DBal;

use DateTime;
use Doctrine\DBAL\Connection;
use FunctionalTester;
use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Ledger;
use IamPersistent\Ledger\Interactor\DBal\SaveLedger;
use Money\Money;

final class SaveLedgerCest
{
    /** @var Connection */
    private $connection;
    /** @var SaveLedger */
    private $saveLedger;

    public function _after(FunctionalTester $I)
    {
        $I->closeDatabase();
    }

    public function _before(FunctionalTester $I)
    {
        $this->connection = $I->getDBalConnection();
        $I->setUpDatabase();
        $this->saveLedger = new SaveLedger($this->connection);
    }

    public function testNewLedger(FunctionalTester $I)
    {
        $ledger = (new Ledger())
            ->setBalance(Money::USD(0));
        $this->saveLedger->save($ledger);
        $ledgerData = $this->connection->fetchAll('SELECT * FROM ledgers');
        $I->assertEquals($this->expectedNewLedgerData(), $ledgerData);
    }

    /**
     * @depends testNewLedger
     */
    public function testSetEntries(FunctionalTester $I)
    {
        $credit = (new Credit())
            ->setCredit(Money::USD(1000))
            ->setDate(new DateTime('2018-10-19'))
            ->setDescription('Initial deposit')
            ->setLine(1)
            ->setProductId(42)
            ->setReferenceNumber('8675309')
            ->setType('Deposit');
        $ledger = (new Ledger())
            ->setEntries([$credit])
            ->setBalance(Money::USD(0));
        $this->saveLedger->save($ledger);
        $I->assertEquals(Money::USD(1000), $ledger->getBalance());
        $ledgerData = $this->connection->fetchAll('SELECT * FROM ledgers');
        $I->assertEquals($this->expectedLedgerWithEntryData(), $ledgerData);
        $debit = (new Debit())
            ->setDebit(Money::USD(500))
            ->setDate(new DateTime('2018-10-20'))
            ->setDescription('Toothpicks')
            ->setLine(2)
            ->setProductId(43)
            ->setReferenceNumber('828282')
            ->setType('Cost');
        $ledger->setEntries([$credit, $debit]);
        $this->saveLedger->save($ledger);
        $I->assertEquals(Money::USD(500), $ledger->getBalance());
        $ledgerData = $this->connection->fetchAll('SELECT * FROM ledgers');
        $entryData = $this->connection->fetchAll('SELECT * FROM ledger_entries');
        $I->assertEquals($this->expectedUpdatedLedgerData(), $ledgerData);
        $I->assertEquals($this->expectedEntryData(), $entryData);
    }

    private function expectedEntryData(): array
    {
        return [
            [
                'credit'           => '{"amount":1000,"currency":"USD"}',
                'debit'            => null,
                'date'             => '2018-10-19',
                'description'      => 'Initial deposit',
                'id'               => '1',
                'line'             => '1',
                'ledger_id'        => '1',
                'reference_number' => '8675309',
                'running_balance'  => '{"amount":1000,"currency":"USD"}',
                'type'             => 'Deposit',
                'product_id'       => '42',
            ],
            [
                'credit'           => null,
                'debit'            => '{"amount":500,"currency":"USD"}',
                'date'             => '2018-10-20',
                'description'      => 'Toothpicks',
                'id'               => '2',
                'line'             => '2',
                'ledger_id'        => '1',
                'reference_number' => '828282',
                'running_balance'  => '{"amount":500,"currency":"USD"}',
                'type'             => 'Cost',
                'product_id'       => '43',
            ],
        ];
    }

    private function expectedNewLedgerData(): array
    {
        return [
            [
                'balance' => '{"amount":0,"currency":"USD"}',
                'id'      => '1',
            ],
        ];
    }

    private function expectedLedgerWithEntryData(): array
    {
        return [
            [
                'balance' => '{"amount":1000,"currency":"USD"}',
                'id'      => '1',
            ],
        ];
    }

    private function expectedUpdatedLedgerData(): array
    {
        return [
            [
                'balance' => '{"amount":500,"currency":"USD"}',
                'id'      => '1',
            ],
        ];
    }
}
