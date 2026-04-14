const fs = require('fs');

const file = 'extensions/form-builder-block/blocks/form-dynamic.liquid';
const content = fs.readFileSync(file, 'utf8');

const cssStartToken = "styleElement.textContent = `";
const cssStartIndex = content.indexOf(cssStartToken);
if (cssStartIndex === -1) {
  console.log('CSS block not found');
  process.exit();
}
const cssEndIndex = content.indexOf("`;", cssStartIndex);

const cssContent = content.substring(cssStartIndex + cssStartToken.length, cssEndIndex);

if (!fs.existsSync('extensions/form-builder-block/assets')) {
  fs.mkdirSync('extensions/form-builder-block/assets', { recursive: true });
}

fs.writeFileSync('extensions/form-builder-block/assets/form-builder-dynamic1.css', cssContent);

let newContent = content.substring(0, cssStartIndex + cssStartToken.length) + content.substring(cssEndIndex);

const schemaStart = newContent.indexOf('{% schema %}');
const schemaContent = newContent.substring(schemaStart);
let newSchema = schemaContent
    .replace(/\{\s*"type":\s*"header"[^}]+\},/g, '')
    .replace(/\{\s*"type":\s*"paragraph"[^}]+\},/g, '')
    .replace(/\{\s*"type":\s*"richtext"[^}]+\},/g, ''); // Strip non-essential non-interactives to pass 6 settings limit

newContent = newContent.substring(0, schemaStart) + newSchema;
newContent = "{{ 'form-builder-dynamic1.css' | asset_url | stylesheet_tag }}\n" + newContent;

fs.writeFileSync(file, newContent, 'utf8');
console.log('Fixed file layout successfully.');
