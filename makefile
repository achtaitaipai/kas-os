.PHONY: all composer-install npm-install build zip

# La cible "all" exécute toutes les étapes dans l'ordre.
all: composer-install npm-install build zip

# 1. Installe les dépendances Composer (sans dev)
composer-install:
	composer install --no-dev --optimize-autoloader

# 2. Installe les dépendances NPM
npm-install:
	cd vite && npm install

# 3. Lance la commande de build (frontend, par exemple)
build:
	cd vite && npm run build

# 4. Crée l'archive ZIP
zip:
	zip -r kas-os.zip . \
		-x "composer.json" \
		-x "composer.lock" \
		-x "README.md" \
		-x "makefile" \
		-x "accounts.txt" \
		-x ".git/*" \
		-x "vite/*" \
		-x "kas-os/*" \
		-x "kas-os.zip" \
		-x "*.DS_Store" \
		-x "*.gitignore" \
		-x "content/*" 
	
	zip kas-os.zip content/ -i content/
