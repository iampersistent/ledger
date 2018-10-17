<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Entity;

use Money\Money;

final class Ledger
{
    /** @var Money */
    private $balance;
    /** @var Entry[] */
    private $entries;
    /** @var int */
    private $id;

    public function getBalance(): Money
    {
        return $this->balance;
    }

    public function setBalance(Money $balance): Ledger
    {
        $this->balance = $balance;

        return $this;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function setEntries(array $entries): Ledger
    {
        $this->entries = $entries;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Ledger
    {
        $this->id = $id;

        return $this;
    }
}