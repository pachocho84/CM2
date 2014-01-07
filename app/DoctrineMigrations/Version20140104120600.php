<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140104120600 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE message_thread (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, subject VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, isSpam TINYINT(1) NOT NULL, INDEX IDX_607D18C61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, thread_id INT NOT NULL, sender_id INT NOT NULL, body LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, INDEX IDX_B6BD307FE2904019 (thread_id), INDEX IDX_B6BD307FF624B39D (sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE message_thread_metadata (id INT AUTO_INCREMENT NOT NULL, thread_id INT NOT NULL, participant_id INT NOT NULL, is_deleted TINYINT(1) NOT NULL, last_participant_message_date DATETIME DEFAULT NULL, last_message_date DATETIME DEFAULT NULL, INDEX IDX_38FC293EE2904019 (thread_id), INDEX IDX_38FC293E9D1C3019 (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE message_metadata (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, participant_id INT NOT NULL, is_read TINYINT(1) NOT NULL, status SMALLINT NOT NULL, INDEX IDX_4632F005537A1329 (message_id), INDEX IDX_4632F0059D1C3019 (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE message_thread ADD CONSTRAINT FK_607D18C61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE2904019 FOREIGN KEY (thread_id) REFERENCES message_thread (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE message_thread_metadata ADD CONSTRAINT FK_38FC293EE2904019 FOREIGN KEY (thread_id) REFERENCES message_thread (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE message_thread_metadata ADD CONSTRAINT FK_38FC293E9D1C3019 FOREIGN KEY (participant_id) REFERENCES user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE message_metadata ADD CONSTRAINT FK_4632F005537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE message_metadata ADD CONSTRAINT FK_4632F0059D1C3019 FOREIGN KEY (participant_id) REFERENCES user (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE2904019");
        $this->addSql("ALTER TABLE message_thread_metadata DROP FOREIGN KEY FK_38FC293EE2904019");
        $this->addSql("ALTER TABLE message_metadata DROP FOREIGN KEY FK_4632F005537A1329");
        $this->addSql("DROP TABLE message_thread");
        $this->addSql("DROP TABLE message");
        $this->addSql("DROP TABLE message_thread_metadata");
        $this->addSql("DROP TABLE message_metadata");
    }
}
