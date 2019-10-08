<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor\DBal;

use Doctrine\DBAL\Connection;
use IamPersistent\Ledger\Entity\Entry;
use IamPersistent\Ledger\Entity\Ledger;

final class SaveLedgerEntry
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Ledger $ledger, Entry $entry): bool
    {
        if (null === $entry->getId()) {
            return $this->insert($ledger, $entry);
        }

        return $this->update($ledger, $entry);
    }

    public function insert(Ledger $ledger, Entry $entry): bool
    {
        $data = $this->prepData($ledger, $entry);
        $response = $this->connection->insert('ledger_entries', $data);
        if (1 === $response) {
            $id = $this->connection->lastInsertId();
            $entry->setId($id);

            return true;
        }
    }

    private function prepData(Ledger $ledger, Entry $entry): array
    {
        $credit = null;
        $debit = null;
        $moneyToJson = new MoneyToJson();
        if ($entry->isCredit()) {
            $credit = $moneyToJson($entry->getCredit());
        } else {
            $debit = $moneyToJson($entry->getDebit());
        }

        return [
            'credit'           => $credit,
            'debit'            => $debit,
            'date'             => $entry->getDate()->format('Y-m-d'),
            'description'      => $entry->getDescription(),
            'line'             => $entry->getLine(),
            'ledger_id'        => $ledger->getId(),
            'reference_number' => $entry->getReferenceNumber(),
            'running_balance'  => $moneyToJson($entry->getRunningBalance()),
            'type'             => $entry->getType(),
        ];
    }

    public function update(Ledger $ledger, Entry $entry): bool
    {
        $data = $this->prepData($ledger, $entry);
        $identifier = [
            'id' => (int) $entry->getId(),
        ];
        $response = $this->connection->update('ledger_entries', $data, $identifier);
        if (1 !== $response) {
            return false;
        }

        return true;
    }
}
