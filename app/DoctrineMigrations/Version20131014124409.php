<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131014124409 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE `group` ADD creator_id INT NOT NULL, CHANGE type_id type SMALLINT NOT NULL");
        $this->addSql("ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C561220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_6DC044C561220EA6 ON `group` (creator_id)");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page DROP FOREIGN KEY FK_140AB620A76ED395");
        $this->addSql("DROP INDEX IDX_140AB620A76ED395 ON page");
        $this->addSql("ALTER TABLE page ADD creator_id INT NOT NULL, ADD type SMALLINT NOT NULL, DROP user_id, DROP type_id");
        $this->addSql("ALTER TABLE page ADD CONSTRAINT FK_140AB62061220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_140AB62061220EA6 ON page (creator_id)");
        $this->addSql("ALTER TABLE users_groups DROP FOREIGN KEY FK_FF8AB7E0FE54D947");
        $this->addSql("ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C561220EA6");
        $this->addSql("DROP INDEX IDX_6DC044C561220EA6 ON `group`");
        $this->addSql("ALTER TABLE `group` DROP creator_id, CHANGE type type_id SMALLINT NOT NULL");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page DROP FOREIGN KEY FK_140AB62061220EA6");
        $this->addSql("DROP INDEX IDX_140AB62061220EA6 ON page");
        $this->addSql("ALTER TABLE page ADD type_id INT NOT NULL, DROP type, CHANGE creator_id user_id INT NOT NULL");
        $this->addSql("ALTER TABLE page ADD CONSTRAINT FK_140AB620A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_140AB620A76ED395 ON page (user_id)");
        $this->addSql("ALTER TABLE users_groups DROP FOREIGN KEY FK_FF8AB7E0FE54D947");
        $this->addSql("ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }
}
