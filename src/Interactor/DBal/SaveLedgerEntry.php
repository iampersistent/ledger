<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor\DBal;

use Doctrine\DBAL\Connection;
use IamPersistent\Ledger\Entity\Entry;
use IamPersistent\Ledger\Entity\Item;
use IamPersistent\Ledger\Entity\Ledger;

class SaveLedgerEntry
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

            return $this->insertItems($entry);
        }

        return false;
    }

    public function update(Ledger $ledger, Entry $entry): bool
    {
        $data = $this->prepData($ledger, $entry);
        $identifier = [
            'id' => (int)$entry->getId(),
        ];
        $response = $this->connection->update('ledger_entries', $data, $identifier);
        if (1 !== $response) {
            return false;
        }

        return true;
    }

    protected function prepData(Ledger $ledger, Entry $entry): array
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
            'product_id'       => $entry->getProductId(),
            'reference_number' => $entry->getReferenceNumber(),
            'running_balance'  => $moneyToJson($entry->getRunningBalance()),
            'type'             => $entry->getType(),
        ];
    }

    protected function prepItemData(Entry $entry, Item $item): array
    {
        $moneyToJson = new MoneyToJson();

        return [
            'amount'           => $moneyToJson($item->getAmount()),
            'description'      => $item->getDescription(),
            'entry_id'         => $entry->getId(),
            'product_id'       => $item->getProductId(),
            'reference_number' => $item->getReferenceNumber(),
            'taxes'            => $moneyToJson($item->getTaxes()),
            'total'            => $moneyToJson($item->getTotal()),
        ];
    }

    private function insertItems(Entry $entry): bool
    {
        foreach ($entry->getItems() as $item) {
            $data = $this->prepItemData($entry, $item);
            $response = $this->connection->insert('ledger_items', $data);
            $id = $this->connection->lastInsertId();
            $item->setId($id);
        }

        return true;
    }
}
