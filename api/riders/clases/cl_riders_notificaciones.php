<?php

/**
 * Notificaciones de la app de motorizados, solo por Expo Push (tb_push_tokens: los tokens de motorizados conviven con los
 * de clientes/cajeros). También avisa a Firebase para que Gestión de Órdenes se refresque y facture al entregar.
 */
class cl_riders_notificaciones {

    /** Un celular que cambia de motorizado conserva su token: pasa a quien inició sesión ahora. */
    public function suscribir($cod_usuario, $token, $plataforma, $sonido = null) {
        $existente = Conexion::buscarRegistro("SELECT id FROM tb_push_tokens WHERE token = :token", ['token' => $token]);
        if ($existente) {
            return Conexion::ejecutar(
                "UPDATE tb_push_tokens SET cod_usuario = :cod_usuario, plataforma = :plataforma, sonido = :sonido WHERE token = :token",
                ['cod_usuario' => $cod_usuario, 'plataforma' => $plataforma, 'sonido' => $sonido, 'token' => $token]
            );
        }
        return Conexion::ejecutar(
            "INSERT INTO tb_push_tokens (cod_usuario, token, plataforma, sonido) VALUES (:cod_usuario, :token, :plataforma, :sonido)",
            ['cod_usuario' => $cod_usuario, 'token' => $token, 'plataforma' => $plataforma, 'sonido' => $sonido]
        );
    }

    public function desuscribir($cod_usuario, $token) {
        return Conexion::ejecutar("DELETE FROM tb_push_tokens WHERE token = :token AND cod_usuario = :cod_usuario", ['token' => $token, 'cod_usuario' => $cod_usuario]);
    }

    /** Push vía Expo al usuario indicado. Nunca rompe la respuesta al motorizado. */
    public function enviarPush($cod_usuario, $titulo, $mensaje, $data = []) {
        // EXPO_PUSH_URL=0 (o vacío) lo apaga: una base local copia de producción tiene celulares de clientes reales
        if (!EXPO_PUSH_URL) return false;
        try {
            $tokens = Conexion::buscarVariosRegistro("SELECT token, sonido FROM tb_push_tokens WHERE cod_usuario = :cod_usuario", ['cod_usuario' => $cod_usuario]);
            if (!$tokens) return false;

            $mensajes = [];
            foreach ($tokens as $t) {
                $mensajes[] = [
                    'to' => $t['token'], 'title' => $titulo, 'body' => $mensaje, 'sound' => 'default',
                    // El sonido real en Android lo decide el canal (uno por sonido, creado en el celular)
                    'channelId' => 'pedidos_' . ($t['sonido'] ?: 'alarm_clock'),
                    'data' => $data,
                ];
            }

            $ch = curl_init(EXPO_PUSH_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mensajes));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $respuesta = curl_exec($ch);
            curl_close($ch);
            return $respuesta;
        } catch (Throwable $e) {
            logError($e);
            return false;
        }
    }

    /** Push de seguimiento al cliente: el link abre /tracking/v2/{token} firmado. */
    public function pushSeguimiento($cod_cliente, $cod_orden, $titulo, $mensaje) {
        if (empty($cod_cliente)) return false;
        return $this->enviarPush($cod_cliente, $titulo, $mensaje, ['type' => 'order_tracking', 'orden_id' => generarTracking($cod_orden)]);
    }

    /**
     * Mismo patrón que los webhooks de Gacela/PedidosYa/Picker: un PUT liviano al REST de Firebase, sin SDK. El panel de
     * Gestión de Órdenes escucha ese nodo para refrescarse y disparar la facturación automática al llegar a ENTREGADA.
     */
    public function firebaseEstado($cod_empresa, $cod_orden, $cod_sucursal, $estado) {
        // RIDERS_FIREBASE_PROJECT=0 (o vacío) lo apaga: no se escribe en el Firebase de producción desde una base local
        if (!RIDERS_FIREBASE_PROJECT) return;
        try {
            $empresa = Conexion::buscarRegistro("SELECT alias FROM tb_empresas WHERE cod_empresa = :cod_empresa", ['cod_empresa' => $cod_empresa]);
            if (!$empresa) return;

            $data = json_encode(['estado' => $estado, 'id' => (int) $cod_orden, 'sucursal' => (int) $cod_sucursal]);
            $ch = curl_init('https://' . RIDERS_FIREBASE_PROJECT . '.firebaseio.com/ordenes/' . $empresa['alias'] . '/' . $cod_orden . '.json');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_exec($ch);
            curl_close($ch);
        } catch (Throwable $e) {
            logError($e);
        }
    }
}
