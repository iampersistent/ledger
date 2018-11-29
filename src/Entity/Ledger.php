<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Entity;

use Money\Money;

final class Ledger
{
    /** @var Money */
    private $balance;
    /** @var Entry[] */
    private $entries = [];
    /** @var mixed */
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

    public function addEntry(Entry $entry): Ledger
    {
        if (null === $line = $entry->getLine()) {
            $line = count($this->entries) + 1;
            $entry->setLine($line);
        }

        $this->entries[$line] = $entry;

        return $this;
    }

    /**
     * @return Entry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getEntryByReferenceNumber(string $referenceNumber): ?Entry
    {
        foreach ($this->entries as $entry) {
            if ($referenceNumber === $entry->getReferenceNumber()) {
                return $entry;
            }
        }

        return null;
    }

    public function setEntries(array $entries): Ledger
    {
        $this->entries = [];
        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Ledger
    {
        $this->id = $id;

        return $this;
    }
}