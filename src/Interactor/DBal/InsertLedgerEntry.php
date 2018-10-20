<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor\DBal;

use Doctrine\DBAL\Connection;
use IamPersistent\Ledger\Entity\Entry;
use IamPersistent\Ledger\Entity\Ledger;

final class InsertLedgerEntry
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function insert(Ledger $ledger, Entry $entry): bool
    {
        $credit = null;
        $debit = null;
        if ($entry->isCredit()) {
            $credit = json_encode($entry->getCredit());
        } else {
            $debit = json_encode($entry->getDebit());
        }
        $data = [
            'credit'           => $credit,
            'debit'            => $debit,
            'date'             => $entry->getDate()->format('Y-m-d'),
            'description'      => $entry->getDescription(),
            'line'             => $entry->getLine(),
            'ledger_id'        => $ledger->getId(),
            'reference_number' => $entry->getReferenceNumber(),
        ];
        $response = $this->connection->insert('ledger_entries', $data);
        if (1 === $response) {
            $id = $this->connection->lastInsertId();
            $entry->setId($id);

            return true;
        }
    }
}