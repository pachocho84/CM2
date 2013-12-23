<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131223151559 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE Article (id INT NOT NULL, source VARCHAR(100) NOT NULL, date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE Article ADD CONSTRAINT FK_CD8737FABF396750 FOREIGN KEY (id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE disc DROP createdAt, DROP updatedAt");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE Article");
        $this->addSql("ALTER TABLE disc ADD createdAt DATETIME DEFAULT NULL, ADD updatedAt DATETIME DEFAULT NULL");
    }
}
