<?php
  include_once('../banco_dados.php');

  $metodo = $_SERVER['REQUEST_METHOD'];
  $url_completa = $_SERVER['REQUEST_URI'];

  $divisao_url = explode('?', $url_completa);
  $segmentos_url = explode('/', $divisao_url[0]);
  $endpoint = end($segmentos_url);
  
  if($endpoint == 'usuarios') {
    $colunas = array("id", "nome", "sobrenome", "email");

    switch($metodo) {
      case 'GET':
        $resposta = listar_registros('usuarios', $colunas);
        break;
      case 'POST':
        if (count($_POST) == 0) {
          $resposta = array("mensagem" => "Erro ao cadastrar o usuário");
        } else {
          $registro_criado = criar_registro('usuarios', array(
            "nome" => $_POST["nome"],
            "sobrenome" => $_POST["sobrenome"],
            "email" => $_POST["email"]
          ));
          
          if ($registro_criado == true) {
            $resposta = array(
              "mensagem" => "Usuário cadastrado com sucesso",
              "sucesso" => true
            );
          } else {
            $resposta = array(
              "mensagem" => "Erro ao cadastrar o usuário",
              "sucesso" => false
            );
          }
        }

        break;
      case 'DELETE':
        $id = "";
        $parametros = explode("&", $divisao_url[1]);

        foreach($parametros as $parametro) {
          if (str_contains($parametro, "id") == true) {
            $id = explode('=', $parametro)[1];

            break;
          }
        }

        $exclusao_realizada = excluir_registro('usuarios', $id);

        if ($exclusao_realizada == true) {
          $resposta = array(
            "mensagem" => "Usuário excluído com sucesso",
            "sucesso" => true
          );
        } else {
          $resposta = array(
            "mensagem" => "Erro ao excluir o usuário",
            "sucesso" => false
          );
        }
    }

    echo json_encode($resposta);
  }
