<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor;

use IamPersistent\Ledger\Entity\Entry;
use IamPersistent\Ledger\Entity\Ledger;

final class AddEntryToLedger
{
    public function handle(Ledger $ledger, Entry $entry)
    {
        if (!$entry->getLine()) {
            $this->setLineNumber($ledger, $entry);
        }
        $rc = new \ReflectionClass(Ledger::class);
        $addEntry = $rc->getMethod('addEntry');
        $addEntry->setAccessible(true);
        $addEntry->invoke($ledger, $entry);

        (new CalculateBalance())->handle($ledger);
    }

    private function setLineNumber(Ledger $ledger, Entry $entry)
    {
        $totalEntries = count($ledger->getEntries());
        $line = $totalEntries + 1;
        $entry->setLine($line);
    }
}
