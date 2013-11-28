<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131128165609 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE `group` CHANGE cover_img_offset cover_img_offset NUMERIC(10, 2) DEFAULT NULL, CHANGE img_offset img_offset NUMERIC(10, 2) DEFAULT NULL");
        $this->addSql("ALTER TABLE page CHANGE cover_img_offset cover_img_offset NUMERIC(10, 2) DEFAULT NULL, CHANGE img_offset img_offset NUMERIC(10, 2) DEFAULT NULL");
        $this->addSql("ALTER TABLE image CHANGE img_offset img_offset NUMERIC(10, 2) DEFAULT NULL");
        $this->addSql("ALTER TABLE user CHANGE cover_img_offset cover_img_offset NUMERIC(10, 2) DEFAULT NULL, CHANGE img_offset img_offset NUMERIC(10, 2) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE `group` CHANGE img_offset img_offset SMALLINT DEFAULT NULL, CHANGE cover_img_offset cover_img_offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE image CHANGE img_offset img_offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE page CHANGE img_offset img_offset SMALLINT DEFAULT NULL, CHANGE cover_img_offset cover_img_offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE user CHANGE img_offset img_offset SMALLINT DEFAULT NULL, CHANGE cover_img_offset cover_img_offset SMALLINT DEFAULT NULL");
    }
}
