<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor\DBal;

use Doctrine\DBAL\Connection;
use IamPersistent\Ledger\Entity\Ledger;
use IamPersistent\Ledger\Interactor\CalculateBalance;
use IamPersistent\Ledger\Interactor\SaveLedgerInterface;

class SaveLedger implements SaveLedgerInterface
{
    public function __construct(
        protected Connection $connection,
        protected FindLedgerById $findLedgerById,
        protected SaveLedgerEntry $saveEntry,
    ) {}

    public function save(Ledger $ledger): bool
    {
        if (null === $ledger->getId()) {
            $this->insertLedger($ledger);
        }

        (new CalculateBalance)->handle($ledger);
        $this->updateLedger($ledger);

        $entries = $ledger->getEntries();
        foreach ($entries as $entry) {
            $this->saveEntry->save($ledger, $entry);
        }

        return true;
    }

    protected function insertLedger(Ledger $ledger)
    {
        $data = [
            'balance' => (new MoneyToJson)($ledger->getBalance()),
        ];
        $response = $this->connection->insert('ledgers', $data);
        if (1 === $response) {
            $id = $this->connection->lastInsertId();
            $ledger->setId($id);
        } else {

        }
    }

    protected function updateLedger(Ledger $ledger)
    {
        $data = [
            'balance' => (new MoneyToJson)($ledger->getBalance()),
        ];
        $identifier = [
            'id' => (int) $ledger->getId(),
        ];
        $response = $this->connection->update('ledgers', $data, $identifier);
        if (1 !== $response) {

        }
    }

}
