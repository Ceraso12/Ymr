# YMR — site inicial

Site criado do zero para a nova loja YMR. `index.html` é a Home com a experiência de rolagem; `pet.html`, `autorais.html`, `brindes.html` e `variados.html` são páginas próprias para cada área. Publique o conteúdo desta pasta na hospedagem para manter os caminhos entre páginas e os arquivos de estilo e imagem.

## Estrutura

- `index.html`: Home, apresentação da marca, destaques das áreas, navegação por rolagem e dúvidas frequentes.
- `pet.html`, `autorais.html`, `brindes.html`, `variados.html`: páginas diretas de cada departamento, cada uma com seu tratamento visual.
- `styles.css`: identidade, responsividade e transições visuais.
- `app.js`: catálogo demonstrativo, opções de produto e sacola local.
- `assets/`: logo e mascote enviados como referência.

## Conteúdo demonstrativo

Os nomes, descrições e opções iniciais dos produtos em `app.js` são conteúdo demonstrativo; preços não foram inventados e aparecem como pendentes. As imagens finais de produto ainda devem ser adicionadas. Para produtos Sanctum, a estrutura já prevê disponibilidade por tamanho; o painel administrativo poderá editar esses valores quando houver um backend persistente.

## Integrações planejadas

O projeto já tem navegação entre páginas, seleção de opções e sacola salva no navegador. Ainda não envia pedidos nem processa pagamentos. O cotador do Melhor Envio, checkout Mercado Pago, registro persistente de pedidos, e-mail de confirmação, WhatsApp Business e painel de administração exigem um backend/serviço de dados e credenciais próprias. Os avisos atuais na sacola indicam esses pontos pendentes; nenhum valor de frete fictício é apresentado.

## Próximos passos de implementação

1. Definir plataforma de backend/banco de dados compatível com a hospedagem HostGator.
2. Conectar catálogo, disponibilidade, fotos e textos ao painel administrativo.
3. Integrar cotação real do Melhor Envio com CEP de origem, peso e dimensões dos produtos.
4. Integrar Mercado Pago e confirmação de pagamento via webhook.
5. Enviar e-mail com o pedido e, para produtos personalizados, mensagem transacional via WhatsApp Business Platform com consentimento do cliente.
6. Substituir produtos demonstrativos por fotos, preços, medidas e textos aprovados.
