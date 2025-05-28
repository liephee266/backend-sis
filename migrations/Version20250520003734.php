<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520003734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hospital ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation CHANGE intensité_symptome intensite_symptome VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE disponibilite CHANGE heure_debut heure_debut TIME, CHANGE heure_fin heure_fin TIME
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale CHANGE uuid uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting CHANGE heure heure TIME
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD receiver_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CACD53EDB6 ON notification (receiver_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD created_by_id INT DEFAULT NULL, ADD uuid VARCHAR(255) NOT NULL, CHANGE poids poids INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1ADAD7EBB03A8386 ON patient (created_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE super_admin ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgentist ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users CHANGE uuid uuid VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE tel tel VARCHAR(255) DEFAULT NULL, CHANGE gender gender VARCHAR(1) NOT NULL, CHANGE nickname nickname VARCHAR(255) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1483A5E9A188FE64 ON users (nickname)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1483A5E9F037AB0F ON users (tel)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hospital DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation CHANGE intensite_symptome intensité_symptome VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE disponibilite CHANGE heure_debut heure_debut TIME DEFAULT NULL, CHANGE heure_fin heure_fin TIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale CHANGE uuid uuid VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting CHANGE heure heure TIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CACD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BF5476CACD53EDB6 ON notification
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP receiver_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBB03A8386
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1ADAD7EBB03A8386 ON patient
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP created_by_id, DROP uuid, CHANGE poids poids INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE super_admin DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgentist DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_1483A5E9A188FE64 ON users
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_1483A5E9F037AB0F ON users
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users CHANGE uuid uuid VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nickname nickname VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE tel tel VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE image image VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE gender gender VARCHAR(1) NOT NULL COLLATE `utf8mb4_unicode_ci`
        SQL);
    }
}
