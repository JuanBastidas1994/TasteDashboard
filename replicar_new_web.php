<?php
require_once "funciones.php";
require_once "clases/cl_empresas.php";

if(!isLogin()){
    header("location:login.php");
    exit;
}

$Clempresas = new cl_empresas(NULL);
$empresas = $Clempresas->getEmpresasPorTipo(1); //RESTAURANTES

// Archivos del servidor que no vienen en el zip y no se deben borrar
const NO_BORRAR = ['.well-known', 'demos.json', 'demos.secret'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    set_time_limit(120);
    header("Content-type:application/json; charset=utf-8");
    try {
        replicarEmpresa((int)($_POST['id'] ?? 0), $empresas);
        echo json_encode(['success' => 1, 'mensaje' => 'Replicado']);
    } catch (Throwable $e) {
        echo json_encode(['success' => 0, 'mensaje' => $e->getMessage()]);
    }
    exit;
}

function replicarEmpresa($id, $empresas){
    $porId = array_column($empresas, null, 'cod_empresa');
    if(!isset($porId[$id])) throw new Exception('Empresa no existe o no está activa');
    $empresa = $porId[$id];

    $folder = trim($empresa['folder'] ?? '', " /");
    if($folder === '') throw new Exception('La empresa no tiene carpeta');
    if($empresa['hosting'] !== 'taste') throw new Exception('El hosting no es taste');
    if(empty($empresa['api_key'])) throw new Exception('La empresa no tiene API key');

    // La carpeta se va a vaciar: tiene que estar dentro del hosting y no ser el hosting mismo
    $base = realpath(path_hosting);
    $path = realpath(path_hosting.'/'.$folder);
    if(!$base || !$path || !is_dir($path)) throw new Exception('Directorio no existe');
    if(strpos($path, rtrim($base, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR) !== 0){
        throw new Exception('Directorio fuera del hosting');
    }
    $sistema = realpath(__DIR__).DIRECTORY_SEPARATOR;
    if(strpos($sistema, $path.DIRECTORY_SEPARATOR) === 0) throw new Exception('Esa carpeta contiene el dashboard');
    foreach($porId as $otraId => $otra){
        $otroFolder = trim($otra['folder'] ?? '', " /");
        if($otraId != $id && strpos($otroFolder.'/', $folder.'/') === 0){
            throw new Exception('La carpeta también la usa: '.$otra['nombre']);
        }
    }

    // Se abre el zip antes de borrar, para no dejar la web vacía si el zip falla
    $zip = new ZipArchive;
    if($zip->open(__DIR__.'/new_page/base.zip') !== true) throw new Exception('No se pudo abrir new_page/base.zip');

    foreach(new FilesystemIterator($path) as $item){
        if(!in_array($item->getFilename(), NO_BORRAR)) borrar($item);
    }

    $ok = $zip->extractTo($path);
    $zip->close();
    if(!$ok) throw new Exception('Falló al descomprimir el zip');

    $tenant = json_encode(['API_KEY' => $empresa['api_key']], JSON_PRETTY_PRINT);
    if(file_put_contents($path.'/tenant.json', $tenant) === false) throw new Exception('No se pudo escribir tenant.json');
}

function borrar(SplFileInfo $item){
    // Un enlace simbólico se quita sin entrar en él
    if($item->isDir() && !$item->isLink()){
        foreach(new FilesystemIterator($item->getPathname()) as $hijo) borrar($hijo);
        $ok = rmdir($item->getPathname());
    } else {
        $ok = unlink($item->getPathname());
    }
    if(!$ok) throw new Exception('No se pudo borrar '.$item->getFilename().' (permisos)');
}

function e($texto){
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8">
    <?php css_mandatory(); ?>
</head>
<body>

    <?php echo top() ?>
    <?php echo navbar(false); ?>

    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <?php echo sidebar(); ?>

        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">
                    <div class="col-md-12" style="margin-top:25px;">
                        <h3 id="titulo">Replicador Web Nueva</h3>

                        <div class="d-flex">
                            <input type="text" class="form-control mr-2" id="txtBuscar" placeholder="Buscar empresa..." style="max-width: 350px;">
                            <button type="button" class="btn btn-primary" id="btnReplicar">Replicar</button>
                        </div>
                    </div>

                    <div class="col-12 layout-spacing">
                        <div class="widget-content widget-content-area br-6">
                            <div class="table-responsive">
                                <table class="table style-3 table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="chkTodos" title="Marcar las visibles" style="width: 30px; height: 30px;"></th>
                                            <th>Empresa</th>
                                            <th>Carpeta</th>
                                            <th class="text-center">Ambiente</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($empresas as $empresa):
                                            $disabled = ($empresa['folder'] == '' || $empresa['hosting'] !== 'taste') ? 'disabled' : '';
                                            $badge = ($empresa['ambiente'] == 'production') ? 'success' : 'danger';
                                        ?>
                                        <tr class="fila" data-nombre="<?= e($empresa['nombre']) ?>">
                                            <td>
                                                <input type="checkbox" class="chk_replicar" value="<?= e($empresa['cod_empresa']) ?>" <?= $disabled ?> style="width: 30px; height: 30px;">
                                            </td>
                                            <td><?= e($empresa['nombre']) ?><div class="estado small"></div></td>
                                            <td><?= e($empresa['folder']) ?></td>
                                            <td class="text-center"><span class="shadow-none badge badge-<?= $badge ?>"><?= e($empresa['ambiente']) ?></span></td>
                                            <td class="text-center">
                                                <ul class="table-controls">
                                                    <li><a href="crear_empresa.php?id=<?= e($empresa['alias']) ?>" target="_blank" title="Editar"><i data-feather="edit-2"></i></a></li>
                                                    <?php if($empresa['url_web'] != ''): ?>
                                                    <li><a href="https://<?= e($empresa['url_web']) ?>" target="_blank" title="Ver web"><i data-feather="globe"></i></a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php footer(); ?>
        </div>
    </div>

    <?php js_mandatory(); ?>
    <script>
    $(function(){
        function normalizar(texto){
            return String(texto).normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
        }

        $('#txtBuscar').on('input', function(){
            const buscar = normalizar(this.value);
            $('.fila').each(function(){
                $(this).toggle(normalizar($(this).attr('data-nombre')).indexOf(buscar) !== -1);
            });
            $('#chkTodos').prop('checked', false);
        });

        $('#chkTodos').on('change', function(){
            $('.fila:visible .chk_replicar:enabled').prop('checked', this.checked);
        });

        $('#btnReplicar').on('click', async function(){
            // Incluye las marcadas que el filtro tenga ocultas, por eso se listan al confirmar
            const marcadas = $('.chk_replicar:checked').toArray();
            if(!marcadas.length){
                alert('Marque al menos una empresa');
                return;
            }
            const nombres = marcadas.map(chk => $(chk).closest('tr').attr('data-nombre'));
            if(!confirm('Se borrará el contenido de la carpeta de ' + marcadas.length + ' empresa(s):\n\n' + nombres.join('\n') + '\n\n¿Continuar?')) return;

            const $btn = $(this).prop('disabled', true).text('Replicando...');
            for(const chk of marcadas){
                const $estado = $(chk).closest('tr').find('.estado').attr('class', 'estado small text-muted').text('Replicando...');
                try {
                    const resp = await $.post('replicar_new_web.php', { id: chk.value });
                    $estado.attr('class', 'estado small ' + (resp.success == 1 ? 'text-success' : 'text-danger')).text(resp.mensaje || 'Respuesta inválida');
                } catch (error) {
                    $estado.attr('class', 'estado small text-danger').text('Error en el servidor');
                }
            }
            $btn.prop('disabled', false).text('Replicar');
        });
    });
    </script>
</body>
</html>
