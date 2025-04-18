<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418120850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE agent_hospital (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, hospital_id INT NOT NULL, INDEX IDX_7453D1CFA76ED395 (user_id), INDEX IDX_7453D1CF63DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE hospital_service (hospital_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_1320FB2963DBB69 (hospital_id), INDEX IDX_1320FB29ED5CA9E6 (service_id), PRIMARY KEY(hospital_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE type_hopital (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hospital ADD CONSTRAINT FK_7453D1CFA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hospital ADD CONSTRAINT FK_7453D1CF63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_service ADD CONSTRAINT FK_1320FB2963DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_service ADD CONSTRAINT FK_1320FB29ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affiliation ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agenda ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL, ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD uuid VARCHAR(255) NOT NULL, ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content_message ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL, ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE examination ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD type_hospital_id INT NOT NULL, ADD uuid VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD infirmiers INT NOT NULL, ADD autres_personnel_de_santé INT NOT NULL, ADD name_director VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B4D246CF8 FOREIGN KEY (type_hospital_id) REFERENCES type_hopital (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4282C85B4D246CF8 ON hospital (type_hospital_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD uuid VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD uuid VARCHAR(255) NOT NULL, ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD uuid VARCHAR(255) NOT NULL, ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE service ADD uuid VARCHAR(255) NOT NULL, ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment ADD uuid VARCHAR(255) NOT NULL, ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ADD description VARCHAR(255) NOT NULL, ADD created_at DATE NOT NULL, ADD updated_at DATE NOT NULL, ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD image VARCHAR(255) DEFAULT NULL, CHANGE gender gender VARCHAR(1) NOT NULL, CHANGE nickname username VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP FOREIGN KEY FK_4282C85B4D246CF8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hospital DROP FOREIGN KEY FK_7453D1CFA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agent_hospital DROP FOREIGN KEY FK_7453D1CF63DBB69
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_service DROP FOREIGN KEY FK_1320FB2963DBB69
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital_service DROP FOREIGN KEY FK_1320FB29ED5CA9E6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE agent_hospital
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE hospital_service
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE type_hopital
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affiliation DROP created_at, DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE agenda DROP created_at, DROP updated_at, DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability DROP created_at, DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP uuid, DROP created_at, DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content_Message DROP created_at, DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale DROP created_at, DROP updated_at, DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE examination DROP created_at, DROP updated_at, DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4282C85B4D246CF8 ON hospital
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE hospital DROP type_hospital_id, DROP uuid, DROP created_at, DROP updated_at, DROP infirmiers, DROP autres_personnel_de_santé, DROP name_director
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP uuid, DROP created_at, DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP uuid, DROP created_at, DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP uuid, DROP created_at, DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE service DROP uuid, DROP created_at, DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment DROP uuid, DROP created_at, DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency DROP description, DROP created_at, DROP updated_at, DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD nickname VARCHAR(255) DEFAULT NULL, DROP username, DROP image, CHANGE gender gender TINYINT(1) NOT NULL
        SQL);
    }
}
