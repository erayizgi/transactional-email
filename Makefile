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

tail:	## Tail the log files of the containers
	docker compose logs -f -t --tail=20
.PHONY: tail

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