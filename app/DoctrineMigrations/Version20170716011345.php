<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170716011345 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE User ADD google_user_id VARCHAR(255) DEFAULT NULL, CHANGE third_party_user_id facebook_user_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DA17977D155CFEE ON User (facebook_user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DA17977592AEE13 ON User (google_user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_2DA17977D155CFEE ON User');
        $this->addSql('DROP INDEX UNIQ_2DA17977592AEE13 ON User');
        $this->addSql('ALTER TABLE User ADD third_party_user_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP facebook_user_id, DROP google_user_id');
    }
}
