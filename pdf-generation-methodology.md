# Metodologia de Geração de PDFs — Rise Gestão Financeira
> Documento de referência para replicar a abordagem em outros projetos com bugs de PDF

---

## 1. Contexto: O Que os PDFs Mostram

O projeto Rise gera dois tipos de relatório em PDF:

| Tipo | Nome | Conteúdo |
|------|------|----------|
| Resumo Executivo | `riseresumoexecutivo_*.pdf` | Cards de totais (Receitas, Despesas, Investimentos, Saldo Líquido) |
| Extrato de Movimentações | `riseextrato_*.pdf` | Gráfico de linha (Saldo Acumulado) + tabela de transações |

### Estrutura Visual Identificada nos PDFs

```
┌─────────────────────────────────────────┐
│                                         │
│   [ESPAÇO EM BRANCO — ~55% da página]  │
│   (cover area / posicionamento fixo)    │
│                                         │
│   Rise                    [logo]        │
│   Gestão Financeira                     │
│                                         │
│   Resumo Executivo  (título em teal)    │
│   Maio de 2026                          │
│   João Paulo Pires de Oliveira          │
│   Gerado em 29/05/2026, 01:41          │
│   ─────────────────────────────────── ← divider
│                                         │
│   [CARDS / GRÁFICO / TABELA]           │
│                                         │
└─────────────────────────────────────────┘
```

---

## 2. Tecnologia de Geração (Inferida)

A análise dos PDFs aponta para **Puppeteer (headless Chrome)** como engine de geração, com HTML/CSS como template. Evidências:

