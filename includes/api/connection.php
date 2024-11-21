<?php
function kame_erp_get_access_token() {
    return get_option('kame_erp_access_token', '');
}

function kame_erp_check_connection() {
    $access_token = kame_erp_get_access_token();
    return $access_token ? true : false;
}

function kame_erp_enviar_datos_venta($order_id) {
    // Send sales data to API
}

function kame_erp_enviar_a_api($datos_venta) {
    // Send data to API logic
}

function kame_erp_check_and_refresh_token() {
    $token_expiration = get_option('kame_erp_token_expiration', 0);
    if (time() > $token_expiration) {
        fetch_and_store_kame_erp_access_token();
    }
}

function kame_erp_access_token_callback() {
    $access_token = get_option('kame_erp_access_token', '');
    echo '<input type="text" name="kame_erp_access_token" value="' . esc_attr($access_token) . '" style="width: 100%;" readonly>';
}

function update_kame_erp_access_token($token_response) {
    if (!empty($token_response->access_token)) {
        update_option('kame_erp_access_token', $token_response->access_token);
        update_option('kame_erp_token_expiration', time() + $token_response->expires_in);
    }
}

function fetch_and_store_kame_erp_access_token() {
    $client_id = get_option('kame_erp_client_id', '');
    $client_secret = get_option('kame_erp_client_secret', '');
    $usuario_kame = get_option('kame_erp_usuario_kame', '');

    $response = wp_remote_post('https://api.kameerp.com/oauth/token', array(
        'body' => array(
            'grant_type' => 'client_credentials',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'username' => $usuario_kame,
        ),
    ));

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    if (!empty($data->access_token)) {
        update_kame_erp_access_token($data);
        return $data->access_token;
    }

    return false;
}
