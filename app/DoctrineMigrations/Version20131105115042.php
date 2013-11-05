<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131105115042 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA3DA5256D");
        $this->addSql("DROP INDEX IDX_BF5476CA3DA5256D ON notification");
        $this->addSql("ALTER TABLE notification ADD object VARCHAR(50) NOT NULL, ADD object_id SMALLINT NOT NULL, DROP image_id, CHANGE type type SMALLINT NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE notification ADD image_id INT DEFAULT NULL, DROP object, DROP object_id, CHANGE type type VARCHAR(50) NOT NULL");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_BF5476CA3DA5256D ON notification (image_id)");
    }
}
