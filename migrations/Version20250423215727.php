<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423215727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE affiliation ADD created_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affiliation ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ADD created_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD raison_visite TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD recommandation TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD created_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD intensité_symptome VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD prochaine_consultation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD test_supplementaire VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD diagnostic_preliminaire VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD antecedents_medicaux JSON DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP description
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP prescription
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP diagnostic
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP recommendation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content_message ADD created_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content_message ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale ADD created_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale RENAME COLUMN antecedents_medicaux TO habitude_vie
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE examination ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE examination ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE examination ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting ADD updated_at DATE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD created_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD created_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD poids INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD groupe_sanguins VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD taille VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD signaler_comme_decedé BOOLEAN NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD nom_urgence VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD adresse_urgence VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient ADD numero_urgence VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE service ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE service ADD created_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE service ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment ADD created_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment ADD updated_at DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ADD uuid VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ALTER created_at TYPE DATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ALTER updated_at TYPE DATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD image VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ALTER gender TYPE VARCHAR(1)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE service DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE service DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE service DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE urgency ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meeting DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP poids
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP groupe_sanguins
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP taille
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP signaler_comme_decedé
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP nom_urgence
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP adresse_urgence
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE patient DROP numero_urgence
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD description TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD prescription TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD diagnostic TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation ADD recommendation TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP raison_visite
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP recommandation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP intensité_symptome
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP prochaine_consultation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP test_supplementaire
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP diagnostic_preliminaire
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consultation DROP antecedents_medicaux
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affiliation DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affiliation DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dossier_medicale RENAME COLUMN habitude_vie TO antecedents_medicaux
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP image
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ALTER gender TYPE BOOLEAN
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ALTER gender TYPE BOOLEAN
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content_Message DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE content_Message DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE examination DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE examination DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE examination DROP uuid
        SQL);
    }
}
