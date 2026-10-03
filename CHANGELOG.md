# Changelog

Marcos do projeto. O histórico fino está no `git log` (30 commits desde
2026-07-14); aqui ficam as mudanças que importam para entender a página.

Formato: `YYYY-MM-DD — resumo`. Commits são em pt-BR.

---

## 2026-10-03 — Responsividade preservando o design original

- Restaurados foto em tela cheia, transição para três cards, molduras, notificações e hovers. Removida a composição em duas colunas rejeitada pelo usuário; mantidos conteúdo e estrutura originais.
- Retirado o limite fixo de 1200 px dos contêineres. Margens, tipografia e recorte da foto acompanham o navegador; texto fica à esquerda do rosto.
- Janelas baixas continuam com a animação. Quando necessário, a abertura rola até mostrar todo o conteúdo antes de iniciar o efeito. Os cards e suas legendas se adaptam ao espaço disponível.
- Mobile com foto de ponta a ponta e fade para o texto abaixo do rosto. Cabeçalho ganha fundo durante a leitura; orientação horizontal também considerada.
- Conferidas 19 combinações de largura e altura, de 320 a 2560 px, transição para os cards, hover, redimensionamento, movimento reduzido, menu e abertura/fechamento da inscrição. Os 14 cenários de preço passaram. Nenhuma cobrança criada.

## 2026-10-03 — Abertura responsiva com texto e foto separados (substituída)

Esta abordagem foi rejeitada pelo usuário e substituída pela restauração do conceito original descrita acima.

- Abertura em duas colunas no computador e sequência título, foto e informações no celular. Tipografia, margens, recorte da foto e botões se adaptam à largura disponível.
- Removida a abertura presa ao scroll e à altura da janela: a seção agora cresce com o conteúdo, sem texto sobre o rosto ou espaço vazio em monitores altos. As três entregas continuam em uma faixa estática.
- Ajustados agenda e barra de compra para telas estreitas. Mantidos textos da oferta, preços, checkout e eventos Meta.
- Conferidos 17 formatos de 320 a 2560 px, orientação horizontal, redimensionamento, menu, abertura/fechamento da inscrição, cabeçalho e movimento reduzido. Sem sobreposição entre texto e foto, cortes no título ou erros de JavaScript. Os 14 cenários de preço passaram; nenhum formulário enviado ou cobrança criada.

## 2026-10-03 — Vitrine com dez posts e mais mulheres nas artes

- Revisados os seis posts anteriores e criados quatro novos sobre rotina de quem empreende, primeira oferta, apoio para começar e conexões entre mulheres. Seis artes agora usam fotografias ilustrativas da própria landing page.
- Refeitas vinte artes nos formatos Instagram 1080×1350 e LinkedIn 1080×1080, com vinte legendas específicas. Corrigidas promessas de posts finalizados; temas, legendas, oferta inicial e página publicada seguem o escopo do encontro.
- Mantidas as condições vigentes de PIX com 10%, dupla com 5% no cartão e garantia até as 12h30. Capacidade de 50 participantes apresentada como tamanho da turma.
- Vitrine em grade com novos posts primeiro, filtros, prévias WebP leves, acesso à arte ampliada, cópia de legenda e download do PNG. Tratamento de falha de cópia e carregamento, descrições das imagens e controles acessíveis.
- Conferidos os vinte PNGs e suas legendas, os dois formatos de rede, filtros, cópia, download, links diretos e telas de 320 a 1440 px. Sem erros de JavaScript ou conteúdo cortado nas artes.

## 2026-10-03 — Convite de saída com apoio para começar

- Popup com a mensagem “Comece com ajuda. Use no seu negócio.”, preço atual, monitoras, IA gratuita e garantia até o almoço. O botão “Quero aproveitar agora” abre a inscrição individual.
- No computador, dispara ao sair pelo topo após 20 segundos; no celular, ao voltar perto do topo depois de ler ao menos uma tela e meia e permanecer 25 segundos. Limite de uma exibição a cada sete dias por navegador.
- Diálogo com foco contido, Escape, fechamento por botão/fundo e restauração de foco. Não disputa atenção com formulários, checkout ou lista de espera, nem aparece com inscrições encerradas/esgotadas.
- Eventos Meta separados para exibição, fechamento e ida ao checkout. `?popup=saida` permite revisão imediata sem gravar exposição ou emitir esses eventos.
- Conferidos cinco formatos de tela, movimento reduzido, gatilhos, repetição, indisponibilidade de storage, transição para checkout e regras de preço. Nenhuma cobrança criada nos testes.

## 2026-10-03 — PIX com 10% de desconto

- PIX individual passa a R$ 1.167,30, com economia de R$ 129,70. Atualizados abertura, oferta, FAQ, fechamento, barra do celular e checkout.
- No servidor e na estimativa local, a dupla aplica o maior desconto: 10% no PIX (R$ 2.334,60 no total) ou 5% no cartão (R$ 2.464,30). Mantidas as regras específicas dos cupons.
- Checkout envia ao Asaas o total calculado no servidor. Conferidos os quatro cenários com uma API local simulada, sem criar cobranças reais; 14 cenários de preço e cupons passam a ser verificados antes do deploy.
- Sincronizados perfil editorial, legendas e oito artes dos posts de conversão que exibiam preços. Conferidos o checkout em desktop/celular e o cálculo de contingência quando a API não responde.

## 2026-10-03 — Seção de aprendizado integrada ao visual da página

- As seis entregas passam a formar três etapas do encontro, em um painel claro com divisórias e laranja restrito aos detalhes. Removidos os cartões com gradientes e os estilos antigos de bandeja/hover.
- A aba do kit organiza quatro materiais e destaca o acesso sem prazo. Conteúdo preservado, com leitura em colunas no desktop e fluxo vertical no celular.
- Abas com foco visível e navegação por setas, Home e End. Conferidos os dois painéis, a agenda e o encaixe entre 320 e 1440 pixels, incluindo os limites do layout de tablet.

## 2026-10-02 — Chamada principal com IA explícita

- Nova abertura: “Use a IA para tirar ideias do papel e automatizar tarefas do seu negócio.” Título de compartilhamento atualizado e tipografia ajustada para desktop e celular.

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
