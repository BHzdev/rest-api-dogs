<?php
function photo_data($post){
    $post_meta = get_post_meta($post->ID);
    $src = wp_get_attachment_image_src($post_meta["img"][0], "large")[0];
    $user = get_userdata($post->post_author);
    $total_comments = get_comments_number($post->ID);

    // Retorna os dados estruturados.
    return [
        "id" => $post->ID,
        "author" => $user->user_login,
        "title" => $post->post_title,
        "date" => $post->post_date,
        "src" => $src,
        "peso" => $post_meta["peso"][0],
        "idade" => $post_meta["idade"][0],
        "acessos" => $post_meta["acessos"][0],
        "total_comments" => $total_comments
    ];
}

function api_photo_get($request) {
  $post_id = $request["id"];
  $post = get_post($post_id);

  //Verifica se o post solicitado existe.
  if(!isset($post) || empty($post_id)){
    $response = new WP_Erro("error", "Post não encontrado.", [
      "status" => 404
  ]);
  return rest_ensure_response($response);
  }

  $photo = photo_data($post);

  $photo['acessos'] = (int) $photo['acessos'] + 1;

  return rest_ensure_response($photo);
}

// Função para registrar o endpoint da API.
function register_api_photo_get() {
  register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
      'methods' => WP_REST_Server::READABLE,
      'callback' => 'api_photo_get'
  ]);
}

add_action('rest_api_init', 'register_api_photo_get');
?>