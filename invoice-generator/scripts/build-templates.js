// invoice-generator/scripts/build-templates.js
// Generates static .xlsx and .docx invoice templates per visual style
// (classic, modern, formal, elegant, ribbon). The .xlsx files are
// committed to invoice-generator/templates/ and served as direct downloads
// from /invoice-generator/templates/{style}.xlsx. The .docx files are
// dev-only artifacts intended for upload to Google Drive (where each is
// converted to a Google Doc and the resulting "Make a copy" URL is recorded
// in invoice-generator/data/template-assets.json).
//
// Run with:
//   npm run build:templates
//
// Uses the npm packages `exceljs` and `docx` (the same docx library the
// browser-side generator uses, consumed via npm here for Node).

import ExcelJS from 'exceljs';
import {
  Document, Paragraph, TextRun, Table, TableRow, TableCell,
  AlignmentType, WidthType, BorderStyle, ShadingType, HeadingLevel,
  Footer, Packer,
} from 'docx';
import { writeFile, mkdir } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const OUT_DIR = resolve(__dirname, '..', 'templates');

// Per-style visual cues mirror the browser-side TEMPLATE_STYLE map in docx.js.
const STYLES = {
  classic: {
    label: 'Classic',
    headingFont: 'Arial',
    headingColor: '1A1A1A',
    headerFill: null,
    accentColor: '1A1A1A',
  },
  modern: {
    label: 'Modern',
    headingFont: 'Arial',
    headingColor: '2C7A7B',
    headerFill: null,
    accentColor: '2C7A7B',
  },
  formal: {
    label: 'Formal',
    headingFont: 'Arial',
    headingColor: '1A1A1A',
    headerFill: null,
    accentColor: 'CCCCCC',
  },
  elegant: {
    label: 'Elegant',
    headingFont: 'Arial',
    headingColor: '1A1A1A',
    headerFill: 'FBBF24',
    accentColor: 'FBBF24',
  },
  ribbon: {
    label: 'Ribbon',
    headingFont: 'Georgia',
    headingColor: '1A1A1A',
    headerFill: null,
    accentColor: '0A2540',
  },
};

// ---------- Excel ----------

