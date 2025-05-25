<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250524222237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
        CREATE TABLE reset_password_request (
            id INT AUTO_INCREMENT NOT NULL, 
            user_id INT NOT NULL, 
            selector VARCHAR(20) NOT NULL, 
            hashed_token VARCHAR(100) NOT NULL, 
            requested_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
            expires_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
            INDEX IDX_7CE748AA76ED395 (user_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
    SQL);

        $this->addSql("ALTER TABLE user CHANGE formateur_info formateur_info VARCHAR(255) DEFAULT NULL");
        $this->addSql("UPDATE user SET formateur_info = '' WHERE formateur_info IS NULL");
        $this->addSql("ALTER TABLE user CHANGE formateur_info formateur_info VARCHAR(255) NOT NULL");

        $this->addSql(<<<'SQL'
        ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
    SQL);
    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reset_password_request
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` CHANGE formateur_info formateur_info VARCHAR(255) DEFAULT NULL
        SQL);
    }
}
