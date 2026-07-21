import fs from "node:fs/promises";
import { SpreadsheetFile, Workbook } from "@oai/artifact-tool";

const outputDir = new URL("./", import.meta.url).pathname;
const catalog = JSON.parse(await fs.readFile(`${outputDir}/catalog.json`, "utf8"));
const files = JSON.parse(await fs.readFile(`${outputDir}/files.json`, "utf8"));
const summary = JSON.parse(await fs.readFile(`${outputDir}/summary.json`, "utf8"));

const workbook = Workbook.create();
const summarySheet = workbook.worksheets.add("Сводка");
const catalogSheet = workbook.worksheets.add("Каталог");
const filesSheet = workbook.worksheets.add("Файлы");
const refsSheet = workbook.worksheets.add("Справочники");

const headerFormat = {
  fill: "#E5E7EB",
  font: { bold: true, color: "#111827" },
  verticalAlignment: "center",
  wrapText: true,
  borders: { preset: "outside", style: "thin", color: "#C7CDD4" },
};
const subHeaderFormat = {
  fill: "#F3F4F6",
  font: { bold: true, color: "#111827" },
  verticalAlignment: "center",
};

function formatHeader(sheet, range) {
  sheet.getRange(range).format = headerFormat;
}

function bytesFormula(cell) {
  return `=${cell}/1024/1024/1024`;
}

// Сводка
summarySheet.showGridLines = false;
summarySheet.getRange("A1:H1").merge();
summarySheet.getRange("A1").values = [["Каталог библиотеки Ассоциации EMDR России"]];
summarySheet.getRange("A1:H1").format = {
  font: { bold: true, size: 18, color: "#111827" },
  fill: "#F3F4F6",
  verticalAlignment: "center",
};
summarySheet.getRange("A1:H1").format.rowHeight = 34;
summarySheet.getRange("A3:H3").values = [[
  "Физических файлов", "", "Записей каталога", "", "Общий объем, ГБ", "", "Файлов с точными копиями", "",
]];
summarySheet.getRange("A4:H4").values = [[null, null, null, null, null, null, null, null]];
summarySheet.getRange("A4").formulas = [[`=COUNTA('Файлы'!A2:A${files.length + 1})`]];
summarySheet.getRange("C4").formulas = [[`=COUNTA('Каталог'!A2:A${catalog.length + 1})`]];
summarySheet.getRange("E4").formulas = [[bytesFormula(`SUM('Файлы'!F2:F${files.length + 1})`)]];
summarySheet.getRange("G4").formulas = [[`=COUNTIF('Файлы'!G2:G${files.length + 1},"Да")`]];
for (const col of ["A", "C", "E", "G"]) {
  summarySheet.getRange(`${col}3:${col}4`).format = {
    fill: "#F8FAFC",
    borders: { preset: "outside", style: "thin", color: "#D1D5DB" },
    verticalAlignment: "center",
  };
  summarySheet.getRange(`${col}3`).format.font = { bold: true, color: "#4B5563" };
  summarySheet.getRange(`${col}4`).format.font = { bold: true, size: 16, color: "#111827" };
}
summarySheet.getRange("E4").format.numberFormat = "0.00";