async function buildXlsx(styleId) {
  const style = STYLES[styleId];
  const workbook = new ExcelJS.Workbook();
  workbook.creator = 'Argo Books';
  workbook.title = `${style.label} Invoice Template`;
  const ws = workbook.addWorksheet('Invoice');

  ws.getColumn('A').width = 26;
  ws.getColumn('B').width = 18;
  ws.getColumn('C').width = 12;
  ws.getColumn('D').width = 14;
  ws.getColumn('E').width = 14;
  ws.getColumn('F').width = 14;
  ws.getColumn('G').width = 14;

  // Logo placeholder cell (top-left)
  ws.mergeCells('A1:B4');
  ws.getCell('A1').value = '[Your logo here]';
  ws.getCell('A1').alignment = { horizontal: 'center', vertical: 'middle' };
  ws.getCell('A1').font = { name: style.headingFont, size: 12, color: { argb: '888888' } };
  ws.getCell('A1').border = {
    top: { style: 'thin', color: { argb: 'CCCCCC' } },
    left: { style: 'thin', color: { argb: 'CCCCCC' } },
    bottom: { style: 'thin', color: { argb: 'CCCCCC' } },
    right: { style: 'thin', color: { argb: 'CCCCCC' } },
  };

  // INVOICE heading (top-right)
  ws.getCell('F1').value = 'INVOICE';
  ws.getCell('F1').font = {
    name: style.headingFont,
    size: 22,
    bold: true,
    color: { argb: style.headingColor },
  };
  ws.getCell('F1').alignment = { horizontal: 'right' };
  if (style.headerFill) {
    ws.getCell('F1').fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: style.headerFill } };
  }

  ws.getCell('F2').value = '#';
  ws.getCell('F2').font = { name: style.headingFont, size: 12, color: { argb: '666666' } };
  ws.getCell('F2').alignment = { horizontal: 'right' };
  ws.getCell('G2').value = '1042';
  ws.getCell('G2').alignment = { horizontal: 'right' };

  // Thin accent rule under header band (Ribbon only).
  if (styleId === 'ribbon') {
    ['A5', 'B5', 'C5', 'D5', 'E5', 'F5', 'G5'].forEach((addr) => {
      ws.getCell(addr).border = {
        bottom: { style: 'thin', color: { argb: style.accentColor } },
      };
    });
  }

  // From block
  ws.getCell('A6').value = 'From';
  ws.getCell('A6').font = { name: 'Arial', size: 9, bold: true, color: { argb: '666666' } };
  ws.getCell('A7').value = '[Your business name]';
  ws.getCell('A8').value = '[Street address]';
  ws.getCell('A9').value = '[City, postcode]';

  // Metadata column (right side, rows 6-9)
  const metaRows = [
    ['F6', 'Date', 'G6', '[YYYY-MM-DD]'],
    ['F7', 'Due Date', 'G7', '[YYYY-MM-DD]'],
    ['F8', 'Terms', 'G8', 'Net 30'],
    ['F9', 'PO Number', 'G9', ''],
  ];
  metaRows.forEach(([labelCell, label, valueCell, value]) => {
    const lc = ws.getCell(labelCell);
    lc.value = label;
    lc.font = { name: 'Arial', size: 9, bold: true, color: { argb: '666666' } };
    lc.alignment = { horizontal: 'right' };
    const vc = ws.getCell(valueCell);
    vc.value = value;
    vc.alignment = { horizontal: 'right' };
  });

  // Bill To block
  ws.getCell('A11').value = 'Bill To';
  ws.getCell('A11').font = { name: 'Arial', size: 9, bold: true, color: { argb: '666666' } };
  ws.getCell('A12').value = '[Client name]';
  ws.getCell('A13').value = '[Client address]';

  // Line items header (row 15)
  ws.mergeCells('A15:B15');
  const headerCells = [
    { addr: 'A15', text: 'Description', align: 'left' },
    { addr: 'C15', text: 'Qty', align: 'right' },
    { addr: 'D15', text: 'Rate', align: 'right' },
    { addr: 'E15', text: 'Amount', align: 'right' },
  ];
  headerCells.forEach((h) => {
    const c = ws.getCell(h.addr);
    c.value = h.text;
    c.alignment = { horizontal: h.align };
    if (styleId === 'classic') {
      c.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: '1A1A1A' } };
      c.font = { name: 'Arial', size: 11, bold: true, color: { argb: 'FFFFFF' } };
    } else if (styleId === 'elegant') {
      c.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: style.headerFill } };
      c.font = { name: 'Arial', size: 11, bold: true, color: { argb: style.headingColor } };
    } else if (styleId === 'formal') {
      c.font = { name: 'Arial', size: 11, bold: true, color: { argb: '1A1A1A' } };
      c.border = { bottom: { style: 'thin', color: { argb: '1A1A1A' } } };
    } else {
      c.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'F3F5F8' } };
      c.font = { name: style.headingFont, size: 11, bold: true, color: { argb: '1A1A1A' } };
    }
  });

  // 10 empty line item rows with formulas
  for (let r = 16; r <= 25; r++) {
    ws.mergeCells(`A${r}:B${r}`);
    ws.getCell(`C${r}`).value = '';
    ws.getCell(`C${r}`).numFmt = '0';
    ws.getCell(`C${r}`).alignment = { horizontal: 'right' };
    ws.getCell(`D${r}`).value = '';
    ws.getCell(`D${r}`).numFmt = '"$"#,##0.00';
    ws.getCell(`D${r}`).alignment = { horizontal: 'right' };
    ws.getCell(`E${r}`).value = { formula: `IF(C${r}*D${r}=0,"",C${r}*D${r})` };
    ws.getCell(`E${r}`).numFmt = '"$"#,##0.00';
    ws.getCell(`E${r}`).alignment = { horizontal: 'right' };
    if (styleId !== 'formal') {
      ['A', 'C', 'D', 'E'].forEach((col) => {
        ws.getCell(`${col}${r}`).border = {
          bottom: { style: 'hair', color: { argb: 'DDDDDD' } },
        };
      });
    }
  }

  // Totals block
  const totalsRows = [
    { row: 27, label: 'Subtotal', formula: '=SUM(E16:E25)' },
    { row: 28, label: 'Tax Rate (%)', value: 0, numFmt: '0.00' },
    { row: 29, label: 'Tax', formula: '=E27*(E28/100)' },
    { row: 30, label: 'Total', formula: '=E27+E29', bold: true },
    { row: 31, label: 'Amount Paid', value: 0 },
    { row: 32, label: 'Balance Due', formula: '=E30-E31', bold: true },
  ];
  totalsRows.forEach((tr) => {
    const labelCell = ws.getCell(`D${tr.row}`);
    labelCell.value = tr.label;
    labelCell.alignment = { horizontal: 'right' };
    labelCell.font = {
      name: 'Arial', size: 10, bold: !!tr.bold,
      color: tr.bold ? { argb: '000000' } : { argb: '555555' },
    };
    const valCell = ws.getCell(`E${tr.row}`);
    if (tr.formula) {
      valCell.value = { formula: tr.formula };
    } else {
      valCell.value = tr.value;
    }
    valCell.numFmt = tr.numFmt || '"$"#,##0.00';
    valCell.alignment = { horizontal: 'right' };
    valCell.font = { name: 'Arial', size: 10, bold: !!tr.bold };
  });

  // Notes / Terms blocks
  ws.getCell('A35').value = 'Notes';
  ws.getCell('A35').font = { name: 'Arial', size: 9, bold: true, color: { argb: '666666' } };

  ws.getCell('A39').value = 'Terms';
  ws.getCell('A39').font = { name: 'Arial', size: 9, bold: true, color: { argb: '666666' } };

  // Footer
  ws.getCell('A45').value = 'Made with argorobots.com';
  ws.getCell('A45').font = { name: 'Arial', size: 9, color: { argb: '999999' } };

  const outPath = resolve(OUT_DIR, `${styleId}.xlsx`);
  await workbook.xlsx.writeFile(outPath);
  console.log(`Wrote ${outPath}`);
}

