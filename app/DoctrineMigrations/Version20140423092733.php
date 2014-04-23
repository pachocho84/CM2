<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140423092733 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE page_tag (id INT AUTO_INCREMENT NOT NULL, page_user_id INT NOT NULL, tag_id INT NOT NULL, `order` SMALLINT DEFAULT NULL, INDEX IDX_CF36BF125D0E3800 (page_user_id), INDEX IDX_CF36BF12BAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE page_tag ADD CONSTRAINT FK_CF36BF125D0E3800 FOREIGN KEY (page_user_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page_tag ADD CONSTRAINT FK_CF36BF12BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE tag ADD is_page TINYINT(1) NOT NULL, DROP is_page_user, DROP is_entity_user");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE page_tag");
        $this->addSql("ALTER TABLE tag ADD is_entity_user TINYINT(1) NOT NULL, CHANGE is_page is_page_user TINYINT(1) NOT NULL");
    }
}
