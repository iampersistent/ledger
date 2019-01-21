<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor\DBal;

use Money\Money;

final class MoneyToJson
{
    public function __invoke(Money $money): string
    {
        $json = [
            'amount'   => (int)$money->getAmount(),
            'currency' => $money->getCurrency(),
        ];

        return json_encode($json);
    }
}