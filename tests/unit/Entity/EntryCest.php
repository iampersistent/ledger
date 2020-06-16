<?php
declare(strict_types=1);

namespace Tests\Unit\Entity;

use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Debit;
use Money\Currency;
use Money\Money;
use UnitTester;

class EntryCest
{
    public function testGetAmount(UnitTester $I)
    {
        $debit = (new Debit())
            ->setDebit($this->getMoney(20));
        $amount = $debit->getAmount();
        $I->assertTrue($amount->equals($this->getMoney(-20)));

        $credit = (new Credit())
            ->setCredit($this->getMoney(15));
        $amount = $credit->getAmount();
        $I->assertTrue($amount->equals($this->getMoney(15)));
    }

    private function getMoney($amount): Money
    {
        $amount *= 100;

        return new Money($amount, new Currency('USD'));
    }
}
