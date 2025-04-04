<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250404163258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE affiliation (id SERIAL NOT NULL, id_hospital INT NOT NULL, id_doctor INT NOT NULL, state BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EA721530125924CE ON affiliation (id_hospital)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EA72153039F74687 ON affiliation (id_doctor)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE agenda (id SERIAL NOT NULL, id_doctor INT NOT NULL, id_hospital INT NOT NULL, list_of_days VARCHAR(255) NOT NULL, time_interval TIME(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2CEDC87739F74687 ON agenda (id_doctor)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2CEDC877125924CE ON agenda (id_hospital)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE agent_hopital (id SERIAL NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DA22ED86A76ED395 ON agent_hopital (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE availability (id SERIAL NOT NULL, id_doctor INT NOT NULL, time_interval TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_interval TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date DATE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3FB7A2BF39F74687 ON availability (id_doctor)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE consultation (id SERIAL NOT NULL, id_patient INT NOT NULL, id_doctor INT NOT NULL, id_hospital INT DEFAULT NULL, description TEXT DEFAULT NULL, symptoms TEXT DEFAULT NULL, prescription TEXT DEFAULT NULL, diagnostic TEXT DEFAULT NULL, recommendation TEXT DEFAULT NULL, comment TEXT DEFAULT NULL, date_symptoms DATE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_964685A6C4477E9B ON consultation (id_patient)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_964685A639F74687 ON consultation (id_doctor)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_964685A6125924CE ON consultation (id_hospital)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE content_Message (id SERIAL NOT NULL, msg TEXT NOT NULL, type VARCHAR(255) NOT NULL, path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE disponibility (id SERIAL NOT NULL, id_doctor INT NOT NULL, date DATE DEFAULT NULL, date_interval DATE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_38BB926039F74687 ON disponibility (id_doctor)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE doctor (id SERIAL NOT NULL, user_id INT NOT NULL, service_id INT DEFAULT NULL, med_lisence_number VARCHAR(255) NOT NULL, speciality VARCHAR(255) NOT NULL, experience INT NOT NULL, service_starting_date DATE NOT NULL, diplome VARCHAR(255) DEFAULT NULL, other VARCHAR(255) DEFAULT NULL, cni VARCHAR(255) NOT NULL, medical_lisence_certificate VARCHAR(255) NOT NULL, cv VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1FC0F36AA76ED395 ON doctor (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1FC0F36AED5CA9E6 ON doctor (service_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE examination (id SERIAL NOT NULL, id_consultation INT NOT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CCDAABC5B587F0D4 ON examination (id_consultation)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE hospital (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, client_service_tel VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, web_site TEXT DEFAULT NULL, registration_number VARCHAR(255) NOT NULL, ceo VARCHAR(255) NOT NULL, accreditation VARCHAR(255) NOT NULL, niu VARCHAR(255) NOT NULL, rccm VARCHAR(255) NOT NULL, has_urgency BOOLEAN NOT NULL, has_ambulance BOOLEAN NOT NULL, exploitation_lisence VARCHAR(255) NOT NULL, accreditation_certificate VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE hospital_admin (id SERIAL NOT NULL, user_id INT NOT NULL, hopital_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_89549DDAA76ED395 ON hospital_admin (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_89549DDACC0FBF92 ON hospital_admin (hopital_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE meeting (id_medecin INT NOT NULL, id_patient INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, motif VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, PRIMARY KEY(id_medecin, id_patient))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F515E139C547FAB6 ON meeting (id_medecin)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F515E139C4477E9B ON meeting (id_patient)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE message (id SERIAL NOT NULL, sender INT NOT NULL, receiver INT NOT NULL, content_msg_id INT NOT NULL, state_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B6BD307F5F004ACF ON message (sender)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B6BD307F3DB88C96 ON message (receiver)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B6BD307FEC1D840C ON message (content_msg_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B6BD307F5D83CC1 ON message (state_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE notification (id SERIAL NOT NULL, sender INT NOT NULL, receiver INT NOT NULL, content TEXT NOT NULL, date_exp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, state_id INT NOT NULL, notif_type_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE notification_type (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE patient (id SERIAL NOT NULL, user_id INT NOT NULL, tutor_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1ADAD7EBA76ED395 ON patient (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1ADAD7EB208F64F1 ON patient (tutor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE receptionist (id SERIAL NOT NULL, user_id_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F168ACCF9D86650F ON receptionist (user_id_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE service (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sis_admin (id SERIAL NOT NULL, username VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, user_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE state (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE super_admin (id SERIAL NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE treatment (id SERIAL NOT NULL, consultation_id INT NOT NULL, description TEXT DEFAULT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_98013C3162FF6CDF ON treatment (consultation_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE urgency (id SERIAL NOT NULL, id_patient INT NOT NULL, id_hospital INT NOT NULL, id_urgentist INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_677C3782C4477E9B ON urgency (id_patient)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_677C3782125924CE ON urgency (id_hospital)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_677C3782F276C524 ON urgency (id_urgentist)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE urgentist (id SERIAL NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_72437903A76ED395 ON urgentist (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users (id SERIAL NOT NULL, uuid VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, nickname VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, gender BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, birth DATE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON users (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affiliation ADD CONSTRAINT FK_EA721530125924CE FOREIGN KEY (id_hospital) REFERENCES hospital (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affiliation ADD CONSTRAINT FK_EA72153039F74687 FOREIGN KEY (id_doctor) REFERENCES doctor (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC87739F74687 FOREIGN KEY (id_doctor) REFERENCES doctor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC877125924CE FOREIGN KEY (id_hospital) REFERENCES hospital (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hopital ADD CONSTRAINT FK_DA22ED86A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BF39F74687 FOREIGN KEY (id_doctor) REFERENCES doctor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD CONSTRAINT FK_964685A6C4477E9B FOREIGN KEY (id_patient) REFERENCES patient (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD CONSTRAINT FK_964685A639F74687 FOREIGN KEY (id_doctor) REFERENCES doctor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD CONSTRAINT FK_964685A6125924CE FOREIGN KEY (id_hospital) REFERENCES hospital (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE disponibility ADD CONSTRAINT FK_38BB926039F74687 FOREIGN KEY (id_doctor) REFERENCES doctor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36AED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE examination ADD CONSTRAINT FK_CCDAABC5B587F0D4 FOREIGN KEY (id_consultation) REFERENCES consultation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_admin ADD CONSTRAINT FK_89549DDAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_admin ADD CONSTRAINT FK_89549DDACC0FBF92 FOREIGN KEY (hopital_id) REFERENCES hospital (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD CONSTRAINT FK_F515E139C547FAB6 FOREIGN KEY (id_medecin) REFERENCES doctor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD CONSTRAINT FK_F515E139C4477E9B FOREIGN KEY (id_patient) REFERENCES patient (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5F004ACF FOREIGN KEY (sender) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F3DB88C96 FOREIGN KEY (receiver) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307FEC1D840C FOREIGN KEY (content_msg_id) REFERENCES content_Message (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB208F64F1 FOREIGN KEY (tutor_id) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE receptionist ADD CONSTRAINT FK_F168ACCF9D86650F FOREIGN KEY (user_id_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment ADD CONSTRAINT FK_98013C3162FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ADD CONSTRAINT FK_677C3782C4477E9B FOREIGN KEY (id_patient) REFERENCES patient (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ADD CONSTRAINT FK_677C3782125924CE FOREIGN KEY (id_hospital) REFERENCES hospital (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ADD CONSTRAINT FK_677C3782F276C524 FOREIGN KEY (id_urgentist) REFERENCES urgentist (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgentist ADD CONSTRAINT FK_72437903A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affiliation DROP CONSTRAINT FK_EA721530125924CE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affiliation DROP CONSTRAINT FK_EA72153039F74687
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agenda DROP CONSTRAINT FK_2CEDC87739F74687
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agenda DROP CONSTRAINT FK_2CEDC877125924CE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hopital DROP CONSTRAINT FK_DA22ED86A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability DROP CONSTRAINT FK_3FB7A2BF39F74687
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP CONSTRAINT FK_964685A6C4477E9B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP CONSTRAINT FK_964685A639F74687
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP CONSTRAINT FK_964685A6125924CE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE disponibility DROP CONSTRAINT FK_38BB926039F74687
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor DROP CONSTRAINT FK_1FC0F36AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE doctor DROP CONSTRAINT FK_1FC0F36AED5CA9E6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE examination DROP CONSTRAINT FK_CCDAABC5B587F0D4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_admin DROP CONSTRAINT FK_89549DDAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_admin DROP CONSTRAINT FK_89549DDACC0FBF92
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP CONSTRAINT FK_F515E139C547FAB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP CONSTRAINT FK_F515E139C4477E9B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP CONSTRAINT FK_B6BD307F5F004ACF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP CONSTRAINT FK_B6BD307F3DB88C96
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP CONSTRAINT FK_B6BD307FEC1D840C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP CONSTRAINT FK_B6BD307F5D83CC1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP CONSTRAINT FK_1ADAD7EBA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP CONSTRAINT FK_1ADAD7EB208F64F1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE receptionist DROP CONSTRAINT FK_F168ACCF9D86650F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment DROP CONSTRAINT FK_98013C3162FF6CDF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency DROP CONSTRAINT FK_677C3782C4477E9B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency DROP CONSTRAINT FK_677C3782125924CE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency DROP CONSTRAINT FK_677C3782F276C524
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgentist DROP CONSTRAINT FK_72437903A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE affiliation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE agenda
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE agent_hopital
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE availability
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE consultation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE content_Message
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE disponibility
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE doctor
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE examination
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE hospital
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE hospital_admin
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE meeting
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE message
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE patient
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE receptionist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE service
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sis_admin
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE state
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE super_admin
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE treatment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE urgency
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE urgentist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users
        SQL);
    }
}
