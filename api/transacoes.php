<?php
  include_once('../banco_dados.php');

  $metodo = $_SERVER['REQUEST_METHOD'];
  $url_completa = $_SERVER['REQUEST_URI'];

  $divisao_url = explode('?', $url_completa);
  $segmentos_url = explode('/', $divisao_url[0]);
  $endpoint = end($segmentos_url);
  
  if($endpoint == 'transacoes') {
    $colunas = array("id_usuario", "operacao", "valor");

    switch($metodo) {
      case 'GET':
        $transacoes = listar_registros('transacoes', $colunas);
        $resposta = $transacoes;

        if (count($divisao_url) > 1) {
          $id_usuario = "";
          $parametros = explode("&", $divisao_url[1]);

          foreach($parametros as $parametro) {
            if (str_contains($parametro, "usuario") == true) {
              $id_usuario = explode('=', $parametro)[1];

              break;
            }
          }

          if ($id_usuario !== "") {
            $transacoes_usuario = array();

            foreach($transacoes as $transacao) {
              if ($transacao["id_usuario"] == $id_usuario) {
                $transacoes_usuario[] = $transacao;
              }
            }
            
            $resposta = $transacoes_usuario;
          }
        }
        
        break;
      case 'POST':
        if (count($_POST) == 0) {
          $resposta = array("mensagem" => "Erro ao cadastrar a transação");
        } else {
          $registro_criado = criar_registro('transacoes', array(
            "id_usuario" => $_POST["usuario"],
            "operacao" => $_POST["operacao"],
            "valor" => $_POST["valor"]
          ));
          
          if ($registro_criado == true) {
            $resposta = array(
              "mensagem" => "Transação cadastrada com sucesso",
              "sucesso" => true
            );
          } else {
            $resposta = array(
              "mensagem" => "Erro ao cadastrar a transação",
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

        $exclusao_realizada = excluir_registro('transacoes', $id);

        if ($exclusao_realizada == true) {
          $resposta = array("mensagem" => "Transação excluída com sucesso");
        } else {
          $resposta = array("mensagem" => "Erro ao excluir a transação");
        }
    }

    echo json_encode($resposta);
  }