summarySheet.getRange("A7:B7").values = [["Категория", "Записей"]];
formatHeader(summarySheet, "A7:B7");
const categories = Object.keys(summary.categories);
summarySheet.getRangeByIndexes(7, 0, categories.length, 2).values = categories.map((category) => [category, null]);
for (let i = 0; i < categories.length; i++) {
  summarySheet.getCell(7 + i, 1).formulas = [[`=COUNTIF('Каталог'!C2:C${catalog.length + 1},A${8 + i})`]];
}
summarySheet.getRange("D7:E7").values = [["Вид материала", "Записей"]];
formatHeader(summarySheet, "D7:E7");
const types = Object.keys(summary.types).sort((a, b) => a.localeCompare(b, "ru"));
summarySheet.getRangeByIndexes(7, 3, types.length, 2).values = types.map((type) => [type, null]);
for (let i = 0; i < types.length; i++) {
  summarySheet.getCell(7 + i, 4).formulas = [[`=COUNTIF('Каталог'!E2:E${catalog.length + 1},D${8 + i})`]];
}
summarySheet.getRange("G7:H7").values = [["Принципы каталогизации", ""]];
summarySheet.getRange("G7:H7").merge();
formatHeader(summarySheet, "G7:H7");
summarySheet.getRange("G8:H13").merge();
summarySheet.getRange("G8").values = [[
  "• Категории повторяют верхний уровень исходных папок.\n• Связанные файлы объединены в одну библиотечную запись.\n• Все физические файлы перечислены на листе «Файлы».\n• На запись назначено не более трех тематических тегов.\n• Точные копии отмечены для редакторской проверки.",
]];
summarySheet.getRange("G8:H13").format = {
  fill: "#F8FAFC",
  wrapText: true,
  verticalAlignment: "top",
  borders: { preset: "outside", style: "thin", color: "#D1D5DB" },
};
summarySheet.getRange("A18:H18").merge();
summarySheet.getRange("A18").values = [[`Источник: ${summary.source_folder}`]];
summarySheet.getRange("A18:H18").format = { font: { italic: true, color: "#6B7280" }, wrapText: true };
summarySheet.getRange("A:H").format.columnWidth = 17;
summarySheet.getRange("A:A").format.columnWidth = 34;
summarySheet.getRange("D:D").format.columnWidth = 32;
summarySheet.getRange("G:H").format.columnWidth = 27;
summarySheet.getRange("3:4").format.rowHeight = 26;

// Каталог
const catalogHeaders = ["ID", "Название для библиотеки", "Категория", "Подкатегория", "Вид материала", "Теги", "Язык", "Год", "Файлов", "Форматы", "Размер, МБ", "Примечание"];
catalogSheet.getRangeByIndexes(0, 0, 1, catalogHeaders.length).values = [catalogHeaders];
formatHeader(catalogSheet, `A1:L1`);
const catalogValues = catalog.map((row) => [
  row.id, row.title, row.category, row.subcategory, row.type, row.tags, row.language, row.year, row.file_count, row.formats,
  row.size_bytes / 1024 / 1024, row.notes,
]);
catalogSheet.getRangeByIndexes(1, 0, catalogValues.length, catalogHeaders.length).values = catalogValues;
catalogSheet.freezePanes.freezeRows(1);
catalogSheet.getRange(`A2:A${catalog.length + 1}`).format.font = { color: "#374151" };
catalogSheet.getRange(`B2:F${catalog.length + 1}`).format.wrapText = true;
catalogSheet.getRange(`L2:L${catalog.length + 1}`).format.wrapText = true;
catalogSheet.getRange(`H2:I${catalog.length + 1}`).format.horizontalAlignment = "center";
catalogSheet.getRange(`K2:K${catalog.length + 1}`).format.numberFormat = "0.00";
catalogSheet.getRange(`A1:L${catalog.length + 1}`).format.borders = {
  insideHorizontal: { style: "thin", color: "#E5E7EB" },
  bottom: { style: "thin", color: "#D1D5DB" },
};
const catalogWidths = [13, 56, 31, 40, 24, 36, 15, 10, 10, 18, 14, 34];
catalogWidths.forEach((width, idx) => catalogSheet.getRangeByIndexes(0, idx, catalog.length + 1, 1).format.columnWidth = width);
catalogSheet.getRange("1:1").format.rowHeight = 30;
catalogSheet.getRange(`2:${catalog.length + 1}`).format.rowHeight = 34;

// Файлы
const fileHeaders = ["ID записи", "Название записи", "Имя файла", "Относительный путь", "Формат", "Размер, байт", "Точная копия", "Отпечаток"];
filesSheet.getRangeByIndexes(0, 0, 1, fileHeaders.length).values = [fileHeaders];
formatHeader(filesSheet, "A1:H1");
const fileValues = files.map((row) => [row.resource_id, row.title, row.filename, row.path, row.format, row.size_bytes, row.exact_duplicate, row.fingerprint]);
filesSheet.getRangeByIndexes(1, 0, fileValues.length, fileHeaders.length).values = fileValues;
filesSheet.freezePanes.freezeRows(1);
filesSheet.getRange(`B2:D${files.length + 1}`).format.wrapText = true;
filesSheet.getRange(`F2:F${files.length + 1}`).format.numberFormat = "#,##0";
filesSheet.getRange(`G2:G${files.length + 1}`).format.horizontalAlignment = "center";
filesSheet.getRange(`A1:H${files.length + 1}`).format.borders = {
  insideHorizontal: { style: "thin", color: "#E5E7EB" },
  bottom: { style: "thin", color: "#D1D5DB" },
};
[13, 48, 48, 75, 12, 18, 15, 16].forEach((width, idx) => filesSheet.getRangeByIndexes(0, idx, files.length + 1, 1).format.columnWidth = width);
filesSheet.getRange("1:1").format.rowHeight = 30;
filesSheet.getRange(`2:${files.length + 1}`).format.rowHeight = 34;

