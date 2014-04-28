<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140428144558 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE page ADD biography_id INT DEFAULT NULL, DROP description");
        $this->addSql("ALTER TABLE page ADD CONSTRAINT FK_140AB62062283C10 FOREIGN KEY (biography_id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_140AB62062283C10 ON page (biography_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE page DROP FOREIGN KEY FK_140AB62062283C10");
        $this->addSql("DROP INDEX UNIQ_140AB62062283C10 ON page");
        $this->addSql("ALTER TABLE page ADD description VARCHAR(250) NOT NULL, DROP biography_id");
    }
}
