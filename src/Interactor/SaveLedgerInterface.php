<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor;

use IamPersistent\Ledger\Entity\Ledger;

interface SaveLedgerInterface
{
    public function save(Ledger $ledger): bool;
}