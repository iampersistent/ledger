<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor;

use IamPersistent\Ledger\Entity\Entry;
use IamPersistent\Ledger\Entity\Ledger;

final class AddEntryToLedger
{
    public function handle(Ledger $ledger, Entry $entry)
    {
        $ledger->addEntry($entry);
        (new CalculateBalance())->handle($ledger);
        $entry->setRunningBalance($ledger->getBalance());
    }
}
