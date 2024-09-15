SERVICE_NAME = app

build:
	@echo "Building Docker images..."
	docker-compose -f docker-compose.yaml build

run:
	@echo "Starting Docker containers..."
	docker-compose -f docker-compose.yaml up -d $(SERVICE_NAME)

stop:
	@echo "Stopping Docker containers..."
	docker-compose -f docker-compose.yaml down

clean:
	@echo "Cleaning up containers and volumes..."
	docker-compose -f docker-compose.yaml down -v
