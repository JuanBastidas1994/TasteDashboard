<?php
/* ==========================================================================
   NOTIFICACIONES PUSH (Expo) DESDE EL DASHBOARD
   Promos, productos nuevos y eventos para clientes finales.
   No mezclar con cl_notificaciones.php (ese es el flujo FCM/órdenes).
   Lee tb_push_tokens (la llena api_gestion_ordenes/api), no se llama
   ningún endpoint de esos proyectos: misma base de datos (jc_taste).
   ========================================================================== */

class cl_notificaciones_expo
{
    public $session;
    public $cod_empresa;

    public function __construct()
    {
        $this->session = getSession();
        $this->cod_empresa = $this->session['cod_empresa'];
    }

    public function lista(){
        $query = "SELECT * FROM tb_notificaciones_expo WHERE cod_empresa = ".$this->session['cod_empresa']." ORDER BY fecha DESC";
        $resp = Conexion::buscarVariosRegistro($query);
        return $resp;
    }

    /* Punto único de entrada: todo lo que se notifica desde aquí pasa por este método */
    public function enviar($tipo, $titulo, $mensaje, $data = [], $cod_usuarios = null)
    {
        $tokens = $cod_usuarios
            ? $this->obtenerTokensPorUsuarios($cod_usuarios)
            : $this->obtenerTokensEmpresa($this->cod_empresa);

        if (empty($tokens)) {
            return ['success' => 0, 'mensaje' => 'No hay clientes con notificaciones push registradas', 'total_enviados' => 0];
        }

        $data['type'] = $tipo;
        $this->enviarExpoPush($tokens, $titulo, $mensaje, $data);

        $totalEnviados = count($tokens);
        $this->registrarHistorial($tipo, $titulo, $mensaje, $data, $totalEnviados);

        return ['success' => 1, 'mensaje' => 'Notificacion enviada', 'total_enviados' => $totalEnviados];
    }

    public function historial()
    {
        $query = "SELECT * FROM tb_notificaciones_expo WHERE cod_empresa = :cod_empresa ORDER BY fecha DESC LIMIT 0,50";
        return Conexion::buscarVariosRegistro($query, [':cod_empresa' => $this->cod_empresa]);
    }

    /* Tokens de todos los clientes activos de la empresa (broadcast) */
    private function obtenerTokensEmpresa($cod_empresa)
    {
        $query = "SELECT t.token
                    FROM tb_push_tokens t
                    INNER JOIN tb_clientes c ON c.cod_usuario = t.cod_usuario
                    WHERE c.cod_empresa = :cod_empresa
                    AND c.estado = 'A'";
        $registros = Conexion::buscarVariosRegistro($query, [':cod_empresa' => $cod_empresa]);
        if (!$registros) return [];
        return array_column($registros, 'token');
    }

    /* Tokens de clientes puntuales (por si más adelante se segmenta el envío) */
    private function obtenerTokensPorUsuarios($cod_usuarios)
    {
        $placeholders = [];
        $data = [];
        foreach ($cod_usuarios as $i => $cod_usuario) {
            $placeholders[] = ":cod_usuario{$i}";
            $data[":cod_usuario{$i}"] = $cod_usuario;
        }

        $query = "SELECT token FROM tb_push_tokens WHERE cod_usuario IN (" . implode(',', $placeholders) . ")";
        $registros = Conexion::buscarVariosRegistro($query, $data);
        if (!$registros) return [];
        return array_column($registros, 'token');
    }

    /* Envío crudo a la API de Expo, en chunks de 100 (límite de Expo por request) */
    private function enviarExpoPush($tokens, $titulo, $mensaje, $data = [])
    {
        $mensajes = [];
        foreach ($tokens as $token) {
            $mensajes[] = [
                "to" => $token,
                "title" => $titulo,
                "body" => $mensaje,
                "data" => $data,
            ];
        }

        foreach (array_chunk($mensajes, 100) as $chunk) {
            $ch = curl_init("https://exp.host/--/api/v2/push/send");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Accept: application/json",
                "Accept-Encoding: gzip, deflate",
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($chunk));
            curl_exec($ch);
            curl_close($ch);
        }
    }

    private function registrarHistorial($tipo, $titulo, $mensaje, $data, $totalEnviados)
    {
        Conexion::ejecutar("SET NAMES 'utf8mb4'", NULL);
        $query = "INSERT INTO tb_notificaciones_expo(cod_empresa, tipo, titulo, mensaje, data, cod_usuario_admin, total_enviados, fecha)
                    VALUES (:cod_empresa, :tipo, :titulo, :mensaje, :data, :cod_usuario_admin, :total_enviados, NOW())";
        return Conexion::ejecutar($query, [
            ':cod_empresa' => $this->cod_empresa,
            ':tipo' => $tipo,
            ':titulo' => htmlentities($titulo),
            ':mensaje' => htmlentities($mensaje),
            ':data' => json_encode($data),
            ':cod_usuario_admin' => $this->session['cod_usuario'],
            ':total_enviados' => $totalEnviados,
        ]);
    }
}
?>
