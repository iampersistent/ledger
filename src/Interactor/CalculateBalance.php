<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor;

use IamPersistent\Ledger\Entity\Ledger;
use Money\Money;

final class CalculateBalance
{
    public function handle(Ledger $ledger)
    {
        $entries = $ledger->getEntries();
        $currency = $ledger->getBalance()->getCurrency();
        $balance = new Money(0, $currency);

        foreach ($entries as $entry) {
            if ($entry->isCredit()) {
                $balance = $balance->add($entry->getCredit());
            } else {
                $balance = $balance->subtract($entry->getDebit());
            }
        }

        $ledger->setBalance($balance);
    }
}