#!/usr/bin/make -f
ifneq (,$(wildcard ./.env))
	include .env
	export
endif

serve:
	docker compose up -d
	docker compose exec app composer i
	docker compose exec app bin/console migration:diff --no-interaction
	docker compose exec app bin/console migration:migrate --no-interaction
	docker compose exec app bin/console app:auth:resources:update

sh:
	docker compose exec -it app /bin/bash

format:
	docker compose exec app composer format

format-check:
	docker compose exec app composer format:check

test:
	docker compose exec app rm -rf temp/*
	docker compose exec app composer analyse

test-single:
	docker compose exec app rm -rf temp/*
	docker compose exec app vendor/bin/pest $(FILE)


test-setup:
	rm -rf migrations/*
	rm -rf temp/*
	rm -rf log/*

	docker compose up -d --build
	docker compose exec app composer i
	docker compose exec postgres psql -U "$(DB_USERNAME)" -d "postgres" -c "DROP DATABASE IF EXISTS \"$(DB_DATABASE)\";"
	docker compose exec postgres psql -U "$(DB_USERNAME)" -d "postgres" -c "CREATE DATABASE \"$(DB_DATABASE)\";"

	docker compose exec app bin/console migration:diff --no-interaction
	docker compose exec app bin/console migration:migrate --no-interaction
	docker compose exec app bin/console app:auth:resources:update

	docker compose exec app bin/console admin admin@test.cz Test1234