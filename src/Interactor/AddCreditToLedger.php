<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor;

use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Ledger;

final class AddCreditToLedger
{
    public function handle(Ledger $ledger, Credit $credit)
    {
        (new AddEntryToLedger())->handle($ledger, $credit);
    }
}