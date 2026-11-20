
document.querySelectorAll('.chatbot').forEach((chatbot) => {

    const botao = chatbot.querySelector('.chatbot-botao');
    const janela = chatbot.querySelector('.chatbot-janela');
    const fechar = chatbot.querySelector('.chatbot-fechar');
    const formulario = chatbot.querySelector('.chatbot-formulario');
    const input = chatbot.querySelector('.chatbot-input');
    const mensagens = chatbot.querySelector('.chatbot-mensagens');
    const enviar = chatbot.querySelector('.chatbot-enviar');

    const tipo = chatbot.dataset.chatbot;

    botao.addEventListener('click', () => {
        janela.hidden = !janela.hidden;

        if (!janela.hidden) {
            input.focus();
        }
    });

    fechar.addEventListener('click', () => {
        janela.hidden = true;
    });

    function adicionarMensagem(texto, classe) {

        const mensagem = document.createElement('div');

        mensagem.classList.add('mensagem', classe);
        mensagem.textContent = texto;

        mensagens.appendChild(mensagem);

        mensagens.scrollTop = mensagens.scrollHeight;

        return mensagem;
    }

    formulario.addEventListener('submit', async (evento) => {

        evento.preventDefault();

        const texto = input.value.trim();

        if (!texto) {
            return;
        }

        adicionarMensagem(texto, 'mensagem-usuario');

        input.value = '';

        input.disabled = true;
        enviar.disabled = true;

        const carregando = adicionarMensagem(
            'Pensando...',
            'mensagem-ia'
        );

        try {

            const resposta = await fetch('/app/chatbot/api.php', {

                method: 'POST',

                headers: {
                    'Content-Type': 'application/json'
                },

                credentials: 'same-origin',

                body: JSON.stringify({
                    tipo: tipo,
                    mensagem: texto
                })

            });

            const dados = await resposta.json();

            carregando.remove();

            if (!resposta.ok) {
                throw new Error(dados.resposta || 'Erro ao enviar mensagem.');
            }

            adicionarMensagem(
                dados.resposta,
                'mensagem-ia'
            );

        } catch (erro) {

            carregando.remove();

            adicionarMensagem(
                erro.message || 'Não foi possível conectar ao assistente.',
                'mensagem-ia'
            );

        } finally {

            input.disabled = false;
            enviar.disabled = false;
            input.focus();

        }

    });

});