// Справочники
refsSheet.showGridLines = false;
refsSheet.getRange("A1:F1").merge();
refsSheet.getRange("A1").values = [["Справочники категорий, видов материалов и тегов"]];
refsSheet.getRange("A1:F1").format = { fill: "#F3F4F6", font: { bold: true, size: 16 }, verticalAlignment: "center" };
refsSheet.getRange("A3:B3").values = [["Категория", "Записей"]];
formatHeader(refsSheet, "A3:B3");
refsSheet.getRangeByIndexes(3, 0, categories.length, 2).values = categories.map((name) => [name, summary.categories[name]]);
refsSheet.getRange("D3:E3").values = [["Вид материала", "Записей"]];
formatHeader(refsSheet, "D3:E3");
refsSheet.getRangeByIndexes(3, 3, types.length, 2).values = types.map((name) => [name, summary.types[name]]);
const tags = Object.keys(summary.tags).sort((a, b) => a.localeCompare(b, "ru"));
const tagsStart = 12;
refsSheet.getRange(`A${tagsStart}:B${tagsStart}`).values = [["Тег", "Использований"]];
formatHeader(refsSheet, `A${tagsStart}:B${tagsStart}`);
refsSheet.getRangeByIndexes(tagsStart, 0, tags.length, 2).values = tags.map((name) => [name, summary.tags[name]]);
refsSheet.getRange("D12:F12").merge();
refsSheet.getRange("D12").values = [["Редакторская памятка"]];
formatHeader(refsSheet, "D12:F12");
refsSheet.getRange("D13:F19").merge();
refsSheet.getRange("D13").values = [[
  "Перед публикацией рекомендуется проверить строки с отметкой «Точная копия = Да», уточнить права доступа к книгам и статьям, а также заменить общие названия выступлений, если появятся программы конференций с точными темами докладов.",
]];
refsSheet.getRange("D13:F19").format = {
  fill: "#F8FAFC", wrapText: true, verticalAlignment: "top",
  borders: { preset: "outside", style: "thin", color: "#D1D5DB" },
};
refsSheet.getRange("A:F").format.columnWidth = 23;
refsSheet.getRange("A:A").format.columnWidth = 38;
refsSheet.getRange("D:D").format.columnWidth = 35;
refsSheet.getRange("A1:F1").format.rowHeight = 32;

// Compact verification
const checks = [];
checks.push((await workbook.inspect({ kind: "table", range: `Каталог!A1:L8`, include: "values,formulas", tableMaxRows: 8, tableMaxCols: 12 })).ndjson);
checks.push((await workbook.inspect({ kind: "table", range: `Файлы!A1:H8`, include: "values,formulas", tableMaxRows: 8, tableMaxCols: 8 })).ndjson);
checks.push((await workbook.inspect({ kind: "match", searchTerm: "#REF!|#DIV/0!|#VALUE!|#NAME\\?|#N/A", options: { useRegex: true, maxResults: 100 }, summary: "проверка ошибок формул" })).ndjson);
await fs.writeFile(`${outputDir}/verification.ndjson`, checks.join("\n"), "utf8");

for (const [sheetName, range, fileName] of [
  ["Сводка", "A1:H18", "preview-summary.png"],
  ["Каталог", "A1:L18", "preview-catalog.png"],
  ["Файлы", "A1:H18", "preview-files.png"],
  ["Справочники", "A1:F30", "preview-references.png"],
]) {
  const preview = await workbook.render({ sheetName, range, scale: 1.3, format: "png" });
  await fs.writeFile(`${outputDir}/${fileName}`, new Uint8Array(await preview.arrayBuffer()));
}

const output = await SpreadsheetFile.exportXlsx(workbook);
const finalPath = `${outputDir}/Каталог_библиотеки_EMDR_России.xlsx`;
await output.save(finalPath);
console.log(JSON.stringify({ finalPath, sheets: 4, catalogEntries: catalog.length, physicalFiles: files.length }));
