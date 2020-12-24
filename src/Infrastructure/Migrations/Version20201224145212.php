<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201224145212 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE password_reset_token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6B7BA4B6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE password_reset_token ADD CONSTRAINT FK_6B7BA4B6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD country VARCHAR(2) DEFAULT NULL, ADD confirmation_token VARCHAR(255) DEFAULT NULL, ADD theme VARCHAR(255) DEFAULT NULL, ADD avatar_name VARCHAR(255) DEFAULT NULL, ADD mail_notification TINYINT(1) DEFAULT \'1\' NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD last_activity_at DATETIME DEFAULT NULL, ADD banned_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE password_reset_token');
        $this->addSql('ALTER TABLE `user` DROP country, DROP confirmation_token, DROP theme, DROP avatar_name, DROP mail_notification, DROP updated_at, DROP created_at, DROP last_activity_at, DROP banned_at, DROP deleted_at');
    }
}
