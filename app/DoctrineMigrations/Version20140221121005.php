<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140221121005 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE relation ADD inverse INT DEFAULT NULL");
        $this->addSql("ALTER TABLE relation ADD CONSTRAINT FK_62894749A569A9E0 FOREIGN KEY (inverse) REFERENCES relation (id) ON DELETE CASCADE");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_62894749A569A9E0 ON relation (inverse)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE relation DROP FOREIGN KEY FK_62894749A569A9E0");
        $this->addSql("DROP INDEX UNIQ_62894749A569A9E0 ON relation");
        $this->addSql("ALTER TABLE relation DROP inverse");
    }
}
