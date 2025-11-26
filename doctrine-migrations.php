<?php

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Entity Manager laden
$entityManager = app(\Doctrine\ORM\EntityManagerInterface::class);

// Migrations configuration
$config = new PhpFile(__DIR__ . '/migrations-db.php');

return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));
