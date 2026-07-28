# Changelog

Marcos do projeto. O histórico fino está no `git log` (30 commits desde
2026-07-14); aqui ficam as mudanças que importam para entender a página.

Formato: `YYYY-MM-DD — resumo`. Commits são em pt-BR.

---

## 2026-07-27

- Adiciona `.gitignore` (`.DS_Store`) — o repo rodou 13 dias sem um.

## 2026-07-23 — As três versões

- **Nascem `/v2` e `/v3`**: a copy v12 em dois designs independentes. A v2 é
  dark glassmorphism (Oswald/Inter, timeline, carrossel); a v3 é creme/navy
  (Syne/Public Sans, cards pastel, estrutura da v1).
- **v3 itera o hero duas vezes**: primeiro vira minimalista (headline curta em
  2 frases, prova social sai de cima da dobra e vira faixa fina), depois ganha
  headline de transformação — *"Em 3 dias, do improviso ao controle total do
  seu negócio"*.
- Galeria de palco vira carrossel com setas, +10 fotos vindas do guizanoni.com.
- **Data do evento muda para 18–20 de setembro de 2026.**

## 2026-07-14 — Fundação e rodada de conversão

Todo o resto do projeto aconteceu neste dia — 27 commits.

- **LP Autonomia v11** publicada: página estática com brand e fotos, caminhos
  relativos. Workflow de deploy FTPS pro HostGator no mesmo padrão dos outros
  sites do ecossistema.
- **Vídeo do YouTube** (`M_qOnI1UnjE`) substitui o placeholder, com `controls=0`
  e botões próprios de play/pause e volume no estilo da LP.
- **Meta Pixel `1382602147129782`** ativado, com eventos `Contact`,
  `InitiateCheckout` e `ViewContent`, fallback `noscript` e meta-tag de
  verificação de domínio.
- **Nova seção 04 "Possibilidades"** — grid de sistemas construíveis por área;
  seções seguintes renumeradas até 10.
- **Nova seção 09 "O local"** — Slaviero Hotel Batel + mapa; contador
  "faltam X dias" no header, no card de preço e no fechamento.
- **Escassez reescrita**: "30 vagas" sai de toda a página e vira "vagas
  limitadas"; "sala de 30" vira "uma sala de empreendedores"; "Turma 4" sai do
  card de preço.
- **Agenda reorganizada**: Diagnóstico + Estratégia / Construção + Validação /
  Melhorias e Roadmap. O Demo Day sai e o domingo à tarde vira criação do
  roadmap de 90 dias.
- Botão de compra passa a apontar pro **checkout Cakto**.
- Otimização mobile (≤640px): sem quebras fixas nos títulos, paddings
  proporcionais, CTAs em largura cheia, barra fixa compacta.

---

**Como manter:** nova entrada a cada marco — mudança estrutural de copy, nova
seção, troca de checkout, mudança de data do evento, ajuste de tracking. O dia
a dia fica no `git log`.
