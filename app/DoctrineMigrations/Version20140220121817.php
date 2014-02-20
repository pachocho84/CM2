<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140220121817 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE relation_type (id INT AUTO_INCREMENT NOT NULL, inverse_type INT DEFAULT NULL, user_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_3BF454A45D3A0E61 (inverse_type), INDEX IDX_3BF454A4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE relation_type ADD CONSTRAINT FK_3BF454A45D3A0E61 FOREIGN KEY (inverse_type) REFERENCES relation_type (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE relation_type ADD CONSTRAINT FK_3BF454A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE relation ADD relation_type INT NOT NULL, DROP type, CHANGE accepted accepted SMALLINT NOT NULL");
        $this->addSql("ALTER TABLE relation ADD CONSTRAINT FK_628947493BF454A4 FOREIGN KEY (relation_type) REFERENCES relation_type (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_628947493BF454A4 ON relation (relation_type)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE relation DROP FOREIGN KEY FK_628947493BF454A4");
        $this->addSql("ALTER TABLE relation_type DROP FOREIGN KEY FK_3BF454A45D3A0E61");
        $this->addSql("DROP TABLE relation_type");
        $this->addSql("DROP INDEX IDX_628947493BF454A4 ON relation");
        $this->addSql("ALTER TABLE relation ADD type SMALLINT NOT NULL, DROP relation_type, CHANGE accepted accepted TINYINT(1) NOT NULL");
    }
}
