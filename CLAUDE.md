# CLAUDE.md

Instruções do projeto para Claude Code e assistentes. Manter sucinto; nada de segredo ou token aqui.

Ver também: [MEMORY.md](MEMORY.md) (contexto e decisões), [CHANGELOG.md](CHANGELOG.md) (histórico), [AGENTS.md](AGENTS.md) (mesmo conteúdo, padrão multi-ferramenta).

---

## O que é

Landing page do **Autonomia** — imersão presencial de 3 dias em Curitiba onde
empreendedores constroem os próprios sistemas de automação. Página única de
venda, com checkout externo.

## Stack

**HTML estático puro.** Sem framework, sem build, sem backend. Um `index.html`
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

- `/` (`index.html`) — LP v11, a versão **no ar**
- `/v2` — variante dark glassmorphism (Oswald/Inter, timeline, carrossel)
- `/v3` — variante creme/navy (Syne/Public Sans, cards pastel)

As v2 e v3 são experimentos de copy/design mantidos lado a lado. Ao editar,
confirmar qual versão é o alvo — elas **não** compartilham código.

## Convenções

- Commits em **pt-BR**, uma frase que conta a história da mudança.
- O copy é a maior parte do trabalho aqui. Mudança de texto é mudança de
  produto: preservar tom, escassez e numeração das seções.
- As seções são numeradas (01…10). Ao inserir ou remover uma, **renumerar as
  seguintes** — já houve commits só para isso.

## Cuidados

- **Meta Pixel** (`1382602147129782`) e eventos `Contact` / `InitiateCheckout` /
  `ViewContent` estão no `head`. Não remover ao mexer em performance.
- O botão de compra aponta pro **checkout Cakto** (externo).
- Imagens em `images/` são `.webp` otimizadas; `brand/` tem os logos oficiais.
