<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131008214306 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE `group` ADD offset SMALLINT DEFAULT NULL, DROP img_offset, CHANGE cover_img cover_img VARCHAR(100) DEFAULT NULL, CHANGE cover_img_offset cover_img_offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page CHANGE img img VARCHAR(100) NOT NULL, CHANGE img_offset offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE post CHANGE type type SMALLINT NOT NULL");
        $this->addSql("ALTER TABLE user CHANGE img_offset offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE users_groups DROP FOREIGN KEY FK_FF8AB7E0FE54D947");
        $this->addSql("ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE `group` ADD img_offset SMALLINT NOT NULL, DROP offset, CHANGE cover_img cover_img VARCHAR(100) NOT NULL, CHANGE cover_img_offset cover_img_offset SMALLINT NOT NULL");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page CHANGE img img VARCHAR(100) DEFAULT NULL, CHANGE offset img_offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE post CHANGE type type VARCHAR(50) NOT NULL");
        $this->addSql("ALTER TABLE user CHANGE offset img_offset SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE users_groups DROP FOREIGN KEY FK_FF8AB7E0FE54D947");
        $this->addSql("ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }
}
