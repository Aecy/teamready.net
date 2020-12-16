http_server = localhost
http_port = 8000

php = php
syf = symfony
sy = $(php) bin/console

.DEFAULT_GOAL = help

.PHONY: help
help: ## Affiche cette demande d'aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: test
test: vendor/autoload.php ## Execute les tests
	.\vendor\bin\phpunit

.PHONY: dev
dev: vendor/autoload.php ## Lance le serveur de développement
	$(php) -S $(http_server):$(http_port) -t public

.PHONY: serve
serve: vendor/autoload.php ## Allume un serveur avec le HTTPS
	$(syf) serve --daemon --port=$(http_port)

.PHONY: serve-stop
serve-stop: vendor/autoload.php ## Eteint le serveur
	$(syf) server:stop

vendor/autoload.php: composer.lock
	$(php) composer install

## -- Symfony

.PHONY: seed
seed: vendor/autoload.php ## Génère les données
	$(sy) hautelook:fixtures:load -q

.PHONY: migrate
migrate: vendor/autoload.php ## Migre la base de donnée
	$(sy) doctrine:migrations:migrate

.PHONY: cc
cc: vendor/autoload.php ## Clear le cache de l'application
	$(sy) cache:clear

.PHONY: assets
assets: purge ## Initialise les assets avec des liens symboliques
	$(sy) assets:install public/ --symlink --relative

.PHONY: purge
purge: vendor/autoload.php ## Nettoie le cache et les logs serveur
	rm -rf var/cache/* var/logs/*

## -- Symfony binary
.PHONY: ca-install
ca-install:  vendor/autoload.php ## Initialise le certificat SSL HTTPS localement
	$(syf) server:ca:install

.PHONY: fixture
fixture: vendor/autoload.php ## Construit la base de donnée
	$(sy) doctrine:cache:clear-metadata
	$(sy) doctrine:database:create --if-not-exists
	$(sy) doctrine:schema:drop --force
	$(sy) doctrine:schema:create
	$(sy) doctrine:schema:validate
	$(sy) doctrine:fixtures:load -n
