.PHONY: help build up down bash composer install stan csfixer csfixertests phpunit

# Variables
DOCKER_PHP := docker compose run --rm php

help:
	@echo "Cibles disponibles :"
	@echo "  build        : Build les images Docker"
	@echo "  up           : Démarre l'environnement Docker en arrière-plan"
	@echo "  down         : Arrête l'environnement Docker"
	@echo "  bash         : Bash dans le conteneur PHP"
	@echo "  composer     : Lance composer dans le conteneur"
	@echo "  install      : Installe les dépendances composer"
	@echo "  stan         : Lance PHPStan"
	@echo "  csfixer      : Vérifie le code avec PHP-CS-Fixer"
	@echo "  csfixertests : Vérifie le code des tests avec PHP-CS-Fixer"
	@echo "  phpunit      : Lance les tests PHPUnit"

build:
	docker compose build

up:
	docker compose up -d

down:
	docker compose down

bash:
	$(DOCKER_PHP) bash

composer:
	$(DOCKER_PHP) composer

install:
	$(DOCKER_PHP) composer install --prefer-dist --no-interaction

stan:
	$(DOCKER_PHP) vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=1G

csfixer:
	$(DOCKER_PHP) vendor/bin/php-cs-fixer fix ./src --verbose --rules=@Symfony

csfixertests:
	$(DOCKER_PHP) vendor/bin/php-cs-fixer fix ./tests --verbose --rules=@Symfony

phpunit:
	$(DOCKER_PHP) vendor/bin/phpunit --no-configuration ./tests --do-not-cache-result
