<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131025162353 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE notification ADD from_group_id INT DEFAULT NULL, ADD from_page_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB8BB39DD FOREIGN KEY (from_group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA8A18804B FOREIGN KEY (from_page_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_BF5476CAB8BB39DD ON notification (from_group_id)");
        $this->addSql("CREATE INDEX IDX_BF5476CA8A18804B ON notification (from_page_id)");
        $this->addSql("ALTER TABLE request ADD from_group_id INT DEFAULT NULL, ADD from_page_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE request ADD CONSTRAINT FK_3B978F9FB8BB39DD FOREIGN KEY (from_group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE request ADD CONSTRAINT FK_3B978F9F8A18804B FOREIGN KEY (from_page_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_3B978F9FB8BB39DD ON request (from_group_id)");
        $this->addSql("CREATE INDEX IDX_3B978F9F8A18804B ON request (from_page_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAB8BB39DD");
        $this->addSql("ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA8A18804B");
        $this->addSql("DROP INDEX IDX_BF5476CAB8BB39DD ON notification");
        $this->addSql("DROP INDEX IDX_BF5476CA8A18804B ON notification");
        $this->addSql("ALTER TABLE notification DROP from_group_id, DROP from_page_id");
        $this->addSql("ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FB8BB39DD");
        $this->addSql("ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F8A18804B");
        $this->addSql("DROP INDEX IDX_3B978F9FB8BB39DD ON request");
        $this->addSql("DROP INDEX IDX_3B978F9F8A18804B ON request");
        $this->addSql("ALTER TABLE request DROP from_group_id, DROP from_page_id");
    }
}
