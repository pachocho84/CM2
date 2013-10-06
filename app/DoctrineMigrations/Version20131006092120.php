<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131006092120 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, image_id INT DEFAULT NULL, user_id INT NOT NULL, comment LONGTEXT NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_9474526C4B89032C (post_id), INDEX IDX_9474526C3DA5256D (image_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type_id SMALLINT NOT NULL, name VARCHAR(150) NOT NULL, description VARCHAR(250) NOT NULL, img VARCHAR(100) NOT NULL, img_offset SMALLINT NOT NULL, cover_img VARCHAR(100) NOT NULL, cover_img_offset SMALLINT NOT NULL, vip TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_6DC044C5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, image_id INT DEFAULT NULL, user_id INT NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_AC6340B34B89032C (post_id), INDEX IDX_AC6340B33DA5256D (image_id), INDEX IDX_AC6340B3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, image_id INT DEFAULT NULL, user_id INT NOT NULL, from_user_id INT NOT NULL, type VARCHAR(50) NOT NULL, status TINYINT(1) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_BF5476CA4B89032C (post_id), INDEX IDX_BF5476CA3DA5256D (image_id), INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CA2130303A (from_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type_id INT NOT NULL, name VARCHAR(150) NOT NULL, description VARCHAR(250) NOT NULL, img VARCHAR(100) DEFAULT NULL, img_offset SMALLINT DEFAULT NULL, cover_img VARCHAR(100) DEFAULT NULL, cover_img_offset SMALLINT DEFAULT NULL, website VARCHAR(150) NOT NULL, vip TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_140AB620A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, from_user_id INT NOT NULL, entity_id INT DEFAULT NULL, object VARCHAR(50) NOT NULL, object_id SMALLINT NOT NULL, status SMALLINT NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_F42AB603A76ED395 (user_id), INDEX IDX_F42AB6032130303A (from_user_id), INDEX IDX_F42AB60381257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE comment ADD CONSTRAINT FK_9474526C3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B34B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B33DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA2130303A FOREIGN KEY (from_user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page ADD CONSTRAINT FK_140AB620A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Request ADD CONSTRAINT FK_F42AB603A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Request ADD CONSTRAINT FK_F42AB6032130303A FOREIGN KEY (from_user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Request ADD CONSTRAINT FK_F42AB60381257D5D FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE Post ADD createdAt DATETIME DEFAULT NULL, ADD updatedAt DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE comment");
        $this->addSql("DROP TABLE `group`");
        $this->addSql("DROP TABLE `like`");
        $this->addSql("DROP TABLE notification");
        $this->addSql("DROP TABLE page");
        $this->addSql("DROP TABLE Request");
        $this->addSql("ALTER TABLE post DROP createdAt, DROP updatedAt");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
    }
}
