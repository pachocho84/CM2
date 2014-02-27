<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140226180827 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE education (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, school VARCHAR(150) NOT NULL, course VARCHAR(150) DEFAULT NULL, teacher VARCHAR(100) DEFAULT NULL, date_from DATE DEFAULT NULL, date_to DATE DEFAULT NULL, mark SMALLINT DEFAULT NULL, mark_scale SMALLINT DEFAULT NULL, laude TINYINT(1) DEFAULT NULL, honour TINYINT(1) DEFAULT NULL, course_type SMALLINT DEFAULT NULL, degree_type SMALLINT DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_DB0A5ED2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE relation_type DROP FOREIGN KEY FK_3BF454A4A76ED395");
        $this->addSql("DROP INDEX IDX_3BF454A4A76ED395 ON relation_type");
        $this->addSql("ALTER TABLE relation_type DROP user_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE education");
        $this->addSql("ALTER TABLE relation_type ADD user_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE relation_type ADD CONSTRAINT FK_3BF454A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_3BF454A4A76ED395 ON relation_type (user_id)");
    }
}
