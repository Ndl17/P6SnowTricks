<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230413140838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AB2C17F66');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AE65D2AB8');
        $this->addSql('DROP INDEX IDX_E01FBE6AB2C17F66 ON images');
        $this->addSql('DROP INDEX IDX_E01FBE6AE65D2AB8 ON images');
        $this->addSql('ALTER TABLE images DROP fig2_id, DROP id_img_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images ADD fig2_id INT DEFAULT NULL, ADD id_img_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AB2C17F66 FOREIGN KEY (fig2_id) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AE65D2AB8 FOREIGN KEY (id_img_id) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E01FBE6AB2C17F66 ON images (fig2_id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6AE65D2AB8 ON images (id_img_id)');
    }
}
