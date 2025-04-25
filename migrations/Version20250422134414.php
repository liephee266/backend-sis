<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250422134414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE autorisation CHANGE entity entity VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation CHANGE date_symptoms date_symptoms DATE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content_message CHANGE path path VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor CHANGE diplome diplome VARCHAR(255) DEFAULT NULL, CHANGE other other VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale CHANGE antecedents_medicaux antecedents_medicaux JSON DEFAULT NULL, CHANGE medications_actuelles medications_actuelles JSON DEFAULT NULL, CHANGE antecedents_familiaux antecedents_familiaux JSON DEFAULT NULL, CHANGE access access JSON DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique_medical DROP FOREIGN KEY FK_15CF58257750B79F
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_15CF58257750B79F ON historique_medical
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique_medical ADD medecin_traitant_id INT DEFAULT NULL, ADD hospital_id INT DEFAULT NULL, ADD motif_de_la_consultation VARCHAR(255) DEFAULT NULL, ADD recommandations VARCHAR(255) DEFAULT NULL, ADD observations_et_diagnostics VARCHAR(255) DEFAULT NULL, CHANGE patient_id patient_id INT DEFAULT NULL, CHANGE dossier_medical_id treatment_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique_medical ADD CONSTRAINT FK_15CF5825471C0366 FOREIGN KEY (treatment_id) REFERENCES treatment (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique_medical ADD CONSTRAINT FK_15CF5825B572964A FOREIGN KEY (medecin_traitant_id) REFERENCES doctor (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique_medical ADD CONSTRAINT FK_15CF582563DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_15CF5825471C0366 ON historique_medical (treatment_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_15CF5825B572964A ON historique_medical (medecin_traitant_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_15CF582563DBB69 ON historique_medical (hospital_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital CHANGE logo logo VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP image, CHANGE roles roles JSON NOT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE tel tel VARCHAR(255) DEFAULT NULL, CHANGE birth birth DATE DEFAULT NULL, CHANGE gender gender TINYINT(1) NOT NULL, CHANGE nickname nickname VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE autorisation CHANGE entity entity VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation CHANGE date_symptoms date_symptoms DATE DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content_Message CHANGE path path VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor CHANGE diplome diplome VARCHAR(255) DEFAULT 'NULL', CHANGE other other VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale CHANGE antecedents_medicaux antecedents_medicaux LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE medications_actuelles medications_actuelles LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE antecedents_familiaux antecedents_familiaux LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE access access LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique_medical DROP FOREIGN KEY FK_15CF5825471C0366
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique_medical DROP FOREIGN KEY FK_15CF5825B572964A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique_medical DROP FOREIGN KEY FK_15CF582563DBB69
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_15CF5825471C0366 ON historique_medical
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_15CF5825B572964A ON historique_medical
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_15CF582563DBB69 ON historique_medical
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique_medical ADD dossier_medical_id INT DEFAULT NULL, DROP treatment_id, DROP medecin_traitant_id, DROP hospital_id, DROP motif_de_la_consultation, DROP recommandations, DROP observations_et_diagnostics, CHANGE patient_id patient_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE historique_medical ADD CONSTRAINT FK_15CF58257750B79F FOREIGN KEY (dossier_medical_id) REFERENCES dossier_medicale (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_15CF58257750B79F ON historique_medical (dossier_medical_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital CHANGE logo logo VARCHAR(255) DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting CHANGE first_name first_name VARCHAR(255) DEFAULT 'NULL', CHANGE last_name last_name VARCHAR(255) DEFAULT 'NULL', CHANGE updated_at updated_at DATE DEFAULT 'NULL'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD image VARCHAR(255) DEFAULT 'NULL', CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE first_name first_name VARCHAR(255) DEFAULT 'NULL', CHANGE last_name last_name VARCHAR(255) DEFAULT 'NULL', CHANGE nickname nickname VARCHAR(255) DEFAULT 'NULL', CHANGE address address VARCHAR(255) DEFAULT 'NULL', CHANGE tel tel VARCHAR(255) DEFAULT 'NULL', CHANGE gender gender VARCHAR(1) NOT NULL, CHANGE birth birth DATE DEFAULT 'NULL'
        SQL);
    }
}
