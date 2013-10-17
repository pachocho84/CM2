<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131017110049 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D81257D5D");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D81257D5D FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D81257D5D");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D81257D5D FOREIGN KEY (entity_id) REFERENCES event (id) ON DELETE CASCADE");
    }
}
