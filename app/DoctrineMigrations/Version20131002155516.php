<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131002155516 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE entity_category (id INT AUTO_INCREMENT NOT NULL, entity_type SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE entity_category_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, plural VARCHAR(50) NOT NULL, slug VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_B5F5DA392C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_B5F5DA392C2AC5D34180C698 (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE entity_category_translation ADD CONSTRAINT FK_B5F5DA392C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES entity_category (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE entity ADD entity_category_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE entity ADD CONSTRAINT FK_E284468907CC731 FOREIGN KEY (entity_category_id) REFERENCES entity_category (id)");
        $this->addSql("CREATE INDEX IDX_E284468907CC731 ON entity (entity_category_id)");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE entity DROP FOREIGN KEY FK_E284468907CC731");
        $this->addSql("ALTER TABLE entity_category_translation DROP FOREIGN KEY FK_B5F5DA392C2AC5D3");
        $this->addSql("DROP TABLE entity_category");
        $this->addSql("DROP TABLE entity_category_translation");
        $this->addSql("DROP INDEX IDX_E284468907CC731 ON entity");
        $this->addSql("ALTER TABLE entity DROP entity_category_id");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
    }
}
