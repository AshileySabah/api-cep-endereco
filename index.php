<?php
  $erroCep = "";
  $erroLogradouro = "";
  $erroCidade = "";
  $erroForm2 = "";
  $erroPesquisaCep = "";
  $erroPesquisaLogradouro = "";
  $mostrarPesquisaCep = false;
  $mostrarPesquisaLogradouro = false;

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['btn-form1'])){
      if(isset($_POST['cep'])){
        $cep = $_POST['cep'];
        $tamanhoCep = strlen($cep);
        if(!preg_match("/[0-9]/",$_POST['cep']) || preg_match("/[-.*,+$}{%#@!?°º\|ª³²¹¢¬><`´~^)(]/",$_POST['cep'])){
          $erroCep = "*O CEP deve conter apenas números.";
        }
        else{
          if($tamanhoCep == 8){
            $url = "viacep.com.br/ws/".$_POST['cep']."/json/";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $resultado = json_decode(curl_exec($ch));
            /*
            
            */

            if(isset($resultado->erro)){
              $erroPesquisaCep = "Infelizmente não temos informações sobre CEP desejado. Tente outro CEP.";
            }
            else{
              $rua = $resultado->logradouro;
              $cidade = $resultado->bairro;
              $estado = $resultado->uf;
              $mostrarPesquisaCep = true;
            }
          }
          else{
            $erroCep = "*O CEP deve ter 8 dígitos.";
          }
        }
      }
    }
    else if(isset($_POST['btn-form2'])){
      $testeLogradouro = isset($_POST['logradouro']) && ($_POST['logradouro'] == '') || ($_POST['logradouro'] == null) || strlen($_POST['logradouro']) < 3;
      $testeCidade = isset($_POST['cidade']) && ($_POST['cidade'] == '') || ($_POST['cidade'] == null) || strlen($_POST['cidade']) < 2;
      if($testeLogradouro){
        $erroLogradouro = "*Logradouro com informação inválida.";
      }
      if($testeCidade){
        $erroCidade = "*Cidade com informação inválida.";
      }
      else{
        $logradouro = $_POST['logradouro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];

        $url = "viacep.com.br/ws/".$estado."/".$cidade."/".$logradouro."/json/";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $resultado = json_decode(curl_exec($ch));
        
        if($resultado == null){
          $erroPesquisa = "Infelizmente não temos informações sobre o endereço desejado. Tente outro o endereço.";
        }
        else{
          $cepPesquisa = $resultado[0]->cep;
          $mostrarPesquisaLogradouro = true;
        }
      }
    }
  }
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <title>Consulta de Endereço</title>
  </head>
  <body style="background-color: #D6EAF8;">
    <!--HEADER-->
    <nav class="navbar navbar-dark bg-success">
      <div class="container">
        <a class="navbar-brand" href="#">
          Consultar Endereço
        </a>
      </div>
    </nav>
    <!--HEADER-->
    <!--CONTEÚDO-->
    <div class="container">
      <h1 class="mt-3 mb-5">Buscar CEP ou Endereço</h1>
      <div class="row">
        <div class="col-lg mb-5">
          <div class="card">
            <div class="card-header text-center">
              Encontrar logradouro, cidade e estado
            </div>
            <div class="card-body">
              <h5 class="card-title">Para saber seu logradouro, cidade e estado basta:</h5>
              <p class="card-text">Digitar o CEP.</p>
              <form method="post">
                <div class="mb-3">
                  <label for="formGroupExampleInput" class="form-label">CEP</label>
                  <input name="cep" type="text" class="form-control" id="formGroupExampleInput" maxlength="8" placeholder="00000000">
                  <span style="color: red;"><?php echo $erroCep; ?></span>
                </div>
                <button name="btn-form1"  class="btn btn-primary">Buscar</button>
                <span></span>
                <?php if($mostrarPesquisaCep){ ?>
                  <p class="mt-4" >Rua: <?php echo $rua; ?></p>
                  <p>Cidade: <?php echo $cidade; ?></p>
                  <p>Estado: <?php echo $estado; ?></p>
                <?php } ?>
                <?php if(!$mostrarPesquisaCep){ ?>
                  <p class="mt-4"><?php echo $erroPesquisaCep; ?></p>
                <?php } ?>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg mb-5">
          <div class="card">
            <div class="card-header text-center">
              Encontrar CEP
            </div>
            <div class="card-body">
              <h5 class="card-title">Para saber seu CEP basta:</h5>
              <p class="card-text">Digitar o logradouro e cidade e selecionar o estado.</p>
              <span style="color: red; display: block;"><?php echo $erroForm2 ?></span>
              <form method="post">
                <div class="mb-3">
                  <span style="color: red; display: block;"><?php echo $erroLogradouro; ?></span>
                  <label for="formGroupExampleInput" class="form-label">Logradouro</label>
                  <input name="logradouro" type="text" class="form-control" id="formGroupExampleInput" placeholder="Rua Margot Fonteyn">
                  <span style="color: red; display: block;"><?php echo $erroCidade; ?></span>
                  <label for="formGroupExampleInput" class="form-label">Cidade</label>
                  <input name="cidade" type="text" class="form-control" id="formGroupExampleInput" placeholder="Jardim Camargo Novo">
                  <label for="formGroupExampleInput" class="form-label">Estado</label>
                  <select name="estado" class="form-select">
                    <option value="AC">Acre</option>
                    <option value="AL">Alagoas</option>
                    <option value="AP">Amapá</option>
                    <option value="AM">Amazonas</option>
                    <option value="BA">Bahia</option>
                    <option value="CE">Ceará</option>
                    <option value="DF">Distrito Federal</option>
                    <option value="ES">Espírito Santo</option>
                    <option value="GO">Goiás</option>
                    <option value="MA">Maranhão</option>
                    <option value="MT">Mato Grosso</option>
                    <option value="MS">Mato Grosso do Sul</option>
                    <option value="MG">Minas Gerais</option>
                    <option value="PA">Pará</option>
                    <option value="PB">Paraíba</option>
                    <option value="PR">Paraná</option>
                    <option value="PE">Pernambuco</option>
                    <option value="PI">Piauí</option>
                    <option value="RJ">Rio de Janeiro</option>
                    <option value="RN">Rio Grande do Norte</option>
                    <option value="RS">Rio Grande do Sul</option>
                    <option value="RO">Rondônia</option>
                    <option value="RR">Roraima</option>
                    <option value="SC">Santa Catarina</option>
                    <option value="SP">São Paulo</option>
                    <option value="SE">Sergipe</option>
                    <option value="TO">Tocantins</option>
                  </select>
                </div>
                <button name="btn-form2" class="btn btn-primary">Buscar</button>
                <?php if($mostrarPesquisaLogradouro){ ?>
                  <p class="mt-4" >CEP: <?php echo $cepPesquisa; ?></p>
                <?php } ?>
                <?php if(!$mostrarPesquisaLogradouro){ ?>
                  <p class="mt-4"><?php echo $erroPesquisaLogradouro; ?></p>
                <?php } ?>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--CONTEÚDO-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
  </body>
</html>