<?php
/**
 * Vínculo de un usuario del dashboard con su chat de Telegram (módulo Perfil).
 *
 * Flujo: el usuario entra a Perfil y se crea una fila pendiente (estado 'P') con un código.
 * Ese código se lo escribe al bot (@tasteordenes_bot). El servicio del bot (api.mie-commerce.com/bot)
 * lo encuentra, guarda chat_id/user_id y pasa la fila a 'A'. Desde ese momento el usuario recibe los avisos
 * (nueva orden / orden asignada a la flota) que envían api y api_gestion_ordenes si la empresa tiene
 * el permiso NOTIFY_TELEGRAM.
 *
 * Aquí no se envía nada ni hay token: solo se administra la fila de tb_telegram_usuarios.
 */
class cl_telegram
{
    public function listaTelegramUsuarios($cod_usuario)
    {
        $query = "SELECT u.nombre, u.apellido, tu.*
                  FROM tb_telegram_usuarios tu
                  INNER JOIN tb_usuarios u ON u.cod_usuario = tu.cod_usuario
                  WHERE tu.cod_usuario = :cod_usuario";
        return Conexion::buscarVariosRegistro($query, [':cod_usuario' => intval($cod_usuario)]);
    }

    public function crearTelegramUsuarios($cod_usuario)
    {
        $cod_usuario = intval($cod_usuario);
        $code = $this->generarCodigoAleatorio() . $cod_usuario;
        $query = "INSERT INTO tb_telegram_usuarios (cod_usuario, chat_id, user_id, code, estado)
                  VALUES (:cod_usuario, '', '', :code, 'P')";
        return Conexion::ejecutar($query, [':cod_usuario' => $cod_usuario, ':code' => $code]);
    }

    public function deleteTelegramUsuarios($cod_usuario)
    {
        $query = "DELETE FROM tb_telegram_usuarios WHERE cod_usuario = :cod_usuario";
        return Conexion::ejecutar($query, [':cod_usuario' => intval($cod_usuario)]);
    }

    // 6 caracteres al azar (sin 0/O/1/I para no confundirlos al escribirlos) + el código de usuario, ej. KQT7M31234.
    // El código es la única llave para vincular un chat con el usuario: por eso no debe poder adivinarse.
    private function generarCodigoAleatorio($longitud = 6)
    {
        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $codigo = '';
        for ($i = 0; $i < $longitud; $i++) {
            $codigo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
        }
        return $codigo;
    }
}
