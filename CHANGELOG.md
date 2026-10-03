# Changelog

Marcos do projeto. O histórico fino está no `git log` (30 commits desde
2026-07-14); aqui ficam as mudanças que importam para entender a página.

Formato: `YYYY-MM-DD — resumo`. Commits são em pt-BR.

---

## 2026-10-02 — Menu com hierarquia visual

- Navegação agrupada em uma faixa arredondada, com estados de hover e seção ativa; inscrição em laranja para destacar a ação principal. Mantidos os quatro rótulos solicitados.
- Menu expansível em celulares e tablets, com fechamento por seleção, clique externo ou Escape. Marca em duas linhas nas telas menores para acomodar o botão de inscrição.
- Conferidos encaixe e navegação em larguras de 320 a 1440 pixels, sem erros de JavaScript.

## 2026-10-02 — Abertura com foco no benefício

- A chamada principal passa a ser “Tire ideias do papel. E tarefas da sua frente.”; conteúdo, oferta e página permanecem como entregas explicadas no parágrafo de apoio.
- Gui passa a ser apresentado como facilitador, conforme preferência do usuário. Título de compartilhamento acompanha a nova abertura.

## 2026-10-02 — Copy da edição Mulheres orientada aos ICPs

- Abertura passa a apresentar conteúdo, oferta e página como entregas, com preço total, parcelamento e garantia perto da chamada de compra.
- Duas entradas de identificação: quem já empreende e quem quer construir uma primeira oferta. Iniciantes têm orientação explicada antes das entregas; familiaridade com IA deixa de ser tratada como um terceiro ICP.
- Entregas ficam visíveis em cards legíveis, sem exigir hover; exemplo de organização residencial mostra a sequência oferta → conteúdo → página e está identificado como ilustrativo.
- FAQ distingue planejamento e legendas de artes prontas, assistência na escrita de atendimento automático e preço inicial de oferta validada. Mantidos valores, regras comerciais, checkout e tracking.
- No celular, em telas baixas e com movimento reduzido, a abertura segue o fluxo normal de leitura. Em telas maiores, a animação apresenta as entregas.
- Verificados seis tamanhos/modos de tela, links internos, abas, checkout individual/dupla e turma esgotada com APIs simuladas, sem criar pedidos.

## 2026-10-02 — Mais três posts de conversão

- A vitrine `/social/posts-conversao/` passa de três para seis posts, com novos argumentos sobre começar do zero, comprar em dupla e reservar tempo para o próprio negócio.
- Cada novo post tem legenda para Instagram e LinkedIn, arte nos formatos 1080×1350 e 1080×1080 e molde editável em HTML. Os três posts anteriores foram preservados.
- Perfil editorial da edição registrado em `social/brand-profile.md`, com condições conferidas na página publicada e no código do checkout.

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
