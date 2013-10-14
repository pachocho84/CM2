<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131014171718 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE group_user (group_id INT NOT NULL, user_id INT NOT NULL, admin TINYINT(1) NOT NULL, join_event SMALLINT NOT NULL, join_disc SMALLINT NOT NULL, join_article SMALLINT NOT NULL, notification TINYINT(1) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_A4C98D39FE54D947 (group_id), INDEX IDX_A4C98D39A76ED395 (user_id), PRIMARY KEY(group_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page_user (page_id INT NOT NULL, user_id INT NOT NULL, admin TINYINT(1) NOT NULL, join_event SMALLINT NOT NULL, join_disc SMALLINT NOT NULL, join_article SMALLINT NOT NULL, notification TINYINT(1) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_A57CA93C4663E4 (page_id), INDEX IDX_A57CA93A76ED395 (user_id), PRIMARY KEY(page_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)");
        $this->addSql("ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)");
        $this->addSql("ALTER TABLE page_user ADD CONSTRAINT FK_A57CA93C4663E4 FOREIGN KEY (page_id) REFERENCES page (id)");
        $this->addSql("ALTER TABLE page_user ADD CONSTRAINT FK_A57CA93A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)");
        $this->addSql("DROP TABLE users_groups");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE users_groups (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_FF8AB7E0A76ED395 (user_id), INDEX IDX_FF8AB7E0FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE group_user");
        $this->addSql("DROP TABLE page_user");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }
}
