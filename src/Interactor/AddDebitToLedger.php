<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor;

use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Ledger;

final class AddDebitToLedger
{
    public function handle(Ledger $ledger, Debit $debit)
    {
        (new AddEntryToLedger())->handle($ledger, $debit);
    }
}