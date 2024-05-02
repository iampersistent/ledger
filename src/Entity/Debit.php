<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Entity;

use Money\Money;

class Debit extends Entry
{
    /** @var Money */
    protected $debit;

    public function getDebit(): Money
    {
        return $this->debit;
    }

    public function setDebit(Money $debit): Debit
    {
        $this->debit = $debit;

        return $this;
    }

    public function isDebit(): bool
    {
        return true;
    }
}
