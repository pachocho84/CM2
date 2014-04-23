<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140423130159 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE page_tag DROP FOREIGN KEY FK_CF36BF125D0E3800");
        $this->addSql("DROP INDEX IDX_CF36BF125D0E3800 ON page_tag");
        $this->addSql("ALTER TABLE page_tag CHANGE page_user_id page_id INT NOT NULL");
        $this->addSql("ALTER TABLE page_tag ADD CONSTRAINT FK_CF36BF12C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_CF36BF12C4663E4 ON page_tag (page_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE page_tag DROP FOREIGN KEY FK_CF36BF12C4663E4");
        $this->addSql("DROP INDEX IDX_CF36BF12C4663E4 ON page_tag");
        $this->addSql("ALTER TABLE page_tag CHANGE page_id page_user_id INT NOT NULL");
        $this->addSql("ALTER TABLE page_tag ADD CONSTRAINT FK_CF36BF125D0E3800 FOREIGN KEY (page_user_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_CF36BF125D0E3800 ON page_tag (page_user_id)");
    }
}
