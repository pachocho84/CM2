<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131024145620 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP INDEX UNIQ_C55F6F6281257D5DA76ED395 ON entity_user");
        $this->addSql("DROP INDEX UNIQ_A4C98D39FE54D947A76ED395 ON group_user");
        $this->addSql("DROP INDEX UNIQ_A57CA93C4663E4A76ED395 ON page_user");
        $this->addSql("DROP INDEX UNIQ_8123208A76ED395DF80782C ON user_user_tag");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE UNIQUE INDEX UNIQ_C55F6F6281257D5DA76ED395 ON entity_user (entity_id, user_id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_A4C98D39FE54D947A76ED395 ON group_user (group_id, user_id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_A57CA93C4663E4A76ED395 ON page_user (page_id, user_id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_8123208A76ED395DF80782C ON user_user_tag (user_id, user_tag_id)");
    }
}
