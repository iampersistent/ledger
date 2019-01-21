<?php
declare(strict_types=1);

namespace Tests\Unit\DBal;

use IamPersistent\Ledger\Interactor\DBal\MoneyToJson;
use Money\Currency;
use Money\Money;
use UnitTester;

class MoneyToJsonCest
{
    public function testInvoke(UnitTester $I)
    {
        $money = new Money("10000", new Currency('USD'));

        $json = (new MoneyToJson)($money);

        $data = json_decode($json, true);
        $I->assertSame(10000, $data['amount']);
        $I->assertSame('USD', $data['currency']);
    }
}
