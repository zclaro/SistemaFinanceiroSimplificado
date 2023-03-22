<?php

$banco = 'financeiro';
$host = "localhost";
$usuario = "root";
$senha = "";

$conexao = mysqli_connect($host, $usuario, $senha, $banco);

if (!$conexao) {
  die("Não foi possível conectar ao banco de dados: " . mysqli_connect_error());
}

function listar_registros($tabela, $colunas) {
  $resultados = mysqli_query($GLOBALS['conexao'], "SELECT * FROM " . $tabela . ";");
  $registros = array();

  while($registro = mysqli_fetch_array($resultados)) {
    $resultado = array();

    foreach($colunas as $coluna) {
      $resultado[$coluna] = $registro[$coluna];
    }

    $registros[] = $resultado;
  }

  return $registros;
}

function criar_registro($tabela, $dados) {
  if (!$dados) {
    return false;
  }

  $colunas = join(',', array_keys($dados));
  $valores = array();

  foreach(array_values($dados) as $dado) {
    $valores[] = "'" . $dado . "'"; 
  }

  $valores = join(',', $valores);

  $resultado = mysqli_query($GLOBALS['conexao'], "INSERT INTO " . $tabela . " (" . $colunas . ") VALUES (". $valores .");");

  if ($resultado) {
    return true;
  }

  return false;
}

function atualizar_registro($tabela, $id, $dados) {

}

function excluir_registro($tabela, $id) {
  if (!$id) {
    return false;
  }
  
  $resultado = mysqli_query($GLOBALS['conexao'], "DELETE FROM " . $tabela . " WHERE id = " . $id . ";");

  if ($resultado) {
    return true;
  }

  return false;
}