<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Factory;

use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Entry;
use Money\Currency;
use Money\Money;

final class EntryFactory
{
    /**
     * @param \IamPersistent\Ledger\Entity\Item[] $items
     *
     * @return \IamPersistent\Ledger\Entity\Credit
     */
    public function createCreditFromItems(array $items): Credit
    {
        $credit = new Credit();
        $total = $this->addItems($credit, $items);
        $credit->setCredit($total);

        return $credit;
    }
    /**
     * @param \IamPersistent\Ledger\Entity\Item[] $items
     *
     * @return \IamPersistent\Ledger\Entity\Credit
     */
    public function createDebitFromItems(array $items): Debit
    {
        $debit = new Debit();
        $total = $this->addItems($debit, $items);
        $debit->setDebit($total);

        return $debit;
    }

    /**
     * @param \IamPersistent\Ledger\Entity\Entry $entry
     * @param \IamPersistent\Ledger\Entity\Item[] $items
     */
    private function addItems(Entry $entry, array $items): Money
    {
        $total = new Money(0, new Currency('USD'));
        foreach ($items as $item) {
            $entry->addItem($item);
            $total = $total->add($item->getTotal());
        }

        return $total;
    }
}
