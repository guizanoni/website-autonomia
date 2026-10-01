# CLAUDE.md

Instruções do projeto para Claude Code e assistentes. Manter sucinto; nada de segredo ou token aqui.

Ver também: [MEMORY.md](MEMORY.md) (contexto e decisões), [CHANGELOG.md](CHANGELOG.md) (histórico), [AGENTS.md](AGENTS.md) (mesmo conteúdo, padrão multi-ferramenta).

---

## O que é

Landing page da **AUTONOM/IA**, uma *label* de bootcamps presenciais de IA com
**edições por público**. No ar: **AUTONOM/IA Mulheres** (Turma 1, 11/11/2026,
Hard Rock Café Curitiba, R$ 1.297). Cada edição futura (Líderes, Médicos…)
ganha a própria LP; não existe hub. Checkout externo (Asaas).

## Stack

**HTML estático + PHP só no checkout (`api/`).** Sem framework, sem build. Um `index.html`
na raiz mais `images/` e `brand/`. Caminhos relativos — a página funciona
aberta direto do disco.

## Como rodar

Abrir `index.html` no navegador. Para testar com servidor:

```bash
python3 -m http.server 8000
```

## Deploy

Automático: `git push` na `main` dispara `.github/workflows/deploy.yml`, que
sincroniza via **FTPS** para o HostGator (`/public_html/`). Não há build.

Secrets necessários no GitHub: `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`.

## Estrutura de páginas

- `/` (`index.html`) — só redireciona (JS + meta refresh, mantendo utm) pra edição em venda.
- `/mulheres/` (`mulheres/index.html`) — LP v13 da edição Mulheres. Caminhos de asset são absolutos (`/images/`, `/api/`). Modelo conceitual da
  home da Revolut: hero com a pessoa em tela cheia e a "tela" do que ela
  constrói por cima; no scroll a foto encolhe (clip-path) e vira um de três
  cards (os três perfis do ICP). Seções alternam branco/preto com um objeto
  visual grande e pílulas que trocam conteúdo.
- `CFG` no topo do `<script>` concentra o que muda por turma: data, links de
  checkout (individual/dupla), códigos de convite (hash SHA-256 → link),
  `soldOut` (vira lista de espera) e o endpoint da lista.
- Lista de espera posta no `enviar.php` do guizanoni.com (CORS liberado);
  nenhuma credencial neste repo, que é **público**.
- `/v2` e `/v3` — variantes antigas do bootcamp de empresas (fora de uso).

## Checkout (Asaas)

- A página abre um formulário próprio (modal `#coModal`) e posta em `api/checkout.php`,
  que calcula o preço **no servidor**, cria a cliente e a cobrança no Asaas e devolve o
  `invoiceUrl` (página segura do Asaas; dado de cartão nunca passa pelo nosso site).
- Regras de preço em `api/lib.php` (`EVENTO` e `calcular()`): R$ 1.297 no cartão até 10x,
  PIX -5%, dupla -5% cada, descontos não se somam; cupom de comunidade define o preço final.
- `api/webhook.php` recebe PAYMENT_CONFIRMED/RECEIVED (header `asaas-access-token`), marca o
  pedido como pago, conta vagas (esgota sozinho em 50) e avisa a equipe via `enviar.php`
  do guizanoni.com. Pedidos ficam em `api/_data/` (bloqueado por `.htaccess`, fora do git
  e fora do deploy).
- Painel financeiro: `/admin/` (usuários em `ADMIN_USERS_JSON`: `{"user":{"hash":bcrypt,"papel":"admin|leitura"}}`; sessão, CSRF, trava após 8 tentativas). Admin estorna; leitura só vê. Painel "transparente": sem nenhuma menção ao Asaas na tela. Saque desligado (`saque_habilitado`), porque o validador de saque da conta é o do pipo.guru: o saque é feito em pipo.guru/financeiro. Pós-pagamento: `/obrigado/?pedido=ID`.
- Segredos **só** nos secrets do GitHub: `ASAAS_ENV` (sandbox|production), `ASAAS_API_KEY`,
  `ASAAS_WEBHOOK_TOKEN`, `ADMIN_KEY`, `GRUPO_WHATSAPP`, `CUPONS_JSON`
  (ex.: `{"CODIGO":{"preco":997,"nome":"Comunidade X","limite":20}}`). O deploy gera
  `api/_config-secret.php`; o repo é público, nunca commitar esse arquivo.
- Teste local: `php -S localhost:8766` na raiz + `api/_config-secret.php` a partir do
  `.example` (dá pra apontar `asaas_url` pra uma API simulada).

## Convenções

- Commits em **pt-BR**, uma frase que conta a história da mudança.
- O copy é a maior parte do trabalho aqui. Mudança de texto é mudança de
  produto: preservar tom, escassez e numeração das seções.
- Toda promessa e todo FAQ têm que ser possíveis com **uma IA só, de
  preferência gratuita**. Assinatura paga é "seria ótimo", nunca obrigatória.
- Sem depoimento inventado. Fotos de mulheres são banco de imagem (Unsplash,
  licença livre) e nunca levam legenda de aluna.
- Acento da edição é uma cor só (`--sig`). Nada de rosa.

## Cuidados

- **Meta Pixel** (`1382602147129782`) e eventos `Contact` / `InitiateCheckout` /
  `ViewContent` estão no `head`. Não remover ao mexer em performance.
- O botão de compra aponta pro **checkout Asaas** (links em `CFG.checkout`).
- Fotos da edição em `images/mulheres/` (jpg otimizado). `images/og-mulheres.jpg`
  é print do hero em 1200×630. Logo é tipográfico: `autonom.ia/mulheres` (ponto entre autonom e ia, barra antes da edição; os dois no acento).
