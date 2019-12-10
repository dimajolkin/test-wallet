<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 */
final class Version20191207193233 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Init currency';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO currency(name, symbol, is_root, ration) VALUES ('USD', '$', 1, 100), ('RUB', 'р', 0, 100)");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM currency");
    }
}
