<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Interactor\DBal;

use DateTime;
use Doctrine\DBAL\Connection;
use IamPersistent\Ledger\Entity\Credit;
use IamPersistent\Ledger\Entity\Debit;
use IamPersistent\Ledger\Entity\Entry;
use IamPersistent\Ledger\Entity\Ledger;
use Money\Currency;
use Money\Money;

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
        $entryData = $statement->fetchAll();
        $entries = [];
        foreach ($entryData as $datum) {
            $entries[] = ($this->createEntry($datum));
        }
        $ledger->setEntries($entries);
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
        if (empty($data['running_balance'])) {
            $runningBalance = new Money(0, new Currency('USD'));
        } else {
            $runningBalance = (new JsonToMoney)($data['running_balance']);
        }
        $entry
            ->setDate(new DateTime($data['date']))
            ->setDescription($data['description'])
            ->setId($data['id'])
            ->setLine((int) $data['line'])
            ->setProductId($data['product_id'])
            ->setReferenceNumber($data['reference_number'])
            ->setRunningBalance($runningBalance)
            ->setType($data['type']);

        return $entry;
    }
}
