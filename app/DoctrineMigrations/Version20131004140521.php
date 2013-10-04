<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131004140521 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, img VARCHAR(100) NOT NULL, img_offset SMALLINT NOT NULL, cover_img VARCHAR(100) NOT NULL, cover_img_offset SMALLINT NOT NULL, sex TINYINT(1) NOT NULL, birth_date DATETIME NOT NULL, birth_date_visible TINYINT(1) NOT NULL, city_birth VARCHAR(50) NOT NULL, city_current VARCHAR(50) NOT NULL, newsletter TINYINT(1) NOT NULL, vip TINYINT(1) NOT NULL, notify_email TINYINT(1) NOT NULL, request_email TINYINT(1) NOT NULL, message_email TINYINT(1) NOT NULL, notes LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE user");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
    }
}
