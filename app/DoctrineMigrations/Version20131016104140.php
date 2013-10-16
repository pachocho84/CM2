<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131016104140 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE user_user_tag (user_id INT NOT NULL, user_tag_id INT NOT NULL, `order` SMALLINT DEFAULT NULL, INDEX IDX_8123208A76ED395 (user_id), INDEX IDX_8123208DF80782C (user_tag_id), PRIMARY KEY(user_id, user_tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE user_user_tag ADD CONSTRAINT FK_8123208A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)");
        $this->addSql("ALTER TABLE user_user_tag ADD CONSTRAINT FK_8123208DF80782C FOREIGN KEY (user_tag_id) REFERENCES user_tag (id)");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39FE54D947");
        $this->addSql("ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DFE54D947");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE user_user_tag");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39FE54D947");
        $this->addSql("ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DFE54D947");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }
}
