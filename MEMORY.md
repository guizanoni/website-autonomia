# MEMORY.md

Contexto que o código e o git não registram: decisões, motivos e armadilhas.
Complementa [CLAUDE.md](CLAUDE.md) (como trabalhar) e [CHANGELOG.md](CHANGELOG.md) (o que mudou).

---

## Edição atual — atualizado em 04/10/2026

- A raiz redireciona para `/mulheres/`: AUTONOM/IA Mulheres, um dia presencial em 11/11/2026, das 8h às 18h30, no Hard Rock Café Curitiba. Checkout Asaas; regras comerciais em `AGENTS.md` e `api/lib.php`.
- A copy prioriza mulheres que já empreendem; quem quer construir uma primeira oferta é o público secundário. Começar do zero em IA é uma condição transversal.
- Promessas específicas: planejamento de conteúdo e legendas, oferta e preço inicial para testar, página simples publicada e IA com contexto para ajudar a escrever. Não anunciar artes/vídeos finalizados, vendas garantidas ou atendimento automático no WhatsApp como entregas do dia.
- Posts de conversão: coleção de 12 peças em `social/posts-conversao/`, alinhada ao conceito atual “A ideia é sua. O trabalho pesado fica com a IA.”. PIX vem primeiro nas peças de oferta; orçamento de profissionais é sempre ilustrativo. Sem mapa de assentos.
- Perfil editorial e referências em `social/brand-profile.md`. Os dados abaixo documentam a edição anterior e não devem orientar a comunicação da edição Mulheres.

## Produto anterior — histórico de julho de 2026

- **Autonomia** (autonomia.vc) — imersão presencial de 3 dias em Curitiba.
  Empreendedores saem com sistemas de automação construídos por eles mesmos.
- **Data atual do evento:** 18–20 de setembro de 2026 (mudou em 2026-07-23; era
  outra data antes — conferir a página antes de citar).
- **Local:** Slaviero Hotel Batel, Curitiba.
- **Checkout:** Cakto (externo). O site não processa pagamento.
- **Conduz:** guizanoni (gui@skope.cc) · timezone America/Sao_Paulo (UTC-3).

## Decisões de copy que já foram revertidas ou ajustadas

- **"30 vagas" foi removido de toda a página** (2026-07-14). A escassez agora é
  "vagas limitadas", e "sala de 30" virou "uma sala de empreendedores". Não
  reintroduzir números de vaga sem confirmar.
- **"Turma 4" saiu do card de preço** — fica só "Curitiba · 2026".
- **Demo Day saiu da agenda.** O domingo à tarde virou "criação do roadmap de
  90 dias". A agenda dos 3 dias é: Diagnóstico + Estratégia / Construção +
  Validação / Melhorias e Roadmap.

## As três versões da página

Existem `/`, `/v2` e `/v3` como HTMLs independentes — não compartilham CSS nem
JS. Foram criadas em 2026-07-23 para testar copy v12 em dois designs diferentes:

- **v2** — dark glassmorphism, Oswald/Inter, timeline, carrossel de palco
  (referência visual "megustamuito")
- **v3** — creme/navy, Syne/Public Sans, cards pastel (estrutura da v1)

**A v3 recebeu duas rodadas de iteração no hero** (headline curta em 2 frases,
prova social virou faixa fina abaixo da dobra). Isso sugere que ela era a
candidata a substituir a v1 — confirmar antes de assumir que a raiz é a versão
final desejada.

## Armadilhas conhecidas

- **Deploy FTP concorrente corrompe o state file.** O workflow tem
  `concurrency: deploy-website-autonomia` com `cancel-in-progress: false` por
  isso — lição aprendida nos repos do site principal e do antihype. Não remover.
- **Meta Pixel é fácil de matar sem querer** ao otimizar o `head`. Há uma
  meta-tag de verificação de domínio da Meta lá também.
- O player de vídeo do YouTube (`M_qOnI1UnjE`) usa `controls=0` com botões
  próprios de play/pause e volume no estilo da LP. Mexer no player quebra o
  visual.

## Vizinhança

Fotos de palco vieram do [[guizanoni-com]]. A marca e o posicionamento
conversam com o ecossistema Skope — ver `../skope.cc`.
