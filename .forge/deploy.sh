# Pegar en Forge → Site → Deployment Script
# Si el dominio del sitio Forge es otro, cambia el cd.

cd /home/forge/dashboard.mie-commerce.com

git pull origin $FORGE_SITE_BRANCH

if [ ! -f .env ]; then
    echo "ERROR: falta .env. Copia .env.example y configura DB/URLs."
    exit 1
fi

mkdir -p assets/empresas logs cache
chmod -R ug+rwx assets/empresas logs cache 2>/dev/null || true
