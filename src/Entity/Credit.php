<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Entity;

use Money\Money;

class Credit extends Entry
{
    /** @var Money */
    protected $credit;

    public function getCredit(): Money
    {
        return $this->credit;
    }

    public function setCredit(Money $credit): Credit
    {
        $this->credit = $credit;

        return $this;
    }

    public function isCredit(): bool
    {
        return true;
    }
}
