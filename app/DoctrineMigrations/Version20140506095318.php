<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140506095318 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE user ADD city_birth_lang VARCHAR(5) DEFAULT NULL, ADD city_birth_latitude DOUBLE PRECISION NOT NULL, ADD city_birth_longitude DOUBLE PRECISION NOT NULL, ADD city_current_lang VARCHAR(5) DEFAULT NULL, ADD city_current_latitude DOUBLE PRECISION NOT NULL, ADD city_current_longitude DOUBLE PRECISION NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE user DROP city_birth_lang, DROP city_birth_latitude, DROP city_birth_longitude, DROP city_current_lang, DROP city_current_latitude, DROP city_current_longitude");
    }
}
