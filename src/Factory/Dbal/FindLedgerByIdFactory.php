<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Factory\Dbal;

use Doctrine\DBAL\Connection;
use IamPersistent\Ledger\Interactor\DBal\FindLedgerById;
use Psr\Container\ContainerInterface;

final class FindLedgerByIdFactory
{
    public function __invoke(ContainerInterface $container): FindLedgerById
    {
        $connection = $container->get(Connection::class);

        return new FindLedgerById($connection);
    }
}