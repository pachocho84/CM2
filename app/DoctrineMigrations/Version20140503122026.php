<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140503122026 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE user ADD biography_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE user ADD CONSTRAINT FK_8D93D64962283C10 FOREIGN KEY (biography_id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_8D93D64962283C10 ON user (biography_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE user DROP FOREIGN KEY FK_8D93D64962283C10");
        $this->addSql("DROP INDEX UNIQ_8D93D64962283C10 ON user");
        $this->addSql("ALTER TABLE user DROP biography_id");
    }
}
