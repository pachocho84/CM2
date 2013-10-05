<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131004182617 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE Post (id INT AUTO_INCREMENT NOT NULL, entity_id INT DEFAULT NULL, user_id INT NOT NULL, type VARCHAR(50) NOT NULL, object VARCHAR(50) NOT NULL, object_ids LONGTEXT NOT NULL COMMENT '(DC2Type:simple_array)', INDEX IDX_FAB8C3B381257D5D (entity_id), INDEX IDX_FAB8C3B3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE Post ADD CONSTRAINT FK_FAB8C3B381257D5D FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Post ADD CONSTRAINT FK_FAB8C3B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE Post");
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
    }
}
