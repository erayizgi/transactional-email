help:	## Show this help
	@echo ""
	@echo "Usage:  make COMMAND"
	@echo ""
	@echo "Commands:"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'
	@echo ""
.PHONY: help

build:	## (Re)build locally the docker containers for this application
	docker compose build
.PHONY: build

down:	## Stop the docker containers for local development
	docker compose down
.PHONY: down

up:	## Start the docker containers for local development
	docker compose up -d
.PHONY: up

fresh-up: ## Restart the containers with removing older ones
	docker compose down --remove-orphans && docker compose up -d
.PHONY: fresh-up

restart: ## Restart the containers
	docker compose restart
.PHONY: restart

cli: ## Gives the bash of given service
	docker compose exec $(filter-out $@,$(MAKECMDGOALS)) bash
.PHONY: cli

scale-app: ## Scales up/down the app service to given number of instances
	docker compose up app --scale app=$(filter-out $@,$(MAKECMDGOALS)) -d
.PHONY: scale-app

scale-worker: ## Scales up/down the app service to given number of instances
	docker compose up app --scale queue-worker=$(filter-out $@,$(MAKECMDGOALS)) -d
.PHONY: scale-worker

migrate-fresh: ## Do a fresh migration
	docker compose exec app bash -c "php artisan migrate:fresh"
.PHONY: migrate-fresh

tail: ## Follow logs of given service
	docker compose logs $(filter-out $@,$(MAKECMDGOALS)) -f
.PHONY: tail

create-mail: ## Gives CLI tool to create a mail
	docker compose exec app bash -c "php artisan create:mail"
.PHONY: create-mail

test: ## Runs test
	docker compose exec app bash -c "php artisan test --coverage"
.PHONY: test