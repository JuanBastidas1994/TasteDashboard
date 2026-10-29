<?php

/**
 * Verifica el id_token de Google Sign-In contra Google (firma, vencimiento y audiencia). api_flotas confiaba en el correo
 * que mandaba el celular, con lo que cualquiera podía entrar como otro motorizado; acá solo vale lo que Google confirmó.
 */
class cl_riders_google {

    /** @return array{google_id:string, email:?string, email_verified:bool, name:?string, picture:?string} */
    public function verificar($idToken) {
        $autoload = __DIR__ . '/../../../google-api/vendor/autoload.php';
        $ids = array_filter(array_map('trim', explode(',', RIDERS_GOOGLE_CLIENT_IDS)));
        if (!is_readable($autoload) || !$ids) {
            logError(new RuntimeException('Google: falta google-api/vendor o RIDERS_GOOGLE_CLIENT_IDS'));
            throw new RidersException('No pudimos validar tu cuenta de Google. Intenta de nuevo.');
        }
        require_once $autoload;

        foreach ($ids as $id) {
            try {
                $client = new Google\Client(['client_id' => $id]);
                $payload = $client->verifyIdToken($idToken);
            } catch (Throwable $e) {
                logError($e);
                continue;
            }
            if ($payload && !empty($payload['sub'])) {
                return [
                    'google_id' => (string) $payload['sub'],
                    'email' => !empty($payload['email']) ? strtolower($payload['email']) : null,
                    'email_verified' => filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'name' => $payload['name'] ?? null,
                    'picture' => $payload['picture'] ?? null,
                ];
            }
        }
        throw new RidersException('No pudimos validar tu cuenta de Google. Intenta de nuevo.');
    }
}
