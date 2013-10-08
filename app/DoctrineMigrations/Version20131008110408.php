<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131008110408 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE users_groups (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_FF8AB7E0A76ED395 (user_id), INDEX IDX_FF8AB7E0FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5A76ED395");
        $this->addSql("DROP INDEX IDX_6DC044C5A76ED395 ON `group`");
        $this->addSql("ALTER TABLE `group` DROP user_id");
        $this->addSql("ALTER TABLE image ADD user_id INT NOT NULL, ADD group_id INT DEFAULT NULL, ADD page_id INT DEFAULT NULL, CHANGE entity_id entity_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FC4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_C53D045FA76ED395 ON image (user_id)");
        $this->addSql("CREATE INDEX IDX_C53D045FFE54D947 ON image (group_id)");
        $this->addSql("CREATE INDEX IDX_C53D045FC4663E4 ON image (page_id)");
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D81257D5D");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D81257D5D FOREIGN KEY (entity_id) REFERENCES event (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE users_groups");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE `group` ADD user_id INT NOT NULL");
        $this->addSql("ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_6DC044C5A76ED395 ON `group` (user_id)");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FA76ED395");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FC4663E4");
        $this->addSql("DROP INDEX IDX_C53D045FA76ED395 ON image");
        $this->addSql("DROP INDEX IDX_C53D045FFE54D947 ON image");
        $this->addSql("DROP INDEX IDX_C53D045FC4663E4 ON image");
        $this->addSql("ALTER TABLE image DROP user_id, DROP group_id, DROP page_id, CHANGE entity_id entity_id INT NOT NULL");
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D81257D5D");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D81257D5D FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE");
    }
}
