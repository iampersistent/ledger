<?php
declare(strict_types=1);

namespace Tests\Functional\Interactor;

use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Ledger;
use IamPersistent\Ledger\Interactor\AddEntryToLedger;
use IamPersistent\Ledger\Interactor\InsertEntryIntoPosition;
use FunctionalTester;
use Money\Currency;
use Money\Money;

class InsertEntryIntoPositionCest
{
    public function testInsert(FunctionalTester $I)
    {
        $ledger = $this->setUpLedger();

        $credit = (new Credit())
            ->setId(6)
            ->setDate(new \DateTime('2014-01-01'))
            ->setDescription('Balance Adjustment')
            ->setCredit(new Money(25000, new Currency('USD')));
        (new InsertEntryIntoPosition)->insert($ledger, $credit, 1);

        $this->assertEntries($I, $ledger);
        $I->assertEquals(Money::USD(8965), $ledger->getBalance());
    }

    private function assertEntries(FunctionalTester $I, Ledger $ledger)
    {
        $assertionMap = [
            1 => [
                'position' => 2,
                'runningBalance' => $this->money(4966),
            ],
            2 => [
                'position' => 3,
                'runningBalance' => $this->money(4105),
            ],
            3 => [
                'position' => 4,
                'runningBalance' => $this->money(3999),
            ],
            4 => [
                'position' => 5,
                'runningBalance' => $this->money(28999),
            ],
            5 => [
                'position' => 6,
                'runningBalance' => $this->money(8965),
            ],
            6 => [
                'position' => 1,
                'runningBalance' => $this->money(25000),
            ],
        ];
        $entries = $ledger->getEntries();
        foreach ($entries as $entry) {
            $assertion = $assertionMap[$entry->getId()];
            $I->assertEquals($assertion['position'], $entry->getLine());
            $I->assertEquals($assertion['runningBalance'], $entry->getRunningBalance());
        }
    }

    private function money($amount): Money
    {
        return new Money($amount, new Currency('USD'));
    }

    private function setUpLedger(): Ledger
    {
        $ledger = (new Ledger())
            ->setBalance($this->money(0));

        $debit = (new Debit())
            ->setDate(new \DateTime('2015-05-14'))
            ->setDescription('Gold Membership')
            ->setId(1)
            ->setDebit(new Money(20034, new Currency('USD')));
        (new AddEntryToLedger())->handle($ledger, $debit);
        $debit = (new Debit())
            ->setDate(new \DateTime('2015-01-02'))
            ->setDescription('USPS Priority')
            ->setId(2)
            ->setDebit(new Money(861, new Currency('USD')));
        (new AddEntryToLedger())->handle($ledger, $debit);
        $debit = (new Debit())
            ->setDate(new \DateTime('2015-01-15'))
            ->setDescription('Oversized Package')
            ->setId(3)
            ->setDebit(new Money(106, new Currency('USD')));
        (new AddEntryToLedger())->handle($ledger, $debit);
        $credit = (new Credit())
            ->setDate(new \DateTime('2015-05-14'))
            ->setDescription('Check 6711')
            ->setId(4)
            ->setCredit(new Money(25000, new Currency('USD')));
        (new AddEntryToLedger())->handle($ledger, $credit);
        $debit = (new Debit())
            ->setDate(new \DateTime('2015-05-14'))
            ->setDescription('Gold Membership')
            ->setId(5)
            ->setDebit(new Money(20034, new Currency('USD')));
        (new AddEntryToLedger())->handle($ledger, $debit);

        return $ledger;
    }
}
