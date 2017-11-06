.PHONY: run-backend-php run-backend-node run-frontend-node

run-backend-php:
	php backend-php/server.php

run-backend-node:
	cd backend-node && node server.js

run-frontend-react:
	cd frontend-react && node server.js

