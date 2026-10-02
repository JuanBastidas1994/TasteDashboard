# Pegar en Forge → Site → Deployments → Deploy Script
# PHP puro, sin Composer. Los archivos subidos no viven en current.

$CREATE_RELEASE()

cd $FORGE_RELEASE_DIRECTORY

if [ ! -f .env ]; then
    echo "ERROR: falta .env. Pegalo en Forge → Environment (Forge lo comparte solo entre releases)."
    exit 1
fi

# En zero-downtime FORGE_SITE_PATH a veces es .../current. La carpeta fija es el padre.
SITE_ROOT="${FORGE_SITE_PATH:-/home/forge/dashboard.tastelatam.com}"
if [ "$(basename "$SITE_ROOT")" = "current" ]; then
    SITE_ROOT="$(dirname "$SITE_ROOT")"
fi
case "$SITE_ROOT" in
    */releases/*) SITE_ROOT="$(dirname "$(dirname "$SITE_ROOT")")" ;;
esac
SHARED="$SITE_ROOT/shared"

share() {
    local rel="$1"
    mkdir -p "$SHARED/$rel"
    if [ -d "$rel" ] && [ ! -L "$rel" ]; then
        cp -a "$rel/." "$SHARED/$rel/" 2>/dev/null || true
    fi
    if [ -d "$SITE_ROOT/current/$rel" ] && [ ! -L "$SITE_ROOT/current/$rel" ]; then
        cp -a "$SITE_ROOT/current/$rel/." "$SHARED/$rel/" 2>/dev/null || true
    fi
    src_count=$(find "$SITE_ROOT/current/$rel" -type f 2>/dev/null | wc -l | tr -d ' ')
    dst_count=$(find "$SHARED/$rel" -type f 2>/dev/null | wc -l | tr -d ' ')
    if [ "${src_count:-0}" -gt 0 ] && [ "${dst_count:-0}" -eq 0 ]; then
        echo "ERROR: no se pudieron copiar los archivos de $rel a shared"
        exit 1
    fi
    rm -rf "$rel"
    ln -sfn "$SHARED/$rel" "$rel"
}

share assets/empresas
share assets/demos
share assets/portfolio
share logs
share replicador/zip

# assets/img sigue en el release (está en git). Si shared/assets/img ya es
# un directorio, ln metería el enlace adentro; por eso se reemplaza.
if [ -d "$SITE_ROOT/current/assets/img" ] && [ ! -L "$SITE_ROOT/current/assets/img" ]; then
    if [ -e "$SHARED/assets/img" ] && [ ! -L "$SHARED/assets/img" ]; then
        rm -rf "$SHARED/assets/img"
    fi
    ln -sfn "$SITE_ROOT/current/assets/img" "$SHARED/assets/img"
fi

chmod -R ug+rwX "$SHARED/assets/empresas" "$SHARED/assets/demos" "$SHARED/assets/portfolio" "$SHARED/logs" "$SHARED/replicador/zip" 2>/dev/null || true

$ACTIVATE_RELEASE()
