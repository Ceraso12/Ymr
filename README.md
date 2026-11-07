# YMR — site inicial

Site criado do zero para a nova loja YMR. `index.html` é a Home com a experiência de rolagem; `pet.html`, `autorais.html`, `brindes.html` e `variados.html` são páginas próprias para cada área. Publique o conteúdo desta pasta na hospedagem para manter os caminhos entre páginas e os arquivos de estilo e imagem.

## Estrutura

- `index.html`: Home, apresentação da marca, destaques das áreas, navegação por rolagem e dúvidas frequentes.
- `pet.html`, `autorais.html`, `brindes.html`, `variados.html`: páginas diretas de cada departamento, cada uma com seu tratamento visual.
- `styles.css`: identidade, responsividade e transições visuais.
- `app.js`: catálogo, opções de produto, carregamento do catálogo do servidor e sacola local.
- `admin/index.php`: painel protegido para cadastro, edição e arquivamento de produtos.
- `api/`: login, upload validado e catálogo público em PHP.
- `storage/seed-products.json`: catálogo inicial; alterações são salvas em `storage/catalog.json`, ignorado pelo Git.
- `assets/`: logo e mascote enviados como referência.

## Conteúdo demonstrativo

Os nomes, descrições e opções iniciais do catálogo são conteúdo de partida; preços não foram inventados e aparecem como pendentes. As fotos finais das outras categorias e os preços precisam ser adicionados. Para produtos Sanctum, o painel edita disponibilidade por tamanho.

As seis imagens recebidas para Sanctum foram incluídas em `assets/sanctum/` e aparecem nos cartões e detalhes das camisetas: Leão de Judá, Ave Maria, Nascimento de Jesus, Ícaro, Cruzadas e Mãos em oração. A página Sanctum usa uma paleta escura e mantém o off-white como cor da camiseta.

## Integrações planejadas

O projeto já tem navegação entre páginas, seleção de opções, sacola salva no navegador e o código do painel administrativo. Ainda não envia pedidos nem processa pagamentos. Cotação real do Melhor Envio, checkout Mercado Pago, pedidos, e-mail de confirmação e WhatsApp Business exigem integrações e credenciais próprias. Os avisos atuais na sacola indicam esses pontos pendentes; nenhum valor de frete fictício é apresentado.

## Painel de administração

O painel está em `/admin/` e requer PHP com sessões e permissões de escrita nas pastas `storage/` e `assets/uploads/`. Na primeira abertura, use o código de instalação único entregue separadamente para definir usuário e senha. O segredo não fica no repositório. Produtos editados são persistidos no JSON privado `storage/catalog.json`; os arquivos `.htaccess` bloqueiam acesso HTTP direto ao diretório privado. As imagens do painel aceitam JPG, PNG e WebP, até 8 MB. Não publique o site antes de concluir a configuração inicial do painel.

O catálogo do painel pode receber produtos em Pet, Autorais (coleção Sanctum), Brindes e Variados. Outras coleções autorais poderão usar o mesmo campo de coleção; a direção visual específica de uma coleção nova ainda precisa ser configurada no site.

## Próximos passos de implementação

1. Publicar esta pasta no domínio HostGator com suporte a PHP e conferir permissões de escrita em `storage/` e `assets/uploads/`.
2. Abrir `/admin/` e concluir a configuração inicial com o código de uso único; definir usuário e senha próprios.
3. Adicionar as fotos, preços, medidas e textos finais das outras categorias pelo painel.
4. Integrar cotação real do Melhor Envio com CEP de origem, peso e dimensões dos produtos.
5. Integrar Mercado Pago e confirmação de pagamento via webhook.
6. Enviar e-mail com o pedido e, para produtos personalizados, mensagem transacional via WhatsApp Business Platform com consentimento do cliente.

