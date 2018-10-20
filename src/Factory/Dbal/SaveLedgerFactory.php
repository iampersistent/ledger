<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Factory\Dbal;

use Doctrine\DBAL\Connection;
use IamPersistent\Ledger\Interactor\DBal\SaveLedger;
use Psr\Container\ContainerInterface;

final class SaveLedgerFactory
{
    public function __invoke(ContainerInterface $container): SaveLedger
    {
        $connection = $container->get(Connection::class);

        return new SaveLedger($connection);
    }
}