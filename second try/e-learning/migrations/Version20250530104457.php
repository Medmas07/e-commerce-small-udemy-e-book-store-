<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250530104457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book DROP title, CHANGE id id INT NOT NULL, CHANGE type category VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book ADD CONSTRAINT FK_CBE5A331BF396750 FOREIGN KEY (id) REFERENCES produit (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formation DROP title, DROP description, DROP price, CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formation ADD CONSTRAINT FK_404021BFBF396750 FOREIGN KEY (id) REFERENCES produit (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit_choisi DROP FOREIGN KEY FK_758DCFD25200282E
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_758DCFD25200282E ON produit_choisi
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit_choisi ADD produit_id INT NOT NULL, DROP formation_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit_choisi ADD CONSTRAINT FK_758DCFD2F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_758DCFD2F347EFB ON produit_choisi (produit_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formation DROP FOREIGN KEY FK_404021BFBF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit_choisi DROP FOREIGN KEY FK_758DCFD2F347EFB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE produit
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book ADD title VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE category type VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formation ADD title VARCHAR(255) NOT NULL, ADD description VARCHAR(255) DEFAULT NULL, ADD price INT NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_758DCFD2F347EFB ON produit_choisi
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit_choisi ADD formation_id INT DEFAULT NULL, DROP produit_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit_choisi ADD CONSTRAINT FK_758DCFD25200282E FOREIGN KEY (formation_id) REFERENCES formation (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_758DCFD25200282E ON produit_choisi (formation_id)
        SQL);
    }
}
