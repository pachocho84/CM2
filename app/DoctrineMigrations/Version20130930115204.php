<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130930115204 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE entity (id INT AUTO_INCREMENT NOT NULL, visible TINYINT(1) DEFAULT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE entity_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(150) NOT NULL, subtitle VARCHAR(250) DEFAULT NULL, extract LONGTEXT DEFAULT NULL, text LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_36531FBC2C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_36531FBC2C2AC5D34180C698 (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE event (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE event_date (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, start DATETIME NOT NULL, end DATETIME DEFAULT NULL, location VARCHAR(150) NOT NULL, address VARCHAR(150) NOT NULL, coordinates VARCHAR(150) NOT NULL, INDEX IDX_B5557BD171F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE entity_translation ADD CONSTRAINT FK_36531FBC2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7BF396750 FOREIGN KEY (id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE event_date ADD CONSTRAINT FK_B5557BD171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE entity_translation DROP FOREIGN KEY FK_36531FBC2C2AC5D3");
        $this->addSql("ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7BF396750");
        $this->addSql("ALTER TABLE event_date DROP FOREIGN KEY FK_B5557BD171F7E88B");
        $this->addSql("DROP TABLE entity");
        $this->addSql("DROP TABLE entity_translation");
        $this->addSql("DROP TABLE event");
        $this->addSql("DROP TABLE event_date");
    }
}
