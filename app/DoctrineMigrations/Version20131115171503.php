<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131115171503 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE `group` CHANGE offset img_offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE page CHANGE offset img_offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE image CHANGE offset img_offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE user CHANGE offset img_offset SMALLINT DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE `group` CHANGE img_offset offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE image CHANGE img_offset offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE page CHANGE img_offset offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE user CHANGE img_offset offset SMALLINT DEFAULT NULL");
    }
}
