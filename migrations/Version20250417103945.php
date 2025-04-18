<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417103945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency DROP CONSTRAINT fk_677c3782f276c524
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE agent_hopital_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE receptionist_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE sis_admin_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE urgentist_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE agent_hospital (id SERIAL NOT NULL, user_id INT NOT NULL, hospital_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7453D1CFA76ED395 ON agent_hospital (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7453D1CF63DBB69 ON agent_hospital (hospital_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE autorisation (id SERIAL NOT NULL, demander_id_id INT NOT NULL, validator_id_id INT NOT NULL, status_id_id INT DEFAULT NULL, demander_role VARCHAR(255) NOT NULL, validator_role VARCHAR(255) NOT NULL, entity VARCHAR(255) DEFAULT NULL, entity_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_limit INT DEFAULT NULL, type_demande VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9A4313419526C78 ON autorisation (demander_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9A4313486F0EE8A ON autorisation (validator_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9A43134881ECFA7 ON autorisation (status_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE doctor_hospital (doctor_id INT NOT NULL, hospital_id INT NOT NULL, PRIMARY KEY(doctor_id, hospital_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EA76BA6D87F4FB17 ON doctor_hospital (doctor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EA76BA6D63DBB69 ON doctor_hospital (hospital_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE dossier_medicale (id SERIAL NOT NULL, consultation_id_id INT DEFAULT NULL, treatment_id_id INT DEFAULT NULL, patient_id_id INT DEFAULT NULL, antecedents_medicaux JSON DEFAULT NULL, medications_actuelles JSON DEFAULT NULL, antecedents_familiaux JSON DEFAULT NULL, access JSON DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4C53FBC04DE7C79 ON dossier_medicale (consultation_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4C53FBC015377D69 ON dossier_medicale (treatment_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4C53FBC0EA724598 ON dossier_medicale (patient_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE hospital_service (hospital_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY(hospital_id, service_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1320FB2963DBB69 ON hospital_service (hospital_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1320FB29ED5CA9E6 ON hospital_service (service_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE status (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE type_hopital (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hospital ADD CONSTRAINT FK_7453D1CFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hospital ADD CONSTRAINT FK_7453D1CF63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE autorisation ADD CONSTRAINT FK_9A4313419526C78 FOREIGN KEY (demander_id_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE autorisation ADD CONSTRAINT FK_9A4313486F0EE8A FOREIGN KEY (validator_id_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE autorisation ADD CONSTRAINT FK_9A43134881ECFA7 FOREIGN KEY (status_id_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor_hospital ADD CONSTRAINT FK_EA76BA6D87F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor_hospital ADD CONSTRAINT FK_EA76BA6D63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale ADD CONSTRAINT FK_4C53FBC04DE7C79 FOREIGN KEY (consultation_id_id) REFERENCES consultation (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale ADD CONSTRAINT FK_4C53FBC015377D69 FOREIGN KEY (treatment_id_id) REFERENCES treatment (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale ADD CONSTRAINT FK_4C53FBC0EA724598 FOREIGN KEY (patient_id_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_service ADD CONSTRAINT FK_1320FB2963DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_service ADD CONSTRAINT FK_1320FB29ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hopital DROP CONSTRAINT fk_da22ed86a76ed395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE receptionist DROP CONSTRAINT fk_f168accf9d86650f
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgentist DROP CONSTRAINT fk_72437903a76ed395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE agent_hopital
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE receptionist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sis_admin
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE urgentist
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability DROP date
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ALTER time_interval TYPE TIME(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ALTER time_interval SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ALTER date_interval SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor ADD is_archived BOOLEAN DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor ADD is_suspended BOOLEAN DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD status_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD type_hospital_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD is_archived BOOLEAN DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD infirmiers INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD autres_personnel_de_santé INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD name_director VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B6BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B4D246CF8 FOREIGN KEY (type_hospital_id) REFERENCES type_hopital (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4282C85B6BF700BD ON hospital (status_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4282C85B4D246CF8 ON hospital (type_hospital_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP CONSTRAINT fk_f515e139c4477e9b
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_f515e139c4477e9b
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP CONSTRAINT meeting_pkey
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD id SERIAL NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD patient_id_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD state_id_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP id_patient
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ALTER motif SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ALTER first_name DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ALTER last_name DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD CONSTRAINT FK_F515E139EA724598 FOREIGN KEY (patient_id_id) REFERENCES patient (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD CONSTRAINT FK_F515E139DD71A5B FOREIGN KEY (state_id_id) REFERENCES state (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F515E139EA724598 ON meeting (patient_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F515E139DD71A5B ON meeting (state_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP CONSTRAINT fk_b6bd307fb91aa170
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP CONSTRAINT fk_b6bd307fd787d2c4
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_b6bd307fd787d2c4
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_b6bd307fb91aa170
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD sender INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD receiver INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP "from"
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP "to"
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5F004ACF FOREIGN KEY (sender) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3DB88C96 FOREIGN KEY (receiver) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B6BD307F5F004ACF ON message (sender)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B6BD307F3DB88C96 ON message (receiver)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD notification_type_id_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD state_id_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD sender_id_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD receiver_id_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP "from"
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP "to"
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP state_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP notif_type_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA8A3ABC82 FOREIGN KEY (notification_type_id_id) REFERENCES notification_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CADD71A5B FOREIGN KEY (state_id_id) REFERENCES state (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA6061F7CF FOREIGN KEY (sender_id_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CABE20CAB0 FOREIGN KEY (receiver_id_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CA8A3ABC82 ON notification (notification_type_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CADD71A5B ON notification (state_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CA6061F7CF ON notification (sender_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF5476CABE20CAB0 ON notification (receiver_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment RENAME COLUMN status TO statut
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_677c3782f276c524
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ADD description VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency RENAME COLUMN id_urgentist TO user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ADD CONSTRAINT FK_677C3782A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_677C3782A76ED395 ON urgency (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP CONSTRAINT FK_4282C85B6BF700BD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP CONSTRAINT FK_4282C85B4D246CF8
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE agent_hopital_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE receptionist_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE sis_admin_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE urgentist_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE agent_hopital (id SERIAL NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_da22ed86a76ed395 ON agent_hopital (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE receptionist (id SERIAL NOT NULL, user_id_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_f168accf9d86650f ON receptionist (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sis_admin (id SERIAL NOT NULL, username VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, user_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE urgentist (id SERIAL NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_72437903a76ed395 ON urgentist (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hopital ADD CONSTRAINT fk_da22ed86a76ed395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE receptionist ADD CONSTRAINT fk_f168accf9d86650f FOREIGN KEY (user_id_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgentist ADD CONSTRAINT fk_72437903a76ed395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hospital DROP CONSTRAINT FK_7453D1CFA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hospital DROP CONSTRAINT FK_7453D1CF63DBB69
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE autorisation DROP CONSTRAINT FK_9A4313419526C78
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE autorisation DROP CONSTRAINT FK_9A4313486F0EE8A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE autorisation DROP CONSTRAINT FK_9A43134881ECFA7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor_hospital DROP CONSTRAINT FK_EA76BA6D87F4FB17
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor_hospital DROP CONSTRAINT FK_EA76BA6D63DBB69
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale DROP CONSTRAINT FK_4C53FBC04DE7C79
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale DROP CONSTRAINT FK_4C53FBC015377D69
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale DROP CONSTRAINT FK_4C53FBC0EA724598
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_service DROP CONSTRAINT FK_1320FB2963DBB69
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_service DROP CONSTRAINT FK_1320FB29ED5CA9E6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE agent_hospital
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE autorisation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE doctor_hospital
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE dossier_medicale
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE hospital_service
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE status
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE type_hopital
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ADD date DATE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ALTER time_interval TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ALTER time_interval DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ALTER date_interval DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor DROP is_archived
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor DROP is_suspended
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment RENAME COLUMN statut TO status
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA8A3ABC82
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP CONSTRAINT FK_BF5476CADD71A5B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA6061F7CF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP CONSTRAINT FK_BF5476CABE20CAB0
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BF5476CA8A3ABC82
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BF5476CADD71A5B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BF5476CA6061F7CF
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_BF5476CABE20CAB0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD "from" INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD "to" INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD state_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD notif_type_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP notification_type_id_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP state_id_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP sender_id_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP receiver_id_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP CONSTRAINT FK_B6BD307F5F004ACF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP CONSTRAINT FK_B6BD307F3DB88C96
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B6BD307F5F004ACF
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B6BD307F3DB88C96
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD "from" INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD "to" INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP sender
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP receiver
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT fk_b6bd307fb91aa170 FOREIGN KEY ("from") REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT fk_b6bd307fd787d2c4 FOREIGN KEY ("to") REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_b6bd307fd787d2c4 ON message ("to")
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_b6bd307fb91aa170 ON message ("from")
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP CONSTRAINT FK_F515E139EA724598
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP CONSTRAINT FK_F515E139DD71A5B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F515E139EA724598
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F515E139DD71A5B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX meeting_pkey
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD id_patient INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP patient_id_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP state_id_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ALTER first_name SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ALTER last_name SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ALTER motif DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD CONSTRAINT fk_f515e139c4477e9b FOREIGN KEY (id_patient) REFERENCES patient (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_f515e139c4477e9b ON meeting (id_patient)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD PRIMARY KEY (id_medecin, id_patient)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4282C85B6BF700BD
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4282C85B4D246CF8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP status_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP type_hospital_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP is_archived
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP infirmiers
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP autres_personnel_de_santé
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP name_director
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency DROP CONSTRAINT FK_677C3782A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_677C3782A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency DROP description
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency RENAME COLUMN user_id TO id_urgentist
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ADD CONSTRAINT fk_677c3782f276c524 FOREIGN KEY (id_urgentist) REFERENCES urgentist (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_677c3782f276c524 ON urgency (id_urgentist)
        SQL);
    }
}
