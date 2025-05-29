<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529114041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE formateur (id INT NOT NULL, description LONGTEXT DEFAULT NULL, wallet DOUBLE PRECISION DEFAULT NULL, total_revenue DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE formateur_formation (formateur_id INT NOT NULL, formation_id INT NOT NULL, INDEX IDX_52449B08155D8F51 (formateur_id), INDEX IDX_52449B085200282E (formation_id), PRIMARY KEY(formateur_id, formation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formateur ADD CONSTRAINT FK_ED767E4FBF396750 FOREIGN KEY (id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formateur_formation ADD CONSTRAINT FK_52449B08155D8F51 FOREIGN KEY (formateur_id) REFERENCES formateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formateur_formation ADD CONSTRAINT FK_52449B085200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formation DROP FOREIGN KEY FK_404021BF155D8F51
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formation ADD CONSTRAINT FK_404021BF155D8F51 FOREIGN KEY (formateur_id) REFERENCES formateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP role_type
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE formation DROP FOREIGN KEY FK_404021BF155D8F51
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formateur DROP FOREIGN KEY FK_ED767E4FBF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formateur_formation DROP FOREIGN KEY FK_52449B08155D8F51
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formateur_formation DROP FOREIGN KEY FK_52449B085200282E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE formateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE formateur_formation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formation DROP FOREIGN KEY FK_404021BF155D8F51
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE formation ADD CONSTRAINT FK_404021BF155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` ADD role_type VARCHAR(255) DEFAULT NULL
        SQL);
    }
}
