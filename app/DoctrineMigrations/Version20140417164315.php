<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140417164315 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE page_user_tag (id INT AUTO_INCREMENT NOT NULL, page_user_id INT NOT NULL, tag_id INT NOT NULL, `order` SMALLINT DEFAULT NULL, INDEX IDX_99C842335D0E3800 (page_user_id), INDEX IDX_99C84233BAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE entity_user_tag (id INT AUTO_INCREMENT NOT NULL, entity_user_id INT NOT NULL, tag_id INT NOT NULL, `order` SMALLINT DEFAULT NULL, INDEX IDX_4A6B6A4834A3E1B6 (entity_user_id), INDEX IDX_4A6B6A48BAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, visible TINYINT(1) NOT NULL, is_user TINYINT(1) NOT NULL, is_page TINYINT(1) NOT NULL, is_protagonist TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE page_user_tag ADD CONSTRAINT FK_99C842335D0E3800 FOREIGN KEY (page_user_id) REFERENCES page_user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page_user_tag ADD CONSTRAINT FK_99C84233BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE entity_user_tag ADD CONSTRAINT FK_4A6B6A4834A3E1B6 FOREIGN KEY (entity_user_id) REFERENCES entity_user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE entity_user_tag ADD CONSTRAINT FK_4A6B6A48BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE user_user_tag");
        $this->addSql("ALTER TABLE user_tag_translation DROP FOREIGN KEY FK_BA7C53002C2AC5D3");
        $this->addSql("ALTER TABLE user_tag_translation ADD CONSTRAINT FK_BA7C53002C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES tag (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE entity_user DROP user_tags");
        $this->addSql("ALTER TABLE user_tag ADD user_id INT NOT NULL, ADD tag_id INT NOT NULL, ADD `order` SMALLINT DEFAULT NULL, DROP visible, DROP is_user, DROP is_page, DROP is_protagonist");
        $this->addSql("ALTER TABLE user_tag ADD CONSTRAINT FK_E89FD608A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE user_tag ADD CONSTRAINT FK_E89FD608BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_E89FD608A76ED395 ON user_tag (user_id)");
        $this->addSql("CREATE INDEX IDX_E89FD608BAD26311 ON user_tag (tag_id)");
        $this->addSql("ALTER TABLE page_user DROP user_tags");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE user_tag_translation DROP FOREIGN KEY FK_BA7C53002C2AC5D3");
        $this->addSql("ALTER TABLE page_user_tag DROP FOREIGN KEY FK_99C84233BAD26311");
        $this->addSql("ALTER TABLE user_tag DROP FOREIGN KEY FK_E89FD608BAD26311");
        $this->addSql("ALTER TABLE entity_user_tag DROP FOREIGN KEY FK_4A6B6A48BAD26311");
        $this->addSql("CREATE TABLE user_user_tag (id INT AUTO_INCREMENT NOT NULL, user_tag_id INT NOT NULL, user_id INT NOT NULL, `order` SMALLINT DEFAULT NULL, INDEX IDX_8123208A76ED395 (user_id), INDEX IDX_8123208DF80782C (user_tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE user_user_tag ADD CONSTRAINT FK_8123208DF80782C FOREIGN KEY (user_tag_id) REFERENCES user_tag (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE user_user_tag ADD CONSTRAINT FK_8123208A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE page_user_tag");
        $this->addSql("DROP TABLE entity_user_tag");
        $this->addSql("DROP TABLE tag");
        $this->addSql("ALTER TABLE entity_user ADD user_tags LONGTEXT DEFAULT NULL COMMENT '(DC2Type:simple_array)'");
        $this->addSql("ALTER TABLE page_user ADD user_tags LONGTEXT DEFAULT NULL COMMENT '(DC2Type:simple_array)'");
        $this->addSql("DROP INDEX IDX_E89FD608A76ED395 ON user_tag");
        $this->addSql("DROP INDEX IDX_E89FD608BAD26311 ON user_tag");
        $this->addSql("ALTER TABLE user_tag ADD visible TINYINT(1) NOT NULL, ADD is_user TINYINT(1) NOT NULL, ADD is_page TINYINT(1) NOT NULL, ADD is_protagonist TINYINT(1) NOT NULL, DROP user_id, DROP tag_id, DROP `order`");
        $this->addSql("ALTER TABLE user_tag_translation DROP FOREIGN KEY FK_BA7C53002C2AC5D3");
        $this->addSql("ALTER TABLE user_tag_translation ADD CONSTRAINT FK_BA7C53002C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES user_tag (id) ON DELETE CASCADE");
    }
}
