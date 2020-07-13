<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Entity;

use DateTime;
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

    /**
     * @return Entry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @return Entry[]
     */
    public function getEntriesByDate(DateTime $dateTime): array
    {
        $date = $dateTime->format('Y-m-d');
        $entries = [];
        foreach ($this->entries as $entry) {
            if ($date === $entry->getDate()->format('Y-m-d')) {
                $entries[] = $entry;
            }
        }

        return $entries;
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

    private function addEntry(Entry $entry): Ledger
    {
        $this->entries[$entry->getLine()] = $entry;

        return $this;
    }
}
