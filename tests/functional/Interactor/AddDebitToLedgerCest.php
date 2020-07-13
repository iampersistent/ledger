<?php
declare(strict_types=1);

namespace Tests\Functional\Interactor;

use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Ledger;
use IamPersistent\Ledger\Interactor\AddDebitToLedger;
use Money\Money;
use UnitTester;

class AddDebitToLedgerCest
{
    public function testHandle(UnitTester $I)
    {
        $addDebitToLedger = new AddDebitToLedger();

        $ledger = (new Ledger())
            ->setBalance(Money::USD(0));
        $debit1 = (new Debit())
            ->setDebit(Money::USD(1999))
            ->setLine(1);

        $addDebitToLedger->handle($ledger, $debit1);

        $I->assertEquals(Money::USD(-1999), $ledger->getBalance());
        $I->assertCount(1, $ledger->getEntries());
        $I->assertSame(1, $debit1->getLine(), 'The line should have been set');
        $I->assertEquals(Money::USD(-1999), $debit1->getRunningBalance());

        $debit2 = (new Debit())
            ->setDebit(Money::USD(1000))
            ->setLine(2);
        $addDebitToLedger->handle($ledger, $debit2);

        $I->assertEquals(Money::USD(-2999), $ledger->getBalance());
        $I->assertCount(2, $ledger->getEntries());
        $I->assertSame(2, $debit2->getLine(), 'The line should have been set to the bottom');
        $I->assertEquals(Money::USD(-2999), $debit2->getRunningBalance());
    }
}
