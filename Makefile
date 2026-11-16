# ============================================================
#  Setly — raccourcis Docker
# ============================================================

up:            ## Démarre les conteneurs
	docker compose up -d --build

down:          ## Arrête les conteneurs
	docker compose down

restart: down up

logs:          ## Affiche les logs de l'app
	docker compose logs -f app

sync-mongo:    ## Alimente MongoDB depuis MySQL (statistiques)
	docker compose exec app php database/sync_mongo.php

reset-db:      ## Réinitialise complètement la base MySQL
	docker compose down -v
	docker compose up -d --build

shell:         ## Ouvre un shell dans le conteneur app
	docker compose exec app bash

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

.PHONY: up down restart logs sync-mongo reset-db shell help
