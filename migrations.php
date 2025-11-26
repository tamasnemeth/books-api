<?php

use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;

return (new PhpFile('migrations-db.php'))
    ->setAllOrNothing(true)
    ->setTransactional(true)
    ->setCheckDatabasePlatform(false)
    ->setMetadataStorageConfiguration(new \Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration());
