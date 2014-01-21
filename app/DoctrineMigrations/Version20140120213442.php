<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140120213442 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE multimedia ADD entity_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE multimedia ADD CONSTRAINT FK_6131286381257D5D FOREIGN KEY (entity_id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_6131286381257D5D ON multimedia (entity_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE multimedia DROP FOREIGN KEY FK_6131286381257D5D");
        $this->addSql("DROP INDEX IDX_6131286381257D5D ON multimedia");
        $this->addSql("ALTER TABLE multimedia DROP entity_id");
    }
}
