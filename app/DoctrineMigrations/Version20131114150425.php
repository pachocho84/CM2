<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131114150425 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE biography (id INT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_E3B3665CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE biography ADD CONSTRAINT FK_E3B3665CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE biography ADD CONSTRAINT FK_E3B3665CBF396750 FOREIGN KEY (id) REFERENCES entity (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE entity_translation CHANGE title title VARCHAR(150) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE biography");
        $this->addSql("ALTER TABLE entity_translation CHANGE title title VARCHAR(150) NOT NULL");
    }
}
