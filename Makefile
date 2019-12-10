SHELL=/bin/bash
LOCAL_USER_ID := $(shell id -u)
DSHELL:=docker-compose run -u ${LOCAL_USER_ID}:${LOCAL_USER_ID} --rm app

build:
	bash -c "${DSHELL} ./build.sh"
up:
	docker-compose up -d
down:
	docker-compose down
test:
	bash -c "${DSHELL} ./vendor/bin/codecept build"
	bash -c "${DSHELL} ./vendor/bin/codecept run"
linters:
	bash -c "${DSHELL} composer check-cs"
	bash -c "${DSHELL} composer phpstan"

.PHONY: build test linters

