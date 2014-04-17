<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140417113852 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE fan DROP FOREIGN KEY FK_65F77839FE54D947");
        $this->addSql("ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39FE54D947");
        $this->addSql("ALTER TABLE homepage_banner DROP FOREIGN KEY FK_12CFEF71FE54D947");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAB8BB39DD");
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DFE54D947");
        $this->addSql("ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FFE54D947");
        $this->addSql("DROP TABLE `group`");
        $this->addSql("DROP TABLE group_user");
        $this->addSql("DROP INDEX IDX_5A8A6C8DFE54D947 ON post");
        $this->addSql("ALTER TABLE post DROP group_id");
        $this->addSql("DROP INDEX IDX_BF5476CAB8BB39DD ON notification");
        $this->addSql("ALTER TABLE notification DROP from_group_id");
        $this->addSql("DROP INDEX IDX_12CFEF71FE54D947 ON homepage_banner");
        $this->addSql("ALTER TABLE homepage_banner DROP group_id");
        $this->addSql("ALTER TABLE user_tag DROP is_group");
        $this->addSql("DROP INDEX IDX_3B978F9FFE54D947 ON request");
        $this->addSql("ALTER TABLE request DROP group_id");
        $this->addSql("DROP INDEX IDX_C53D045FFE54D947 ON image");
        $this->addSql("ALTER TABLE image DROP group_id");
        $this->addSql("DROP INDEX IDX_65F77839FE54D947 ON fan");
        $this->addSql("ALTER TABLE fan DROP group_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, type SMALLINT NOT NULL, name VARCHAR(150) NOT NULL, description VARCHAR(250) NOT NULL, vip TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, img VARCHAR(100) DEFAULT NULL, img_offset NUMERIC(10, 2) DEFAULT NULL, cover_img VARCHAR(100) DEFAULT NULL, cover_img_offset NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_6DC044C561220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE group_user (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, group_id INT NOT NULL, admin TINYINT(1) NOT NULL, status SMALLINT NOT NULL, join_event SMALLINT NOT NULL, join_disc SMALLINT NOT NULL, join_article SMALLINT NOT NULL, user_tags LONGTEXT DEFAULT NULL COMMENT '(DC2Type:simple_array)', notification TINYINT(1) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_A4C98D39FE54D947 (group_id), INDEX IDX_A4C98D39A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C561220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE fan ADD group_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE fan ADD CONSTRAINT FK_65F77839FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_65F77839FE54D947 ON fan (group_id)");
        $this->addSql("ALTER TABLE homepage_banner ADD group_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE homepage_banner ADD CONSTRAINT FK_12CFEF71FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_12CFEF71FE54D947 ON homepage_banner (group_id)");
        $this->addSql("ALTER TABLE image ADD group_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_C53D045FFE54D947 ON image (group_id)");
        $this->addSql("ALTER TABLE notification ADD from_group_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB8BB39DD FOREIGN KEY (from_group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_BF5476CAB8BB39DD ON notification (from_group_id)");
        $this->addSql("ALTER TABLE post ADD group_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_5A8A6C8DFE54D947 ON post (group_id)");
        $this->addSql("ALTER TABLE request ADD group_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE request ADD CONSTRAINT FK_3B978F9FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_3B978F9FFE54D947 ON request (group_id)");
        $this->addSql("ALTER TABLE user_tag ADD is_group TINYINT(1) NOT NULL");
    }
}
