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
}