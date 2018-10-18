<?php
declare(strict_types=1);

namespace Tests\Unit\Entity;

use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Ledger;
use UnitTester;

class LedgerCest
{
    public function testAddEntry(UnitTester $I)
    {
        $entry1 = (new Credit());
        $ledger = (new Ledger())
            ->addEntry($entry1);

        $I->assertSame(1, $entry1->getLine());

        $entry2 = (new Credit());
        $ledger->addEntry($entry2);

        $I->assertSame(2, $entry2->getLine());
    }

    public function testSetEntries(UnitTester $I)
    {
        $ledger = (new Ledger())
            ->setEntries($this->entries());

        $ledgerEntries = $ledger->getEntries();
        foreach ($ledgerEntries as $key => $entry) {
            $I->assertSame($key, $entry->getLine());
        }
    }

    private function entries(): array
    {
        $entries = [];
        $entries[] = (new Credit())
            ->setLine(3);
        $entries[] = (new Credit())
            ->setLine(1);
        $entries[] = (new Debit())
            ->setLine(2);

        return $entries;
    }
}
