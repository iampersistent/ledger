<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor\DBal;

use DateTime;
use Doctrine\DBAL\Connection;
use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Entry;
use IamPersistent\Ledger\Entity\Ledger;

final class FindLedgerById
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find($id): ?Ledger
    {
        $statement = $this->connection->executeQuery("SELECT * FROM ledgers WHERE id = $id");
        $ledgerData = $statement->fetch();
        if (empty($ledgerData)) {
            return null;
        }
        $ledger = (new Ledger())
            ->setBalance((new JsonToMoney)($ledgerData['balance']))
            ->setId($id);
        $this->addEntries($ledger);

        return $ledger;
    }

    private function addEntries(Ledger $ledger)
    {
        $sql = 'SELECT * FROM ledger_entries WHERE ledger_id = ' . $ledger->getId() .
            ' ORDER BY line ASC';
        $statement = $this->connection->executeQuery($sql);
        $entries = $statement->fetchAll();
        foreach ($entries as $entry) {
            $ledger->addEntry($this->createEntry($entry));
        }
    }

    private function createEntry(array $data): Entry
    {
        if (null !== $data['credit']) {
            $entry = (new Credit())
                ->setCredit((new JsonToMoney)($data['credit']));
        } else {
            $entry = (new Debit())
                ->setDebit((new JsonToMoney)($data['debit']));
        }
        $entry
            ->setDate(new DateTime($data['date']))
            ->setDescription($data['description'])
            ->setId($data['id'])
            ->setLine((int) $data['line'])
            ->setReferenceNumber($data['reference_number'])
            ->setType($data['type']);

        return $entry;
    }
}
