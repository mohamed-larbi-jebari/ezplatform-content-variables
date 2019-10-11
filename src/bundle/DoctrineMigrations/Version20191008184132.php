<?php

declare(strict_types=1);

namespace ContextualCode\EzPlatformContentVariablesBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191008184132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cc_content_variable (id INT AUTO_INCREMENT NOT NULL, collection_id INT DEFAULT NULL, identifier VARCHAR(256) NOT NULL, name VARCHAR(256) NOT NULL, value_type SMALLINT DEFAULT NULL, value_static VARCHAR(256) DEFAULT NULL, value_callback VARCHAR(256) DEFAULT NULL, priority INT DEFAULT NULL, UNIQUE INDEX UNIQ_E14B29A3772E836A (identifier), INDEX IDX_E14B29A3514956FD (collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cc_content_variable_collection (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(256) NOT NULL, description VARCHAR(256) DEFAULT NULL, priority INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cc_content_variable ADD CONSTRAINT FK_E14B29A3514956FD FOREIGN KEY (collection_id) REFERENCES cc_content_variable_collection (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cc_content_variable DROP FOREIGN KEY FK_E14B29A3514956FD');
        $this->addSql('DROP TABLE cc_content_variable');
        $this->addSql('DROP TABLE cc_content_variable_collection');
    }
}
