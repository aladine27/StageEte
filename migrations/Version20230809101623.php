<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230809101623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture CHANGE net_apayer net_apayer DOUBLE PRECISION NOT NULL, CHANGE anc_index anc_index DOUBLE PRECISION NOT NULL, CHANGE nouv_inedx nouv_inedx DOUBLE PRECISION NOT NULL, CHANGE estimation estimation DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture CHANGE net_apayer net_apayer TINYINT(1) NOT NULL, CHANGE anc_index anc_index TINYINT(1) NOT NULL, CHANGE nouv_inedx nouv_inedx TINYINT(1) NOT NULL, CHANGE estimation estimation TINYINT(1) NOT NULL');
    }
}
