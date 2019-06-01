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

        $entry = (new Credit())
            ->setCredit(Money::USD(1000));
        $ledger->addEntry($entry);

        $calculateBalance->handle($ledger);

        $I->assertEquals(Money::USD(1000), $ledger->getBalance());
        $I->assertEquals(Money::USD(1000), $entry->getRunningBalance());

        $entry = (new Debit())
            ->setDebit(Money::USD(500));
        $ledger->addEntry($entry);

        $calculateBalance->handle($ledger);

        $I->assertEquals(Money::USD(500), $ledger->getBalance());
        $I->assertEquals(Money::USD(500), $entry->getRunningBalance());


        $entry = (new Credit())
            ->setCredit(Money::USD(362));
        $ledger->addEntry($entry);

        $calculateBalance->handle($ledger);

        $I->assertEquals(Money::USD(862), $ledger->getBalance());
        $I->assertEquals(Money::USD(862), $entry->getRunningBalance());
    }
}
