<!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Document</title>
      <link rel="stylesheet" href="./estilos.css" />
    </head>
    <body>
        <div class="container">
          <form class="formulario" id="form-usuario">
            <input type="text" placeholder="Nome" id="input-nome" class="margem"/>
            <input type="text" placeholder="Sobrenome" id="input-sobrenome" class="margem"/>
            <input type="text" placeholder="Email" id="input-email" class="margem"/>
            <button type="submit">+ Usuario</button>
          </form>

          <select id="select-usuarios">
            <option value="-1" selected disabled>Selecione um usuário</option>
          </select>

          <form class="formulario" id="form-transacao">
            <select id="select-operacao" class="margem" disabled>
              <option value="CREDITO" selected>Crédito</option>
              <option value="DEBITO">Débito</option>
            </select>
            <input type="number" min="0" step="0.1" placeholder="Valor" id="input-valor" class="margem" disabled/>
            <button type="submit" id="botao-transacao" disabled>+ Transação</button>
          </form>

          <table id="transacoes">
            <thead>
              <th>Operação</th>
              <th>Valor</th>
            </thead>
            <tbody>
              <tr>
                <td colspan="2">Sem transações</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td></td>
                <td id="total">Total: 0</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- <div id="usuarios">

        </div>

        <br />

        <div id="transacoes">

        </div> -->
    </body>
    <script>
      let idUsuarioSelecionado = null;

      function listarUsuarios(usuarios) {
        let html = '<option value="-1" selected disabled>Selecione um usuário</option>';

        usuarios.forEach(usuario => {
          html += "<option value=" + usuario.id + ">" + usuario.nome + " " + usuario.sobrenome + "</option>";
        });

        document.querySelector('select').innerHTML = html;
      }

      function listarTransacoes(transacoes) {
        let htmlTransacoes = '';
        let htmlTotal = "";
        let total = 0;

        if (transacoes.length > 0) {
          transacoes.forEach(transacao => {
            htmlTransacoes += "<tr><td>" + transacao.operacao + "</td><td>" + transacao.valor + "</td></tr>";
            
            if (transacao.operacao === 'CREDITO') {
              total += Number(transacao.valor);
            } else {
              total -= Number(transacao.valor);
            }
          });
        } else {
          htmlTransacoes = '<tr><td colspan="2">Sem transações</td></tr>';
        }

        document.querySelector('tbody').innerHTML = htmlTransacoes;
        document.querySelector('#total').innerHTML = "Total: " + total;
      }

      document.querySelector('#form-usuario').addEventListener('submit', event => {
        event.preventDefault();

        const inputNome = document.querySelector('#input-nome');
        const inputSobrenome = document.querySelector('#input-sobrenome');
        const inputEmail = document.querySelector('#input-email');

        const dados = new FormData();

        dados.append('nome', inputNome.value);
        dados.append('sobrenome', inputSobrenome.value);
        dados.append('email', inputEmail.value);

        fetch('api/usuarios', {
          method: 'POST',
          body: dados
        })
          .then(resposta => resposta.json())
          .then(dados => {
            if (dados.sucesso) {
              fetch('api/usuarios')
                .then(resposta => resposta.json())
                .then(usuarios => {
                  listarUsuarios(usuarios);
                  listarTransacoes([]);

                  inputNome.value = "";
                  inputSobrenome.value = "";
                  inputEmail.value = "";
                });
            }
          })
      });

      fetch('api/usuarios')
        .then(resposta => resposta.json())
        .then(usuarios => listarUsuarios(usuarios));

      document
        .querySelector('#select-usuarios')
        .addEventListener('change', event => {
          event.preventDefault();

          const inputOperacoes = document.querySelector('#select-operacao');
          const inputValor = document.querySelector('#input-valor');
          const botaoTransacoes = document.querySelector('#botao-transacao');
          
          const opcoes = Array.from(event.target.selectedOptions);

          idUsuarioSelecionado = opcoes.pop().value;

          fetch('api/transacoes?id_usuario=' + idUsuarioSelecionado)
            .then(resposta => resposta.json())
            .then(transacoes => listarTransacoes(transacoes));

          inputOperacoes.removeAttribute('disabled');
          inputValor.removeAttribute('disabled');
          botaoTransacoes.removeAttribute('disabled');
        });

        document
          .querySelector('#botao-transacao')
          .addEventListener('click', event => {
            event.preventDefault();

            const inputOperacoes = document.querySelector('#select-operacao');
            const inputValor = document.querySelector('#input-valor');
        
            const dados = new FormData();
            const operacao = Array.from(inputOperacoes.selectedOptions).pop().value;

            dados.append('usuario', idUsuarioSelecionado);
            dados.append('operacao', operacao);
            dados.append('valor', document.querySelector('#input-valor').value);

            fetch('api/transacoes', {
              method: 'POST',
              body: dados
            })
              .then(resposta => resposta.json())
              .then(dados => {
                if (dados.sucesso) {
                  fetch('api/transacoes?usuario=' + idUsuarioSelecionado)
                    .then(resposta => resposta.json())
                    .then(transacoes => {
                      listarTransacoes(transacoes);

                      inputOperacoes.value = 'CREDITO';
                      inputValor.value = '';
                    });
                }
              });
          });

          
      // fetch('api/usuarios')
      //   .then(resposta => resposta.json())
      //   .then(usuarios => {
      //     document.querySelector('#usuarios').innerHTML = JSON.stringify(usuarios)
      //   });

      // fetch('api/transacoes')
      //   .then(resposta => resposta.json())
      //   .then(transacoes => {
      //     document.querySelector('#transacoes').innerHTML = JSON.stringify(transacoes)
      //   });
    </script>
  </html>