// ---------- Word ----------

const noBorder = { style: BorderStyle.NONE, size: 0, color: 'FFFFFF' };
const cellBorders = { top: noBorder, bottom: noBorder, left: noBorder, right: noBorder };
const tableBorders = {
  top: noBorder, bottom: noBorder, left: noBorder, right: noBorder,
  insideHorizontal: noBorder, insideVertical: noBorder,
};

function buildHeaderTable(style, styleId) {
  const leftChildren = [
    new Paragraph({
      children: [new TextRun({ text: 'From', bold: true, size: 18, color: '666666' })],
      spacing: { after: 40 },
    }),
    new Paragraph({ children: [new TextRun({ text: '[Your business name]', size: 20 })] }),
    new Paragraph({ children: [new TextRun({ text: '[Street address]', size: 20 })] }),
    new Paragraph({ children: [new TextRun({ text: '[City, postcode]', size: 20 })] }),
  ];

  const rightChildren = [
    new Paragraph({
      children: [new TextRun({
        text: 'INVOICE',
        bold: true,
        size: 44,
        font: style.headingFont,
        color: style.headingColor,
      })],
      alignment: AlignmentType.RIGHT,
      heading: HeadingLevel.HEADING_1,
    }),
    new Paragraph({
      children: [new TextRun({ text: '# 1042', size: 22 })],
      alignment: AlignmentType.RIGHT,
    }),
  ];

  const rightCellBorders = {
    top: noBorder,
    bottom: styleId === 'ribbon'
      ? { style: BorderStyle.SINGLE, size: 6, color: style.accentColor }
      : noBorder,
    left: noBorder,
    right: noBorder,
  };

  return new Table({
    width: { size: 100, type: WidthType.PERCENTAGE },
    borders: tableBorders,
    rows: [
      new TableRow({
        children: [
          new TableCell({
            width: { size: 60, type: WidthType.PERCENTAGE },
            borders: cellBorders,
            children: leftChildren,
          }),
          new TableCell({
            width: { size: 40, type: WidthType.PERCENTAGE },
            borders: rightCellBorders,
            shading: style.headerFill
              ? { type: ShadingType.CLEAR, color: 'auto', fill: style.headerFill }
              : undefined,
            children: rightChildren,
          }),
        ],
      }),
    ],
  });
}

function buildPartiesTable() {
  function block(label, lines) {
    const children = [
      new Paragraph({
        children: [new TextRun({ text: label, bold: true, size: 18, color: '666666' })],
        spacing: { after: 40 },
      }),
    ];
    lines.forEach((line) => {
      children.push(new Paragraph({ children: [new TextRun({ text: line, size: 20 })] }));
    });
    return children;
  }

  function metaLine(label, value) {
    return new Paragraph({
      children: [
        new TextRun({ text: `${label}: `, bold: true, size: 18, color: '666666' }),
        new TextRun({ text: value, size: 20 }),
      ],
      alignment: AlignmentType.RIGHT,
      spacing: { after: 40 },
    });
  }

  return new Table({
    width: { size: 100, type: WidthType.PERCENTAGE },
    borders: tableBorders,
    rows: [
      new TableRow({
        children: [
          new TableCell({
            width: { size: 50, type: WidthType.PERCENTAGE },
            borders: cellBorders,
            children: block('Bill To', ['[Client name]', '[Client address]']),
          }),
          new TableCell({
            width: { size: 50, type: WidthType.PERCENTAGE },
            borders: cellBorders,
            children: [
              metaLine('Date', '[YYYY-MM-DD]'),
              metaLine('Due Date', '[YYYY-MM-DD]'),
              metaLine('Terms', 'Net 30'),
            ],
          }),
        ],
      }),
    ],
  });
}

