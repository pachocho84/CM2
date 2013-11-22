<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131121185237 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FB8BB39DD");
        $this->addSql("DROP INDEX IDX_3B978F9FB8BB39DD ON request");
        $this->addSql("ALTER TABLE request CHANGE from_group_id group_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE request ADD CONSTRAINT FK_3B978F9FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_3B978F9FFE54D947 ON request (group_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FFE54D947");
        $this->addSql("DROP INDEX IDX_3B978F9FFE54D947 ON request");
        $this->addSql("ALTER TABLE request CHANGE group_id from_group_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE request ADD CONSTRAINT FK_3B978F9FB8BB39DD FOREIGN KEY (from_group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_3B978F9FB8BB39DD ON request (from_group_id)");
    }
}
