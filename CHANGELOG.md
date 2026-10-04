# Changelog

Marcos do projeto. O histórico fino está no `git log` (30 commits desde
2026-07-14); aqui ficam as mudanças que importam para entender a página.

Formato: `YYYY-MM-DD — resumo`. Commits são em pt-BR.

---

## 2026-10-03 — Rodada de negócios vira uma mesa redonda com os crachás ligados a você

- No computador a cena antiga (cinco crachás pequenos soltos sobre uma faixa escura) parecia perdida e não dizia o que era a seção. Agora há uma mesa redonda com três participantes em volta (Moda, Psicologia, Confeitaria) e o crachá "Você" em laranja na frente. Um fio pontilhado liga você a cada uma, com a etiqueta do que ela pode virar: "pode virar cliente", "pode te indicar", "pode virar parceira", as três saídas que a chamada já prometia.
- Crachás maiores, com desenho de crachá de evento (marca, nome, área). A cena cresce com a janela e cabe na altura da tela; até 900 px entra uma composição compacta, com a etiqueta presa na base de cada crachá.
- Mudanças de texto: saiu o crachá "Consultoria · Carreira" (ficou uma etiqueta por participante) e o rótulo "Sua ambição" do crachá "Você" virou "O que você faz". Chamada, apoio e as três colunas de baixo não mudaram.
- Com "reduzir movimento" ligado a cena aparece completa e parada. Com movimento, fios e etiquetas entram um por um e os pontos correm de você para cada contato; passar o mouse numa participante acende o fio dela.
- Conferido em 320, 360, 390, 768, 901, 1024, 1366×650, 1512×900 e 1920×1080, com e sem movimento, sem erros de JavaScript. Duas rodadas de leitura com três personas do público.

## 2026-10-03 — Assinatura "Desenvolvido por: skope.cc" com contato no rodapé

- Rodapé ganha a assinatura ao lado do copyright, à esquerda, longe do botão do WhatsApp. Clicar abre um formulário curto no próprio lugar (nome, WhatsApp e mensagem), que fecha com Esc ou clique fora.
- O envio usa o `enviar.php` do guizanoni.com, que ganhou um caminho próprio para `produto=Assinatura skope.cc`: dispensa e-mail e entrega em sites@skope.cc. Nenhuma credencial neste repo.
- Também nesta rodada: abertura ocupa a janela inteira em monitores altos e o cartão do facilitador ganhou botão de seguir no Instagram.

## 2026-10-03 — Popup abre sozinho aos 3 segundos e "Role" vira convite

- O convite passa a abrir sozinho 3 s depois do acesso, além dos gatilhos de saída. Continua valendo uma exibição a cada sete dias por navegador e o silêncio para quem já começou a compra. `?popup=saida` segue mostrando sem contar exposição.
- A dica de rolagem da abertura troca "Role" por "Veja o que você leva" com seta.
- Etiqueta laranja do hover dos cards ampliada; segunda chamada reescrita ("A ideia é sua. O trabalho pesado fica com a IA.").

## 2026-10-03 — Popup de saída armado 3 segundos após o acesso

- A espera mínima antes de o convite de saída poder aparecer cai de 20 s (computador) e 25 s (celular) para 3 s, a pedido do Gui, para alcançar quem sai rápido. Gatilhos e limite de uma exibição a cada sete dias continuam iguais.

## 2026-10-03 — Transição da abertura roda mesmo com "reduzir movimento"

- No PC do Gui o Windows está com animações desligadas, e a página trocava a abertura inteira pela versão estática: sem moldura, sem os três cards. Parecia que o efeito tinha sumido.
- A transição da abertura deixa de depender dessa preferência em telas a partir de 900 px. Hovers, cursor piscando e demais transições continuam desligados para quem pede menos movimento.
- Conferido com a preferência emulada em 1449×900: abertura, meio da transição, cards, hover e volta ao topo.

## 2026-10-03 — Cena presa durante a transição e menu com indicador deslizante

- Corrigida a falha da prévia anterior: a transição disparava, mas a página rolava junto e o título dos três cards sumia atrás do menu. A cena volta a ficar presa (sticky) por 60% da altura da janela, o bastante para a animação de 1,1 s e um respiro para ver os cards. Em janela baixa, prende depois de ler a abertura inteira.
- Menu de desktop ganha um indicador único que desliza até o link sob o mouse e volta para a seção em leitura. Troca entre cabeçalho transparente e branco agora é suave. Sem JavaScript, valem os estilos anteriores.
- Mantidos menu centralizado, moldura e tela "Página no ar" sobre a foto na abertura, hovers dos cards, textos e mobile.
- Conferidos 1366×650, 1440×820 e 1920×950 (abertura, meio da transição, cards, hover, volta ao topo, seção ativa) e 390 px. Sem erros de JavaScript. Publicação pendente.

## 2026-10-03 — Transição de desktop revisada pela referência Revolut

- Observada a referência pública no navegador, incluindo quadros intermediários e retorno ao topo. O primeiro scroll dispara uma transição de 1,1 s; não exige percorrer 160 alturas percentuais extras de rolagem.
- Foto e moldura recuam juntas até o card central, com escala uniforme e rosto preservado. Cards laterais entram no mesmo movimento; a segunda chamada e as legendas aparecem quando há área livre, sem atravessar a foto.
- Menu centralizado na janela, com logo e botão nas laterais. Mantidos formato do cabeçalho, textos, identidade e hovers. Cards também acessíveis pelo teclado.
- Enquadramento inicial considera largura e altura. Abertura limitada a 1120 px em monitores muito altos; em janelas baixas, cresce o necessário para ler a oferta antes da transição. Mobile e movimento reduzido mantêm sua composição estática.
- A tentativa local anterior, baseada apenas no recorte/zoom do histórico `221b01c`, foi substituída após a indicação explícita da referência. Conferência visual passa a incluir a animação real, além das medidas finais.
- Conferidos 15 tamanhos de desktop (900–2560 px), retorno ao topo, reversão durante a transição, hover sem sobreposição, menu centralizado, teclado e movimento reduzido. Regressão mobile em 320, 390 e 768 px; 15 cenários de preço passaram. Prévia local pronta, publicação pendente.

## 2026-10-03 — Desconto de 8% para cada participante na dupla

- Dupla no cartão passa de 5% para 8% por vaga: R$ 1.193,24 por participante e R$ 2.386,48 pelas duas. Mantida uma única compra e cobrança, com os dados das duas participantes.
- PIX continua com 10%: R$ 1.167,30 por participante e R$ 2.334,60 pela dupla. Sempre vale o maior desconto, sem acumular; regras próprias de cupons preservadas.
- Atualizados cálculo no servidor, estimativa local, seletor do checkout, oferta, FAQ, perfil editorial, legendas e as duas artes do post da dupla com suas prévias.
- Passaram 15 cenários de preço e oito combinações no checkout, cobrindo API e estimativa local, compra individual/dupla e PIX/cartão. Nenhuma cobrança enviada.

## 2026-10-03 — Troca de olhar no retrato do facilitador

- Mantida a foto atual de Gui como padrão. Ao passar o mouse, aparece a foto do mesmo ensaio olhando para a frente; ao sair, volta à original, com transição suave e o zoom existente.
- Efeito também disponível ao focar o link do Instagram pelo teclado. Em telas de toque, a foto permanece estática; a preferência por movimento reduzido é respeitada.
- Conferidos entrada/saída do mouse, foco, celular e dimensões do retrato. Imagem original preservada sob a segunda para evitar flashes durante o carregamento.

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
