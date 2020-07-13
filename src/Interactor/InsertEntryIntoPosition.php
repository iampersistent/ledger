<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor;

use IamPersistent\Ledger\Entity\Entry;
use IamPersistent\Ledger\Entity\Ledger;

final class InsertEntryIntoPosition
{
    public function insert(Ledger $ledger, Entry $entry, int $position)
    {
        $entries = $this->getSortedEntries($ledger);
        $entry->setLine(null);
        array_splice($entries, $position-1, 0, [$entry]);
        $this->resetLineNumbers($entries);
        $ledger->setEntries($entries);

        (new CalculateBalance)->handle($ledger);
    }

    private function getSortedEntries(Ledger $ledger): array
    {
        $entries = [];
        foreach ($ledger->getEntries() as $entry) {
            $entries[$entry->getLine()] = $entry;
            $entry->setLine(null);
        }

        return $entries;
    }

    private function resetLineNumbers(array &$entries)
    {
        $lineNumber = 1;
        foreach ($entries as $entry) {
            $entry->setLine($lineNumber);
            $lineNumber++;
        }
    }
}
