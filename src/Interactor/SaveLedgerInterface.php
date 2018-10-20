<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor;

use IamPersistent\Ledger\Entity\Ledger;

interface SaveLedgerInterface
{
    public function saveLedger(Ledger $ledger): bool;
}