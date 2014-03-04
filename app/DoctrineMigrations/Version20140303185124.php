<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140303185124 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE homepage_column DROP FOREIGN KEY FK_1D0E883A269F2");
        $this->addSql("CREATE TABLE homepage_banner (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, group_id INT DEFAULT NULL, page_id INT DEFAULT NULL, img VARCHAR(50) NOT NULL, img_alt VARCHAR(250) NOT NULL, img_href VARCHAR(250) NOT NULL, position SMALLINT NOT NULL, visible_from DATE NOT NULL, visible_to DATE NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_12CFEF71A76ED395 (user_id), INDEX IDX_12CFEF71FE54D947 (group_id), INDEX IDX_12CFEF71C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE homepage_banner ADD CONSTRAINT FK_12CFEF71A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_banner ADD CONSTRAINT FK_12CFEF71FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_banner ADD CONSTRAINT FK_12CFEF71C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE homepage_column");
        $this->addSql("DROP TABLE homepage_row");
        $this->addSql("ALTER TABLE homepage_box ADD logo VARCHAR(50) DEFAULT NULL, ADD colour VARCHAR(50) DEFAULT NULL, ADD position SMALLINT NOT NULL, ADD visible_from DATE NOT NULL, ADD visible_to DATE NOT NULL, DROP width, DROP leftSide, DROP rightSide, DROP slug, CHANGE name name VARCHAR(50) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE homepage_column (id INT AUTO_INCREMENT NOT NULL, box_id INT DEFAULT NULL, archive_id INT DEFAULT NULL, row_id INT NOT NULL, user_id INT NOT NULL, type SMALLINT NOT NULL, `order` INT NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_1D0E883A269F2 (row_id), INDEX IDX_1D0E8A76ED395 (user_id), INDEX IDX_1D0E82956195F (archive_id), INDEX IDX_1D0E8D8177B3F (box_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE homepage_row (id INT AUTO_INCREMENT NOT NULL, type SMALLINT NOT NULL, `order` INT NOT NULL, visible TINYINT(1) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE homepage_column ADD CONSTRAINT FK_1D0E8D8177B3F FOREIGN KEY (box_id) REFERENCES homepage_box (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE homepage_column ADD CONSTRAINT FK_1D0E82956195F FOREIGN KEY (archive_id) REFERENCES homepage_archive (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE homepage_column ADD CONSTRAINT FK_1D0E883A269F2 FOREIGN KEY (row_id) REFERENCES homepage_row (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_column ADD CONSTRAINT FK_1D0E8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE homepage_banner");
        $this->addSql("ALTER TABLE homepage_box ADD leftSide SMALLINT NOT NULL, ADD rightSide SMALLINT DEFAULT NULL, ADD slug VARCHAR(255) NOT NULL, DROP logo, DROP colour, DROP visible_from, DROP visible_to, CHANGE name name VARCHAR(50) NOT NULL, CHANGE position width SMALLINT NOT NULL");
    }
}
