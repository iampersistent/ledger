<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use PDO;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class Functional extends \Codeception\Module
{
    /** @var Connection */
    private $connection;
    /** @var PDO */
    private $pdo;

    public function closeDatabase()
    {
        $this->connection->close();
        $this->connection = null;
        $this->pdo = null;
    }

    public function setUpDatabase()
    {
        $configFile = __DIR__ . '/../../../phinx.yml';
        $configArray = yaml_parse_file($configFile);
        $configArray['paths']['migrations'] = __DIR__ . '/../../../db/dbal/migrations';
        $configArray['environments']['test'] = [
            'adapter'    => 'sqlite',
            'connection' => $this->getPDO()
        ];
        $config = new Config($configArray);
        $manager = new Manager($config, new StringInput(' '), new NullOutput());
        $manager->migrate('test');
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

    public function getDBalConnection()
    {
        if (! $this->connection) {
            $params = [
                'pdo' => $this->getPDO(),
            ];
            $driver = new Driver();

            $this->connection = new Connection($params, $driver);
        }

        return $this->connection;
    }

    public function getPDO(): PDO
    {
        if (! $this->pdo) {
            $this->pdo = new PDO(
                'sqlite::memory:', null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            );
        }

        return $this->pdo;
    }
}
