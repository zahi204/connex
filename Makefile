.PHONY: up down fresh seed dev dev-backend dev-frontend

up:
	docker compose up -d

down:
	docker compose down

fresh:
	cd backend && php artisan migrate:fresh --seed

seed:
	cd backend && php artisan db:seed

dev-backend:
	cd backend && php artisan serve

dev-frontend:
	cd frontend && npm run dev

dev:
	make dev-backend & make dev-frontend
