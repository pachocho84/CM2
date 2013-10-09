<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131009091857 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE entity_user (entity_id INT NOT NULL, user_id INT NOT NULL, admin TINYINT(1) NOT NULL, status SMALLINT NOT NULL, notification TINYINT(1) NOT NULL, INDEX IDX_C55F6F6281257D5D (entity_id), INDEX IDX_C55F6F62A76ED395 (user_id), PRIMARY KEY(entity_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE entity_user ADD CONSTRAINT FK_C55F6F6281257D5D FOREIGN KEY (entity_id) REFERENCES entity (id)");
        $this->addSql("ALTER TABLE entity_user ADD CONSTRAINT FK_C55F6F62A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)");
        $this->addSql("DROP TABLE users_entities");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE users_groups DROP FOREIGN KEY FK_FF8AB7E0FE54D947");
        $this->addSql("ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE users_entities (user_id INT NOT NULL, entity_id INT NOT NULL, INDEX IDX_2BAD4F3FA76ED395 (user_id), INDEX IDX_2BAD4F3F81257D5D (entity_id), PRIMARY KEY(user_id, entity_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE users_entities ADD CONSTRAINT FK_2BAD4F3F81257D5D FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE users_entities ADD CONSTRAINT FK_2BAD4F3FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE entity_user");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE users_groups DROP FOREIGN KEY FK_FF8AB7E0FE54D947");
        $this->addSql("ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }
}
