<?php
declare(strict_types=1);

namespace Tests\Unit\Interactor;

use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Ledger;
use IamPersistent\Ledger\Interactor\CalculateBalance;
use Money\Money;
use UnitTester;

class CalculateBalanceCest
{
    public function testHandle(UnitTester $I)
    {
        $calculateBalance = new CalculateBalance();

        $ledger = (new Ledger())
            ->setBalance(Money::USD(0));

        $entry1 = (new Credit())
            ->setCredit(Money::USD(1000))
            ->setLine(1);
        $entries[] = $entry1;
        $ledger->setEntries($entries);

        $calculateBalance->handle($ledger);

        $I->assertEquals(Money::USD(1000), $ledger->getBalance());
        $I->assertEquals(Money::USD(1000), $entry1->getRunningBalance());

        $entry2 = (new Debit())
            ->setDebit(Money::USD(500))
            ->setLine(2);
        $entries[] = $entry2;
        $ledger->setEntries($entries);

        $calculateBalance->handle($ledger);

        $I->assertEquals(Money::USD(500), $ledger->getBalance());
        $I->assertEquals(Money::USD(500), $entry2->getRunningBalance());

        $entry3 = (new Credit())
            ->setCredit(Money::USD(362))
            ->setLine(3);
        $entries[] = $entry3;
        $ledger->setEntries($entries);
        $calculateBalance->handle($ledger);

        $I->assertEquals(Money::USD(862), $ledger->getBalance());
        $I->assertEquals(Money::USD(862), $entry3->getRunningBalance());
    }
}
