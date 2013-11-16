<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131116182328 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE fan (id INT AUTO_INCREMENT NOT NULL, from_user_id INT NOT NULL, user_id INT NOT NULL, group_id INT DEFAULT NULL, page_id INT DEFAULT NULL, fromUser_id INT NOT NULL, INDEX IDX_65F778392130303A (from_user_id), INDEX IDX_65F77839A76ED395 (user_id), INDEX IDX_65F77839FE54D947 (group_id), INDEX IDX_65F77839C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE fan ADD CONSTRAINT FK_65F778392130303A FOREIGN KEY (from_user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE fan ADD CONSTRAINT FK_65F77839A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE fan ADD CONSTRAINT FK_65F77839FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE fan ADD CONSTRAINT FK_65F77839C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE biography DROP user_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE fan");
        $this->addSql("ALTER TABLE biography ADD user_id INT NOT NULL");
    }
}
