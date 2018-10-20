<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor\DBal;

use Doctrine\DBAL\Connection;
use IamPersistent\Ledger\Entity\Ledger;
use IamPersistent\Ledger\Interactor\CalculateBalance;

final class SaveLedger
{
    /** @var Connection */
    private $connection;
    /** @var FindLedgerById */
    private $findLedgerById;
    /** @var InsertLedgerEntry */
    private $insertEntry;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->findLedgerById = new FindLedgerById($connection);
        $this->insertEntry = new InsertLedgerEntry($connection);
    }

    public function save(Ledger $ledger)
    {
        if (null === $ledger->getId()) {
            $this->insertLedger($ledger);
        } else {
            $this->mergePersistedLedger($ledger);
        }

        $entries = $ledger->getEntries();
        foreach ($entries as $entry) {
            if (null === $entry->getId()) {
                $this->insertEntry->insert($ledger, $entry);
            }
        }

        (new CalculateBalance)->handle($ledger);
        $this->updateLedger($ledger);
    }

    private function insertLedger(Ledger $ledger)
    {
        $data = [
            'balance' => json_encode($ledger->getBalance()),
        ];
        $response = $this->connection->insert('ledgers', $data);
        if (1 === $response) {
            $id = $this->connection->lastInsertId();
            $ledger->setId($id);
        } else {

        }
    }

    private function mergePersistedLedger(Ledger $ledger)
    {
        $persistedLedger = $this->findLedgerById->find($ledger->getId());
        $persistedEntries = $persistedLedger->getEntries();

        foreach ($persistedEntries as $entry) {
            $ledger->addEntry($entry);
        }
    }

    private function updateLedger(Ledger $ledger)
    {
        $data = [
            'balance' => json_encode($ledger->getBalance()),
        ];
        $identifier = [
            'id' => (int) $ledger->getId(),
        ];
        $response = $this->connection->update('ledgers', $data, $identifier);
        if (1 !== $response) {

        }
    }
}