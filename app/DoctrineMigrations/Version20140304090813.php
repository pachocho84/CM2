<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140304090813 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE homepage_archive DROP FOREIGN KEY FK_CCED39BAA76ED395");
        $this->addSql("DROP INDEX IDX_CCED39BAA76ED395 ON homepage_archive");
        $this->addSql("ALTER TABLE homepage_archive DROP user_id, DROP createdAt, DROP updatedAt");
        $this->addSql("ALTER TABLE homepage_banner DROP FOREIGN KEY FK_12CFEF71FE54D947");
        $this->addSql("ALTER TABLE homepage_banner ADD CONSTRAINT FK_12CFEF71FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE homepage_archive ADD user_id INT NOT NULL, ADD createdAt DATETIME DEFAULT NULL, ADD updatedAt DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE homepage_archive ADD CONSTRAINT FK_CCED39BAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_CCED39BAA76ED395 ON homepage_archive (user_id)");
        $this->addSql("ALTER TABLE homepage_banner DROP FOREIGN KEY FK_12CFEF71FE54D947");
        $this->addSql("ALTER TABLE homepage_banner ADD CONSTRAINT FK_12CFEF71FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
    }
}
