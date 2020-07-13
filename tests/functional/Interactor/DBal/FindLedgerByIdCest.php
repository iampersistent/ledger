<?php
declare(strict_types=1);

namespace Tests\Functional\Interactor\DBal;

use DateTime;
use Doctrine\DBAL\Connection;
use FunctionalTester;
use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Ledger;
use IamPersistent\Ledger\Interactor\DBal\FindLedgerById;
use Money\Money;

class FindLedgerByIdCest
{
    /** @var Connection */
    private $connection;
    /** @var FindLedgerById */
    private $findLedger;

    public function _after(FunctionalTester $I)
    {
        $I->closeDatabase();
    }

    public function _before(FunctionalTester $I)
    {
        $this->connection = $I->getDBalConnection();
        $I->setUpDatabase();
        $this->findLedger = new FindLedgerById($this->connection);
        $ledgerData = [
            'balance' => '{"amount":"1000","currency":"USD"}',
        ];
        $this->connection->insert('ledgers', $ledgerData);
        $entries = $this->entryData();
        foreach ($entries as $entryData) {
             $this->connection->insert('ledger_entries', $entryData);
        }
    }

    public function testFind(FunctionalTester $I)
    {
        $ledger = $this->findLedger->find(1);
        $I->assertEquals($this->expectedLedger(), $ledger);
    }

    private function entryData(): array
    {
        return [
            [
                'credit'           => '{"amount":"1500","currency":"USD"}',
                'debit'            => null,
                'date'             => '2018-10-19',
                'description'      => 'Initial deposit',
                'id'               => '1',
                'ledger_id'        => '1',
                'line'             => 1,
                'product_id'       => 42,
                'reference_number' => '8675309',
                'running_balance'  => '{"amount":"1500","currency":"USD"}',
                'type'             => 'Deposit',
            ],
            [
                'credit'           => null,
                'debit'            => '{"amount":"500","currency":"USD"}',
                'date'             => '2018-10-20',
                'description'      => 'Toothpicks',
                'id'               => '2',
                'ledger_id'        => '1',
                'line'             => 2,
                'product_id'       => 43,
                'reference_number' => '828282',
                'running_balance'  => '{"amount":"1000","currency":"USD"}',
                'type'             => 'Cost',
            ],
        ];
    }

    private function expectedLedger(): Ledger
    {
        $ledger = (new Ledger())
            ->setId(1)
            ->setBalance(Money::USD(1000));
        $entries[] = (new Credit())
            ->setCredit(Money::USD(1500))
            ->setDate(new DateTime('2018-10-19'))
            ->setDescription('Initial deposit')
            ->setId(1)
            ->setLine(1)
            ->setProductId(42)
            ->setReferenceNumber('8675309')
            ->setRunningBalance(Money::USD(1500))
            ->setType('Deposit');
        $entries[] = (new Debit())
            ->setDebit(Money::USD(500))
            ->setDate(new DateTime('2018-10-20'))
            ->setDescription('Toothpicks')
            ->setId(2)
            ->setLine(2)
            ->setProductId(43)
            ->setReferenceNumber('828282')
            ->setRunningBalance(Money::USD(1000))
            ->setType('Cost');
        $ledger->setEntries($entries);

        return $ledger;
    }
}
