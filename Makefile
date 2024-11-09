#!/usr/bin/make -f
ifneq (,$(wildcard ./.env))
	include .env
	export
endif

test:
	docker compose exec app composer analyse
	#docker compose exec app bin/console orm:validate-schema
	docker compose exec app vendor/bin/pest

test-with-setup:
	make test-setup
	docker compose exec app bin/console admin admin@test.cz Test1234
	make test

test-one:
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