- ✅ Gráficos de linha renderizados corretamente (requer execução de JavaScript)
- ✅ Cores CSS aplicadas (#00b894 teal, verde para receitas, vermelho para despesas)
- ✅ Layout flexbox funcionando (grid de 2 colunas nos cards)
- ✅ Fontes carregadas (sans-serif limpa, provavelmente Inter ou Roboto)
- ✅ Página A4 com dimensões corretas

### Stack Provável

```
Backend (Node.js)
  └── Puppeteer
        └── Headless Chrome
              └── Renderiza rota HTML específica
                    └── Chart.js ou Recharts (gráficos)
                    └── CSS com variáveis de cor
                    └── Salva como PDF
```

---

## 3. Evolução Entre Versões (Diagnóstico dos Bugs)

Comparando os PDFs por horário de geração:

### 01:31 — Versão com Bugs Parciais
- **Extrato**: gráfico renderiza ✅, tabela mostra apenas 1 linha com texto cortado ❌
- **Problema**: dados assíncronos não carregados no momento da captura

### 01:34 — Versão Intermediária
- **Resumo Executivo**: cards superiores aparecem, inferiores aparecem cortados/sem valor ❌
- **Extrato**: gráfico renderiza ✅, tabela sem dados ❌
- **Problema**: timing da captura melhorou mas ainda insuficiente

### 01:41 — Versão Correta ✅
- **Resumo Executivo**: todos os 4 cards completos com valores
- **Extrato**: gráfico completo + início da tabela com dados visíveis
- **Correção aplicada**: `waitForSelector` ou `waitUntil: 'networkidle0'` adicionado

---

## 4. Arquitetura do Template HTML

### 4.1 Estrutura da Página (A4)

```html
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <style>
    /* CRÍTICO: definir tamanho exato A4 */
    @page {
      size: A4;
      margin: 0;
    }
    
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact; /* CRÍTICO para cores */
      print-color-adjust: exact;
    }

    body {
      width: 210mm;
      min-height: 297mm;
      margin: 0;
      padding: 0;
      font-family: 'Inter', 'Roboto', Arial, sans-serif;
      font-size: 14px;
      color: #333;
      background: white;
    }

    .page {
      width: 210mm;
      min-height: 297mm;
      padding: 40px 48px;
      position: relative;
    }
  </style>
</head>
<body>
  <div class="page">
    <!-- HEADER SECTION -->
    <div class="header">
      <div class="brand">
        <span class="brand-name">Rise</span>
        <span class="brand-sub">Gestão Financeira</span>
      </div>
      <h1 class="report-title">Resumo Executivo</h1>
      <p class="report-period">Maio de 2026</p>
      <p class="report-user">João Paulo Pires de Oliveira</p>
      <p class="report-date">Gerado em 29/05/2026, 01:41</p>
    </div>

    <hr class="divider">

    <!-- CONTENT SECTION -->
    <div class="content">
      <!-- Cards, gráficos ou tabelas aqui -->
    </div>
  </div>
</body>
</html>
```

### 4.2 CSS dos Componentes

```css
/* ── HEADER ───────────────────────────────── */
.header {
  padding-bottom: 24px;
}

.brand-name {
  font-size: 22px;
  font-weight: 700;
  color: #00b894; /* teal do Rise */
  display: block;
}

.brand-sub {
  font-size: 13px;
  color: #636e72;
}

.report-title {
  font-size: 20px;
  font-weight: 600;
  color: #b2bec3; /* cinza claro — efeito "watermark" */
  margin: 16px 0 8px;
}

.report-period,
.report-user {
  font-size: 13px;
  color: #2d3436;
  margin: 2px 0;
}

.report-date {
  font-size: 12px;
  color: #636e72;
  margin-top: 4px;
}

.divider {
  border: none;
  border-top: 1.5px solid #dfe6e9;
  margin: 0 0 24px;
}

/* ── CARDS DE TOTAIS ──────────────────────── */
.cards-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 24px;
}

.card {
  border: 1px solid #dfe6e9;
  border-radius: 8px;
  padding: 16px 20px;
}

.card-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #636e72;
  margin-bottom: 8px;
}

.card-value {
  font-size: 26px;
  font-weight: 700;
  color: #00b894; /* verde para receitas */
}

.card-value.despesa {
  color: #d63031; /* vermelho para despesas */
}

/* ── TABELA DO EXTRATO ────────────────────── */
.table-header {
  display: grid;
  grid-template-columns: 90px 1fr 140px 100px 110px;
  padding: 8px 0;
  border-bottom: 1.5px solid #dfe6e9;
  margin-top: 24px;
}

.table-header span {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #636e72;
}

.table-row {
  display: grid;
  grid-template-columns: 90px 1fr 140px 100px 110px;
  padding: 10px 0;
  border-bottom: 1px solid #f5f6fa;
  font-size: 13px;
  align-items: center;
}

.value-positive { color: #00b894; font-weight: 600; }
.value-negative { color: #d63031; font-weight: 600; }
.saldo-value    { font-weight: 700; color: #2d3436; }
```

---

## 5. Geração com Puppeteer (Node.js)

### 5.1 Código Base — Padrão Correto

```javascript
const puppeteer = require('puppeteer');

async function generatePDF(htmlContent, outputPath) {
  const browser = await puppeteer.launch({
    headless: 'new',
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-dev-shm-usage',
      '--font-render-hinting=none', // CRÍTICO: evita corte de letras
    ]
  });

  const page = await browser.newPage();

  // CRÍTICO: viewport em pixels equivalentes ao A4
  await page.setViewport({
    width: 794,   // 210mm em 96dpi
    height: 1123, // 297mm em 96dpi
    deviceScaleFactor: 2, // resolução dobrada — evita texto borrado
  });

  await page.setContent(htmlContent, {
    waitUntil: 'networkidle0', // CRÍTICO: espera todos os recursos
  });

  // CRÍTICO: esperar que gráficos terminem de renderizar
  await page.waitForSelector('.chart-container canvas', {
    visible: true,
    timeout: 10000,
  });

  // OPCIONAL: espera extra para animações de gráfico terminarem
  await new Promise(resolve => setTimeout(resolve, 500));

  const pdf = await page.pdf({
    path: outputPath,
    format: 'A4',
    printBackground: true, // CRÍTICO: preserva cores de fundo
    preferCSSPageSize: true,
    margin: {
      top: '0',
      right: '0',
      bottom: '0',
      left: '0',
    },
  });

  await browser.close();
  return pdf;
}
```

### 5.2 Versão com URL (em vez de HTML string)

```javascript
async function generatePDFFromURL(url, outputPath) {
  const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
  const page = await browser.newPage();

  await page.goto(url, {
    waitUntil: 'networkidle0', // espera rede estabilizar
    timeout: 30000,
  });

  // Esperar elemento específico que indica que os dados carregaram
  await page.waitForSelector('[data-loaded="true"]', { timeout: 15000 });

  // Esconder elementos da UI que não devem aparecer no PDF
  await page.evaluate(() => {
    document.querySelectorAll('.no-print, nav, .sidebar').forEach(el => {
      el.style.display = 'none';
    });
  });

  const pdf = await page.pdf({
    format: 'A4',
    printBackground: true,
  });

  await browser.close();
  return pdf;
}
```

---

## 6. Renderização de Gráficos no PDF

### 6.1 Chart.js (recomendado para server-side)

```javascript
// No HTML do template, ANTES de fechar </body>:
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  // CRÍTICO: desativar animações para PDF
  Chart.defaults.animation = false;
  Chart.defaults.animations = false;
  Chart.defaults.transitions = {};

  const ctx = document.getElementById('balanceChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: window.__CHART_DATA__.labels,
      datasets: [{
        label: 'Saldo',
        data: window.__CHART_DATA__.values,
        borderColor: '#0984e3',
        backgroundColor: 'rgba(9, 132, 227, 0.1)',
        borderWidth: 2,
        pointBackgroundColor: '#0984e3',
        pointRadius: 4,
        tension: 0.3,
        fill: false,
      }]
    },
    options: {
      responsive: false, // CRÍTICO para PDF
      animation: false,  // CRÍTICO para PDF
      plugins: {
        legend: {
          position: 'top',
          labels: { usePointStyle: true, boxWidth: 12 }
        }
      },
      scales: {
        y: {
          ticks: {
            callback: (v) => `R$ ${v.toLocaleString('pt-BR')}`,
          }
        },
        x: {
          ticks: { maxRotation: 45 }
        }
      }
    }
  });

  // CRÍTICO: sinalizar ao Puppeteer que o gráfico terminou
  document.body.setAttribute('data-chart-ready', 'true');
</script>
```

```javascript
// No Puppeteer, esperar esse sinal:
await page.waitForSelector('body[data-chart-ready="true"]', {
  timeout: 10000
});
```

### 6.2 Tamanho fixo do canvas (evita redimensionamento)

```html
<!-- CRÍTICO: canvas com tamanho fixo em pixels -->
<canvas id="balanceChart" width="680" height="240"></canvas>
```

---

## 7. Bugs Comuns e Correções

### BUG 1: PDF totalmente em branco

**Causa:** Puppeteer captura antes do conteúdo carregar.

```javascript
// ❌ ERRADO
await page.setContent(html);
const pdf = await page.pdf(); // captura imediata

// ✅ CORRETO
await page.setContent(html, { waitUntil: 'networkidle0' });
await page.waitForSelector('.content-loaded');
const pdf = await page.pdf();
```

**Causa alternativa:** CSS `display: none` ou `visibility: hidden` no container principal.

```css
/* ❌ ERRADO — Puppeteer pode não ver o conteúdo */
.report-container { display: none; }

/* ✅ CORRETO — tornar visível antes de gerar */
.report-container { display: block; }
```

---

### BUG 2: Letras cortadas

**Causa A: Overflow hidden sem espaço suficiente**
```css
/* ❌ ERRADO */
.card { overflow: hidden; height: 80px; }

/* ✅ CORRETO */
.card { overflow: visible; min-height: 80px; }
```

**Causa B: Font rendering no headless Chrome**
```javascript
// ✅ Adicionar ao Puppeteer launch args:
args: ['--font-render-hinting=none', '--disable-font-subpixel-positioning']
```

**Causa C: Margem de página muito pequena**
```css
/* ✅ Sempre definir margem explícita */
@page { margin: 20mm 16mm; }
```

**Causa D: Linha da tabela cortada entre páginas**
```css
/* ✅ Evitar quebra dentro de elementos */
.table-row {
  page-break-inside: avoid;
  break-inside: avoid;
}
```

---

### BUG 3: PDF sem formatação (sem cores, sem bordas)

**Causa A: `printBackground` não habilitado**
```javascript
// ❌ ERRADO
await page.pdf({ format: 'A4' });

// ✅ CORRETO
await page.pdf({ format: 'A4', printBackground: true });
```

**Causa B: CSS `-webkit-print-color-adjust` ausente**
```css
/* ✅ Adicionar no início do CSS */
* {
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
  color-adjust: exact !important;
}
```

**Causa C: Estilos em arquivo externo não carregados**
```javascript
// ✅ Esperar CSS carregar
await page.waitForNetworkIdle({ idleTime: 500 });
// OU: usar CSS inline no template
```

**Causa D: Fontes Google/externas não carregadas**
```html
<!-- ✅ Pré-conectar e forçar carregamento -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
```

---

### BUG 4: Gráfico não aparece no PDF

```javascript
// ✅ Sequência correta para gráficos com Chart.js:

// 1. Desativar animações NO HTML antes do Chart.js
Chart.defaults.animation = false;

// 2. Após criar o chart, marcar como pronto
document.querySelector('#chart').setAttribute('data-ready', 'true');

// 3. No Puppeteer, aguardar esse marcador
await page.waitForSelector('#chart[data-ready="true"]', { timeout: 10000 });

// 4. Delay adicional de segurança (opcional mas recomendado)
await new Promise(r => setTimeout(r, 300));
```

---

## 8. Padrão de Passagem de Dados ao Template

```javascript
// Injetar dados antes de gerar o PDF (evita fetch assíncrono)
await page.evaluate((data) => {
  window.__REPORT_DATA__ = data;
}, {
  receitas: 3000.00,
  despesas: 1520.50,
  investimentos: 0,
  saldoLiquido: 1479.50,
  periodo: 'Maio de 2026',
  usuario: 'João Paulo Pires de Oliveira',
  geradoEm: '29/05/2026, 01:41',
  chartLabels: ['27/05/2026', '27/05/2026', '28/05/2026', '28/05/2026'],
  chartValues: [3000, 1950, 1850, 1600],
  transacoes: [/* array de transações */],
});

// No HTML, usar window.__REPORT_DATA__ em vez de fetch:
const data = window.__REPORT_DATA__;
document.getElementById('receitas').textContent = formatBRL(data.receitas);
```

---

## 9. Checklist de Diagnóstico Rápido

Use este checklist quando o PDF vier em branco, cortado ou sem formatação:

- [ ] `printBackground: true` no `page.pdf()`
- [ ] `-webkit-print-color-adjust: exact` no CSS
- [ ] `waitUntil: 'networkidle0'` no `setContent` ou `goto`
- [ ] `waitForSelector` esperando elemento de dados carregados
- [ ] `Chart.defaults.animation = false` antes de criar gráficos
- [ ] Canvas do gráfico com `width` e `height` fixos em px
- [ ] `@page { size: A4; margin: 0; }` no CSS
- [ ] Viewport configurado: `width: 794, height: 1123`
- [ ] `page-break-inside: avoid` nas linhas de tabela
- [ ] Fontes externas com tempo para carregar (ou fontes inline/system)
- [ ] `overflow: visible` em containers de conteúdo dinâmico
- [ ] `--no-sandbox --disable-setuid-sandbox` nos args do Puppeteer (ambiente servidor)
- [ ] Dados passados via `page.evaluate()` em vez de fetch assíncrono no template

---

## 10. Estrutura de Arquivos Recomendada

```
src/
├── pdf/
│   ├── PdfGenerator.js         # classe principal com Puppeteer
│   ├── templates/
│   │   ├── base.html           # layout base com CSS inline
│   │   ├── resumo-executivo.html
│   │   └── extrato.html
│   └── helpers/
│       ├── formatters.js       # formatBRL(), formatDate()
│       └── chartBuilder.js     # monta config do Chart.js
```

---

## 11. Exemplo Mínimo Funcional (Resumo Executivo)

```javascript
// pdf/PdfGenerator.js
const puppeteer = require('puppeteer');

class PdfGenerator {
  async generateResumoExecutivo(data) {
    const html = this.buildResumoHTML(data);
    return await this.renderPDF(html);
  }

  buildResumoHTML(data) {
    return `<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
  * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  @page { size: A4; margin: 0; }
  body { width: 210mm; min-height: 297mm; margin: 0; padding: 48px; font-family: Arial, sans-serif; background: white; }
  .brand { font-size: 22px; font-weight: 700; color: #00b894; }
  .title { font-size: 18px; color: #b2bec3; margin: 16px 0 4px; }
  .meta { font-size: 13px; color: #636e72; margin: 2px 0; }
  hr { border: none; border-top: 1.5px solid #dfe6e9; margin: 20px 0; }
  .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .card { border: 1px solid #dfe6e9; border-radius: 8px; padding: 16px 20px; }
  .label { font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #636e72; }
  .value { font-size: 28px; font-weight: 700; margin-top: 8px; color: #00b894; }
  .red { color: #d63031; }
</style>
</head>
<body>
  <div class="brand">Rise<br><small style="font-size:13px;font-weight:400;color:#636e72">Gestão Financeira</small></div>
  <div class="title">Resumo Executivo</div>
  <div class="meta">${data.periodo}</div>
  <div class="meta">${data.usuario}</div>
  <div class="meta">Gerado em ${data.geradoEm}</div>
  <hr>
  <div class="grid">
    <div class="card"><div class="label">Receitas</div><div class="value">R$ ${data.receitas.toLocaleString('pt-BR', {minimumFractionDigits:2})}</div></div>
    <div class="card"><div class="label">Despesas</div><div class="value red">R$ ${data.despesas.toLocaleString('pt-BR', {minimumFractionDigits:2})}</div></div>
    <div class="card"><div class="label">Investimentos</div><div class="value">R$ ${data.investimentos.toLocaleString('pt-BR', {minimumFractionDigits:2})}</div></div>
    <div class="card"><div class="label">Saldo Líquido</div><div class="value">R$ ${data.saldoLiquido.toLocaleString('pt-BR', {minimumFractionDigits:2})}</div></div>
  </div>
  <div data-loaded="true"></div>
</body>
</html>`;
  }

  async renderPDF(html) {
    const browser = await puppeteer.launch({
      headless: 'new',
      args: ['--no-sandbox', '--disable-setuid-sandbox', '--font-render-hinting=none'],
    });
    const page = await browser.newPage();
    await page.setViewport({ width: 794, height: 1123, deviceScaleFactor: 2 });
    await page.setContent(html, { waitUntil: 'networkidle0' });
    await page.waitForSelector('[data-loaded="true"]');

    const pdf = await page.pdf({
      format: 'A4',
      printBackground: true,
      margin: { top: '0', right: '0', bottom: '0', left: '0' },
    });

    await browser.close();
    return pdf;
  }
}

module.exports = new PdfGenerator();
```

---

## 12. Notas sobre o Espaço em Branco no Topo

Os PDFs do Rise têm ~55% da primeira página em branco. Isso é **intencional** — é um design de "capa", onde o conteúdo fica na metade inferior da primeira folha. Isso é conseguido com:

```css
/* Opção A: padding-top elevado */
.page { padding-top: 160mm; }

/* Opção B: min-height no header para empurrar conteúdo */
.cover-area { min-height: 55vh; display: flex; align-items: flex-end; }

/* Opção C: posicionamento absoluto */
.header { position: absolute; bottom: 40%; left: 48px; right: 48px; }
```

Se **não quiser** esse espaço em branco (o outro projeto pode ter esse bug sem intenção), verifique se há `padding-top`, `margin-top` ou `min-height` excessivos no container principal.

---

*Documento gerado em 29/05/2026 — Baseado na análise dos PDFs do projeto Rise Gestão Financeira*
