# AGENTS.md

Instruções para agentes de código neste repositório (padrão [agents.md](https://agents.md)).
Lido por Cursor, Copilot, Claude Code e outras ferramentas.

O conteúdo canônico vive em **[CLAUDE.md](CLAUDE.md)** — este arquivo repete o
essencial para ferramentas que não leem aquele nome.

---

## Projeto

Landing page do **Autonomia**: imersão presencial de 3 dias em Curitiba sobre
construir os próprios sistemas de automação. Página de venda única, checkout
externo (Cakto).

## Stack e execução

- **HTML estático puro** — sem framework, sem build, sem backend.
- Rodar: abrir `index.html`, ou `python3 -m http.server 8000`.
- Não há testes, linter ou pipeline de build.

## Deploy

`git push` na `main` → GitHub Actions → **FTPS** pro HostGator (`/public_html/`).
Nunca rodar dois deploys em paralelo: corrompe o state file do FTP (o workflow
já tem um `concurrency group` para isso).

## Estrutura

- `index.html` — a LP no ar (v11)
- `v2/index.html` — variante dark glassmorphism
- `v3/index.html` — variante creme/navy
- `images/` — fotos `.webp` otimizadas · `brand/` — logos oficiais

As três páginas são **independentes**: não compartilham CSS nem JS. Confirmar o
alvo antes de editar.

## Convenções

- Commits e comentários em **pt-BR**.
- Seções numeradas (01…10): ao inserir ou remover uma, renumerar as seguintes.
- Copy é produto — preservar tom e estrutura de escassez ao editar texto.

## Não quebrar

- Meta Pixel e eventos de conversão no `head`.
- Meta-tag de verificação de domínio da Meta.
- Player de vídeo customizado (`controls=0` + botões próprios).
- Link do checkout Cakto.

## Contexto adicional

[MEMORY.md](MEMORY.md) tem as decisões de copy já tomadas (e revertidas) —
consultar antes de reintroduzir números de vaga, "Turma 4" ou Demo Day.
