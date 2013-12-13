<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131212163311 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE disc_track CHANGE artists artists VARCHAR(150) NOT NULL");
        $this->addSql("ALTER TABLE disc ADD createdAt DATETIME DEFAULT NULL, ADD updatedAt DATETIME DEFAULT NULL, CHANGE authors authors VARCHAR(150) NOT NULL, CHANGE interpreters interpreters VARCHAR(150) NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE disc DROP createdAt, DROP updatedAt, CHANGE authors authors LONGTEXT NOT NULL COMMENT '(DC2Type:simple_array)', CHANGE interpreters interpreters LONGTEXT NOT NULL COMMENT '(DC2Type:simple_array)'");
        $this->addSql("ALTER TABLE disc_track CHANGE artists artists LONGTEXT NOT NULL COMMENT '(DC2Type:simple_array)'");
    }
}
