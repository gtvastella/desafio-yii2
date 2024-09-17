.PHONY: help build run stop clean bash migrate create-user

# Alvo padrão
help:
	@echo "Available commands:"
	@echo "  make build        - Build Docker images"
	@echo "  make run          - Start Docker containers"
	@echo "  make stop         - Stop Docker containers"
	@echo "  make clean        - Clean up containers and volumes"
	@echo "  make bash         - Open a bash shell in the app container"
	@echo "  make migrate      - Run database migrations"
	@echo "  make create-user  - Create a new user (requires arguments: name, login, password)"

build:
	@echo "Building Docker images..."
	docker compose --build -f

run:
	@echo "Starting Docker containers..."
	docker compose up -d

stop:
	@echo "Stopping Docker containers..."
	docker compose down

clean:
	@echo "Cleaning up containers and volumes..."
	docker compose down -v

bash:
	@echo "Opening a bash shell in the app container..."
	docker exec -it yii2 bash

migrate:
	@echo "Running database migrations..."
	docker exec -it yii2 php yii migrate --interactive=0

create-user:
	@echo "Creating a new user with arguments $(name), $(login), $(password)..."
	docker exec -it yii2 php yii user/create $(name) $(login) $(password)
