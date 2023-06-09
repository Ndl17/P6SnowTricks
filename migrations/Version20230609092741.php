<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230609092741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C85F5AD92');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA0D628E5');
        $this->addSql('DROP INDEX IDX_9474526CA0D628E5 ON comment');
        $this->addSql('DROP INDEX IDX_9474526C85F5AD92 ON comment');
        $this->addSql('ALTER TABLE comment ADD user_id INT NOT NULL, ADD figure_id INT NOT NULL, DROP id_pseudo_id, DROP id_figure_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('CREATE INDEX IDX_9474526C5C011B5 ON comment (figure_id)');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37A9D86650F');
        $this->addSql('DROP INDEX IDX_2F57B37A9D86650F ON figure');
        $this->addSql('ALTER TABLE figure CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2F57B37AA76ED395 ON figure (user_id)');
        $this->addSql('ALTER TABLE user ADD is_verified TINYINT(1) NOT NULL, CHANGE pseudo pseudo VARCHAR(50) NOT NULL, CHANGE reset_token reset_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5C011B5');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395 ON comment');
        $this->addSql('DROP INDEX IDX_9474526C5C011B5 ON comment');
        $this->addSql('ALTER TABLE comment ADD id_pseudo_id INT NOT NULL, ADD id_figure_id INT NOT NULL, DROP user_id, DROP figure_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C85F5AD92 FOREIGN KEY (id_figure_id) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA0D628E5 FOREIGN KEY (id_pseudo_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9474526CA0D628E5 ON comment (id_pseudo_id)');
        $this->addSql('CREATE INDEX IDX_9474526C85F5AD92 ON comment (id_figure_id)');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AA76ED395');
        $this->addSql('DROP INDEX IDX_2F57B37AA76ED395 ON figure');
        $this->addSql('ALTER TABLE figure CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37A9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2F57B37A9D86650F ON figure (user_id_id)');
        $this->addSql('ALTER TABLE user DROP is_verified, CHANGE pseudo pseudo VARCHAR(50) DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(255) NOT NULL');
    }
}
