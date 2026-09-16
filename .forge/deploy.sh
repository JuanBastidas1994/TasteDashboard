# Pegar en Forge → Site → Deployments → Deploy Script
# PHP 8.4. Sin Composer ni npm: el vendor ya va en git.

$CREATE_RELEASE()

cd $FORGE_RELEASE_DIRECTORY

if [ ! -f .env ]; then
    echo "ERROR: falta .env. Pegalo en Forge → Environment (Forge lo comparte solo entre releases)."
    exit 1
fi

# Uploads y logs viven fuera de releases/ para no perderlos en cada deploy.
SHARED="$FORGE_SITE_ROOT/shared"
share() {
    local rel="$1"
    mkdir -p "$SHARED/$rel"
    mkdir -p "$(dirname "$rel")"
    rm -rf "$rel"
    ln -sfn "$SHARED/$rel" "$rel"
}

share assets/empresas
share assets/demos
share assets/portfolio
share logs
share replicador/zip

chmod -R ug+rwX "$SHARED" || true

$ACTIVATE_RELEASE()
