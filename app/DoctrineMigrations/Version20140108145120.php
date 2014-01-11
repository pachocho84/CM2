<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140108145120 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE homepage_box (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, page_id INT DEFAULT NULL, width SMALLINT NOT NULL, type SMALLINT NOT NULL, name VARCHAR(50) NOT NULL, leftSide SMALLINT NOT NULL, rightSide SMALLINT DEFAULT NULL, slug VARCHAR(255) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_57E6A8E12469DE2 (category_id), INDEX IDX_57E6A8EC4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE homepage_archive (id INT AUTO_INCREMENT NOT NULL, article_id INT NOT NULL, user_id INT NOT NULL, category_id INT NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_CCED39BA7294869C (article_id), INDEX IDX_CCED39BAA76ED395 (user_id), INDEX IDX_CCED39BA12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE homepage_category_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, singular VARCHAR(50) DEFAULT NULL, slug VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_8FFE75722C2AC5D3 (translatable_id), UNIQUE INDEX UNIQ_8FFE75722C2AC5D34180C698 (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE homepage_column (id INT AUTO_INCREMENT NOT NULL, row_id INT NOT NULL, user_id INT NOT NULL, archive_id INT DEFAULT NULL, box_id INT DEFAULT NULL, type SMALLINT NOT NULL, `order` INT NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_1D0E883A269F2 (row_id), INDEX IDX_1D0E8A76ED395 (user_id), INDEX IDX_1D0E82956195F (archive_id), INDEX IDX_1D0E8D8177B3F (box_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE homepage_category (id INT AUTO_INCREMENT NOT NULL, editor_id INT NOT NULL, rubric TINYINT(1) NOT NULL, `update` VARCHAR(150) DEFAULT NULL, INDEX IDX_D4588D586995AC4C (editor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE homepage_row (id INT AUTO_INCREMENT NOT NULL, type SMALLINT NOT NULL, `order` INT NOT NULL, visible TINYINT(1) NOT NULL, createdAt DATETIME DEFAULT NULL, updatedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE homepage_box ADD CONSTRAINT FK_57E6A8E12469DE2 FOREIGN KEY (category_id) REFERENCES homepage_category (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_box ADD CONSTRAINT FK_57E6A8EC4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_archive ADD CONSTRAINT FK_CCED39BA7294869C FOREIGN KEY (article_id) REFERENCES Article (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_archive ADD CONSTRAINT FK_CCED39BAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_archive ADD CONSTRAINT FK_CCED39BA12469DE2 FOREIGN KEY (category_id) REFERENCES homepage_category (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_category_translation ADD CONSTRAINT FK_8FFE75722C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES homepage_category (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_column ADD CONSTRAINT FK_1D0E883A269F2 FOREIGN KEY (row_id) REFERENCES homepage_row (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_column ADD CONSTRAINT FK_1D0E8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE homepage_column ADD CONSTRAINT FK_1D0E82956195F FOREIGN KEY (archive_id) REFERENCES homepage_archive (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE homepage_column ADD CONSTRAINT FK_1D0E8D8177B3F FOREIGN KEY (box_id) REFERENCES homepage_box (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE homepage_category ADD CONSTRAINT FK_D4588D586995AC4C FOREIGN KEY (editor_id) REFERENCES user (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE homepage_column DROP FOREIGN KEY FK_1D0E8D8177B3F");
        $this->addSql("ALTER TABLE homepage_column DROP FOREIGN KEY FK_1D0E82956195F");
        $this->addSql("ALTER TABLE homepage_box DROP FOREIGN KEY FK_57E6A8E12469DE2");
        $this->addSql("ALTER TABLE homepage_archive DROP FOREIGN KEY FK_CCED39BA12469DE2");
        $this->addSql("ALTER TABLE homepage_category_translation DROP FOREIGN KEY FK_8FFE75722C2AC5D3");
        $this->addSql("ALTER TABLE homepage_column DROP FOREIGN KEY FK_1D0E883A269F2");
        $this->addSql("DROP TABLE homepage_box");
        $this->addSql("DROP TABLE homepage_archive");
        $this->addSql("DROP TABLE homepage_category_translation");
        $this->addSql("DROP TABLE homepage_column");
        $this->addSql("DROP TABLE homepage_category");
        $this->addSql("DROP TABLE homepage_row");
    }
}
