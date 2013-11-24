<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131124155106 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F8A18804B");
        $this->addSql("DROP INDEX IDX_3B978F9F8A18804B ON request");
        $this->addSql("ALTER TABLE request CHANGE from_page_id page_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE request ADD CONSTRAINT FK_3B978F9FC4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_3B978F9FC4663E4 ON request (page_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FC4663E4");
        $this->addSql("DROP INDEX IDX_3B978F9FC4663E4 ON request");
        $this->addSql("ALTER TABLE request CHANGE page_id from_page_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE request ADD CONSTRAINT FK_3B978F9F8A18804B FOREIGN KEY (from_page_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_3B978F9F8A18804B ON request (from_page_id)");
    }
}
