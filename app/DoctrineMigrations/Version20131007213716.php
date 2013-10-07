<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131007213716 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, image_id INT DEFAULT NULL, user_id INT NOT NULL, comment LONGTEXT NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_9474526C4B89032C (post_id), INDEX IDX_9474526C3DA5256D (image_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE entity (id INT AUTO_INCREMENT NOT NULL, entity_category_id INT DEFAULT NULL, visible TINYINT(1) DEFAULT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_E284468907CC731 (entity_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE entity_category (id INT AUTO_INCREMENT NOT NULL, entity_type SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE entity_category_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, plural VARCHAR(50) NOT NULL, slug VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_B5F5DA392C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_B5F5DA392C2AC5D34180C698 (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE entity_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(150) NOT NULL, subtitle VARCHAR(250) DEFAULT NULL, extract LONGTEXT DEFAULT NULL, text LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_36531FBC2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_36531FBC2C2AC5D34180C698 (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE event (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE event_date (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, start DATETIME NOT NULL, end DATETIME DEFAULT NULL, location VARCHAR(150) NOT NULL, address VARCHAR(150) NOT NULL, coordinates VARCHAR(150) NOT NULL, INDEX IDX_B5557BD171F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type_id SMALLINT NOT NULL, name VARCHAR(150) NOT NULL, description VARCHAR(250) NOT NULL, img VARCHAR(100) NOT NULL, img_offset SMALLINT NOT NULL, cover_img VARCHAR(100) NOT NULL, cover_img_offset SMALLINT NOT NULL, vip TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_6DC044C5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, img VARCHAR(100) NOT NULL, offset SMALLINT DEFAULT NULL, main TINYINT(1) DEFAULT NULL, sequence SMALLINT DEFAULT NULL, text LONGTEXT DEFAULT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_C53D045F81257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, image_id INT DEFAULT NULL, user_id INT NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_AC6340B34B89032C (post_id), INDEX IDX_AC6340B33DA5256D (image_id), INDEX IDX_AC6340B3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, image_id INT DEFAULT NULL, user_id INT NOT NULL, from_user_id INT NOT NULL, type VARCHAR(50) NOT NULL, status TINYINT(1) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_BF5476CA4B89032C (post_id), INDEX IDX_BF5476CA3DA5256D (image_id), INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CA2130303A (from_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type_id INT NOT NULL, name VARCHAR(150) NOT NULL, description VARCHAR(250) NOT NULL, img VARCHAR(100) DEFAULT NULL, img_offset SMALLINT DEFAULT NULL, cover_img VARCHAR(100) DEFAULT NULL, cover_img_offset SMALLINT DEFAULT NULL, website VARCHAR(150) NOT NULL, vip TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_140AB620A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, entity_id INT DEFAULT NULL, user_id INT NOT NULL, type VARCHAR(50) NOT NULL, object VARCHAR(50) NOT NULL, object_ids LONGTEXT NOT NULL COMMENT '(DC2Type:simple_array)', createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_5A8A6C8D81257D5D (entity_id), INDEX IDX_5A8A6C8DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, from_user_id INT NOT NULL, entity_id INT DEFAULT NULL, object VARCHAR(50) NOT NULL, object_id SMALLINT NOT NULL, status SMALLINT NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_3B978F9FA76ED395 (user_id), INDEX IDX_3B978F9F2130303A (from_user_id), INDEX IDX_3B978F9F81257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, img VARCHAR(100) NOT NULL, img_offset SMALLINT DEFAULT NULL, cover_img VARCHAR(100) DEFAULT NULL, cover_img_offset SMALLINT DEFAULT NULL, sex TINYINT(1) NOT NULL, birth_date DATE NOT NULL, birth_date_visible TINYINT(1) NOT NULL, city_birth VARCHAR(50) NOT NULL, city_current VARCHAR(50) NOT NULL, newsletter TINYINT(1) NOT NULL, vip TINYINT(1) NOT NULL, notify_email TINYINT(1) NOT NULL, request_email TINYINT(1) NOT NULL, message_email TINYINT(1) NOT NULL, notes LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE comment ADD CONSTRAINT FK_9474526C3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE entity ADD CONSTRAINT FK_E284468907CC731 FOREIGN KEY (entity_category_id) REFERENCES entity_category (id)");
        $this->addSql("ALTER TABLE entity_category_translation ADD CONSTRAINT FK_B5F5DA392C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES entity_category (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE entity_translation ADD CONSTRAINT FK_36531FBC2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7BF396750 FOREIGN KEY (id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE event_date ADD CONSTRAINT FK_B5557BD171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045F81257D5D FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B34B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B33DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA2130303A FOREIGN KEY (from_user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page ADD CONSTRAINT FK_140AB620A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D81257D5D FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE request ADD CONSTRAINT FK_3B978F9FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE request ADD CONSTRAINT FK_3B978F9F2130303A FOREIGN KEY (from_user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE request ADD CONSTRAINT FK_3B978F9F81257D5D FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE entity_translation DROP FOREIGN KEY FK_36531FBC2C2AC5D3");
        $this->addSql("ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7BF396750");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045F81257D5D");
        $this->addSql("ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F81257D5D");
        $this->addSql("ALTER TABLE entity DROP FOREIGN KEY FK_E284468907CC731");
        $this->addSql("ALTER TABLE entity_category_translation DROP FOREIGN KEY FK_B5F5DA392C2AC5D3");
        $this->addSql("ALTER TABLE event_date DROP FOREIGN KEY FK_B5557BD171F7E88B");
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D81257D5D");
        $this->addSql("ALTER TABLE comment DROP FOREIGN KEY FK_9474526C3DA5256D");
        $this->addSql("ALTER TABLE like DROP FOREIGN KEY FK_AC6340B33DA5256D");
        $this->addSql("ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA3DA5256D");
        $this->addSql("ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C");
        $this->addSql("ALTER TABLE like DROP FOREIGN KEY FK_AC6340B34B89032C");
        $this->addSql("ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA4B89032C");
        $this->addSql("ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395");
        $this->addSql("ALTER TABLE group DROP FOREIGN KEY FK_6DC044C5A76ED395");
        $this->addSql("ALTER TABLE like DROP FOREIGN KEY FK_AC6340B3A76ED395");
        $this->addSql("ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395");
        $this->addSql("ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA2130303A");
        $this->addSql("ALTER TABLE page DROP FOREIGN KEY FK_140AB620A76ED395");
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395");
        $this->addSql("ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FA76ED395");
        $this->addSql("ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F2130303A");
        $this->addSql("DROP TABLE comment");
        $this->addSql("DROP TABLE entity");
        $this->addSql("DROP TABLE entity_category");
        $this->addSql("DROP TABLE entity_category_translation");
        $this->addSql("DROP TABLE entity_translation");
        $this->addSql("DROP TABLE event");
        $this->addSql("DROP TABLE event_date");
        $this->addSql("DROP TABLE `group`");
        $this->addSql("DROP TABLE image");
        $this->addSql("DROP TABLE `like`");
        $this->addSql("DROP TABLE notification");
        $this->addSql("DROP TABLE page");
        $this->addSql("DROP TABLE post");
        $this->addSql("DROP TABLE request");
        $this->addSql("DROP TABLE user");
    }
}
