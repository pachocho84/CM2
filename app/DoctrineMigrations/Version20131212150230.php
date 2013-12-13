<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131212150230 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE disc_track (id INT AUTO_INCREMENT NOT NULL, disc_id INT NOT NULL, number SMALLINT NOT NULL, composer VARCHAR(150) NOT NULL, title VARCHAR(150) NOT NULL, movement VARCHAR(150) NOT NULL, artists LONGTEXT NOT NULL COMMENT '(DC2Type:simple_array)', duration TIME NOT NULL, INDEX IDX_BEDE8E11C38F37CA (disc_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE disc (id INT AUTO_INCREMENT NOT NULL, authors LONGTEXT NOT NULL COMMENT '(DC2Type:simple_array)', interpreters LONGTEXT NOT NULL COMMENT '(DC2Type:simple_array)', `label` VARCHAR(150) NOT NULL, year DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE disc_track ADD CONSTRAINT FK_BEDE8E11C38F37CA FOREIGN KEY (disc_id) REFERENCES disc (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE relation ADD createdAt DATETIME DEFAULT NULL, ADD updatedAt DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE disc_track DROP FOREIGN KEY FK_BEDE8E11C38F37CA");
        $this->addSql("DROP TABLE disc_track");
        $this->addSql("DROP TABLE disc");
        $this->addSql("ALTER TABLE relation DROP createdAt, DROP updatedAt");
    }
}
