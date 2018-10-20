<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor\DBal;

use Money\Currency;
use Money\Money;

final class JsonToMoney
{
    public function __invoke(string $json): Money
    {
        $data = json_decode($json, true);

        return new Money($data['amount'], new Currency($data['currency']));
    }
}