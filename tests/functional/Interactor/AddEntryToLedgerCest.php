<?php
declare(strict_types=1);

namespace Tests\Functional\Interactor;

use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Ledger;
use IamPersistent\Ledger\Interactor\AddEntryToLedger;
use FunctionalTester;
use Money\Money;

class AddEntryToLedgerCest
{
    public function testNullLinePosition(FunctionalTester $I)
    {
        $addEntryToLedger = new AddEntryToLedger();
        $ledger = (new Ledger())
            ->setBalance(Money::USD(0));

        $entry1 = (new Credit())
            ->setCredit(Money::USD(500));

        $addEntryToLedger->handle($ledger, $entry1);

        $I->assertSame(1, $entry1->getLine());

        $entry2 = (new Credit())
            ->setCredit(Money::USD(500));
        $entities[] = $entry2;
        $addEntryToLedger->handle($ledger, $entry2);

        $I->assertSame(2, $entry2->getLine());
    }

    public function testCalculations(FunctionalTester $I)
    {
        $addEntryToLedger = new AddEntryToLedger();

        $ledger = (new Ledger())
            ->setBalance(Money::USD(0));

        $entry = (new Credit())
            ->setCredit(Money::USD(1000));

        $addEntryToLedger->handle($ledger, $entry);

        $I->assertEquals(Money::USD(1000), $ledger->getBalance());
        $I->assertEquals(Money::USD(1000), $entry->getRunningBalance());
        $I->assertContains($entry, $ledger->getEntries());

        $entry = (new Debit())
            ->setDebit(Money::USD(1000));

        $addEntryToLedger->handle($ledger, $entry);

        $I->assertEquals(Money::USD(0), $ledger->getBalance());
        $I->assertEquals(Money::USD(0), $entry->getRunningBalance());
        $I->assertContains($entry, $ledger->getEntries());
    }
}