function buildLineItemsTable(style, styleId) {
  const thinBorder = { style: BorderStyle.SINGLE, size: 4, color: 'DDDDDD' };

  function headerCell(text, alignment) {
    let fill = 'F3F5F8';
    let textColor = '1A1A1A';
    if (styleId === 'classic') { fill = '1A1A1A'; textColor = 'FFFFFF'; }
    if (styleId === 'elegant') { fill = style.headerFill; textColor = style.headingColor; }
    return new TableCell({
      shading: { type: ShadingType.CLEAR, color: 'auto', fill },
      children: [new Paragraph({
        children: [new TextRun({ text, bold: true, size: 20, color: textColor })],
        alignment: alignment || AlignmentType.LEFT,
      })],
    });
  }

  function bodyCell(text, alignment) {
    return new TableCell({
      children: [new Paragraph({
        children: [new TextRun({ text, size: 20 })],
        alignment: alignment || AlignmentType.LEFT,
      })],
    });
  }

  const headerRow = new TableRow({
    tableHeader: true,
    children: [
      headerCell('Description'),
      headerCell('Quantity', AlignmentType.RIGHT),
      headerCell('Rate', AlignmentType.RIGHT),
      headerCell('Amount', AlignmentType.RIGHT),
    ],
  });

  const sampleRows = [
    ['Service or product description', '1', '$0.00', '$0.00'],
    ['', '', '', ''],
    ['', '', '', ''],
  ].map(([d, q, r, a]) => new TableRow({
    children: [
      bodyCell(d),
      bodyCell(q, AlignmentType.RIGHT),
      bodyCell(r, AlignmentType.RIGHT),
      bodyCell(a, AlignmentType.RIGHT),
    ],
  }));

  return new Table({
    width: { size: 100, type: WidthType.PERCENTAGE },
    borders: {
      top: thinBorder, bottom: thinBorder, left: thinBorder, right: thinBorder,
      insideHorizontal: thinBorder, insideVertical: thinBorder,
    },
    rows: [headerRow, ...sampleRows],
  });
}

function buildTotalsTable() {
  function row(label, value, bold) {
    return new TableRow({
      children: [
        new TableCell({
          width: { size: 70, type: WidthType.PERCENTAGE },
          borders: cellBorders,
          children: [new Paragraph({
            children: [new TextRun({
              text: label, bold: !!bold, size: 20,
              color: bold ? '000000' : '555555',
            })],
            alignment: AlignmentType.RIGHT,
          })],
        }),
        new TableCell({
          width: { size: 30, type: WidthType.PERCENTAGE },
          borders: cellBorders,
          children: [new Paragraph({
            children: [new TextRun({ text: value, bold: !!bold, size: 20 })],
            alignment: AlignmentType.RIGHT,
          })],
        }),
      ],
    });
  }

  return new Table({
    width: { size: 100, type: WidthType.PERCENTAGE },
    borders: tableBorders,
    rows: [
      row('Subtotal', '$0.00'),
      row('Tax', '$0.00'),
      row('Total', '$0.00', true),
    ],
  });
}

async function buildDocx(styleId) {
  const style = STYLES[styleId];
  const spacer = (after) => new Paragraph({
    children: [new TextRun({ text: '' })],
    spacing: { after: after || 200 },
  });

  const doc = new Document({
    creator: 'Argo Books',
    title: `${style.label} Invoice Template`,
    sections: [{
      properties: {},
      footers: {
        default: new Footer({
          children: [new Paragraph({
            children: [new TextRun({ text: 'Made with argorobots.com', size: 18, color: '999999' })],
            alignment: AlignmentType.CENTER,
          })],
        }),
      },
      children: [
        buildHeaderTable(style, styleId),
        spacer(200),
        buildPartiesTable(),
        spacer(200),
        buildLineItemsTable(style, styleId),
        spacer(200),
        buildTotalsTable(),
      ],
    }],
  });

  const buffer = await Packer.toBuffer(doc);
  const outPath = resolve(OUT_DIR, `${styleId}.docx`);
  await writeFile(outPath, buffer);
  console.log(`Wrote ${outPath}`);
}

async function main() {
  await mkdir(OUT_DIR, { recursive: true });
  const styles = Object.keys(STYLES);
  for (const styleId of styles) {
    await buildXlsx(styleId);
    await buildDocx(styleId);
  }
  console.log(`Done. Generated ${styles.length} .xlsx and ${styles.length} .docx files in ${OUT_DIR}`);
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
