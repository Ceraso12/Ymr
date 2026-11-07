<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <title>Administração · YMR</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <header class="admin-top"><a class="admin-brand" href="../index.html"><b>YMR</b><span>PAINEL DA LOJA</span></a><span class="top-note">ÁREA RESTRITA</span></header>
  <main class="admin-main">
    <section class="auth-card" id="setup-view" hidden>
      <span class="eyebrow">PRIMEIRO ACESSO</span><h1>Configure seu<br>acesso de admin.</h1>
      <p>Use o código de configuração de uso único fornecido junto com esta versão. Depois, defina seu usuário e uma senha forte.</p>
      <form id="setup-form" class="stack-form"><label>Código de configuração<input name="setup_token" type="password" required autocomplete="off"></label><label>Usuário<input name="username" required autocomplete="username" maxlength="64"></label><label>Senha (mínimo 12 caracteres)<input name="password" type="password" required minlength="12" autocomplete="new-password"></label><button class="primary-button">Criar acesso protegido <span>↗</span></button></form>
      <div class="form-message" id="setup-message" role="status" aria-live="polite"></div>
    </section>
    <section class="auth-card" id="login-view" hidden>
      <span class="eyebrow">BEM-VINDA DE VOLTA</span><h1>Entre no<br>painel YMR.</h1>
      <p>Faça login para atualizar produtos, fotos e disponibilidade.</p>
      <form id="login-form" class="stack-form"><label>Usuário<input name="username" required autocomplete="username"></label><label>Senha<input name="password" type="password" required autocomplete="current-password"></label><button class="primary-button">Entrar no painel <span>↗</span></button></form>
      <div class="form-message" id="login-message" role="status" aria-live="polite"></div>
    </section>

    <section class="dashboard" id="dashboard-view" hidden>
      <div class="dashboard-heading"><div><span class="eyebrow">CATÁLOGO YMR</span><h1>Seus produtos,<br><em>do seu jeito.</em></h1></div><button id="logout-button" class="quiet-button">Sair do painel ↗</button></div>
      <div class="dashboard-columns">
        <section class="editor-panel">
          <div class="panel-heading"><div><span class="eyebrow">CADASTRO</span><h2 id="form-title">Adicionar produto</h2></div><button id="clear-form" class="quiet-button">Limpar</button></div>
          <form id="product-form" class="stack-form" enctype="multipart/form-data">
            <input type="hidden" name="id">
            <label>Nome do produto<input name="name" required maxlength="140" placeholder="Ex.: Leão de Judá"></label>
            <div class="form-pair"><label>Seção<select name="section" required><option value="pet">Pet</option><option value="autorais">Autorais</option><option value="brindes">Brindes</option><option value="variados">Variados</option></select></label><label class="collection-field">Coleção<input name="collection" maxlength="100" placeholder="Ex.: Sanctum"></label></div>
            <label>Categoria / etiqueta<input name="category" maxlength="100" placeholder="Ex.: Sanctum · Estampa 07"></label>
            <label>Descrição<textarea name="description" rows="3" maxlength="800" placeholder="Conte o que torna este produto especial."></textarea></label>
            <div class="form-pair"><label>Preço (R$)<input name="price" inputmode="decimal" placeholder="Deixe em branco para orçamento"></label><label>Opções / tamanhos<textarea name="options" rows="2" placeholder="P, M, G, GG"></textarea></label></div>
            <label>Cores disponíveis<input name="colors" placeholder="Off-white, preto"></label>
            <fieldset class="stock-field"><legend>Disponibilidade das opções</legend><div id="availability-options" class="availability-options"><span>Adicione opções para configurar a disponibilidade.</span></div></fieldset>
            <label class="upload-label">Foto do produto<input name="image" type="file" accept="image/png,image/jpeg,image/webp"><small>JPG, PNG ou WebP · até 8 MB. Se editar sem escolher uma nova foto, a atual será mantida.</small></label>
            <button class="primary-button" id="save-button">Salvar produto <span>↗</span></button>
            <div class="form-message" id="save-message" role="status" aria-live="polite"></div>
          </form>
        </section>
        <section class="products-panel"><div class="panel-heading"><div><span class="eyebrow">SEU CATÁLOGO</span><h2>Produtos</h2></div><span class="count-badge" id="product-count">0</span></div><div id="admin-products" class="admin-products"><p class="loading-note">Carregando produtos…</p></div></section>
      </div>
      <p class="privacy-note">As alterações são salvas no servidor e aparecem no catálogo público. Senhas e catálogo ficam fora do GitHub.</p>
    </section>
  </main>
  <script src="admin.js"></script>
</body>
</html>
