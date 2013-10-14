<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131014184329 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39FE54D947");
        $this->addSql("ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE post ADD page_id INT NOT NULL, ADD group_id INT NOT NULL");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_5A8A6C8DC4663E4 ON post (page_id)");
        $this->addSql("CREATE INDEX IDX_5A8A6C8DFE54D947 ON post (group_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE event_date CHANGE start start DATETIME NOT NULL, CHANGE end end DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39FE54D947");
        $this->addSql("ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)");
        $this->addSql("ALTER TABLE image DROP FOREIGN KEY FK_C53D045FFE54D947");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC4663E4");
        $this->addSql("ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DFE54D947");
        $this->addSql("DROP INDEX IDX_5A8A6C8DC4663E4 ON post");
        $this->addSql("DROP INDEX IDX_5A8A6C8DFE54D947 ON post");
        $this->addSql("ALTER TABLE post DROP page_id, DROP group_id");
    }
}
