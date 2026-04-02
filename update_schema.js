const fs = require('fs');
const file = 'extensions/form-builder-block/blocks/form-dynamic.liquid';
const content = fs.readFileSync(file, 'utf8');

const schemaStart = content.indexOf('{% schema %}');
if (schemaStart !== -1) {
  let newSchema = `{% schema %}
{
  "name": "Easy Form Builder",
  "target": "section",
  "settings": [
    {
      "type": "text",
      "id": "form_id",
      "label": "Form ID",
      "info": "Enter the Form ID. You can find this in your app dashboard.",
      "default": "0"
    },
    {
      "type": "select",
      "id": "form_alignment",
      "label": "Form Alignment",
      "options": [
        {
          "value": "left",
          "label": "Left"
        },
        {
          "value": "center",
          "label": "Center"
        },
        {
          "value": "right",
          "label": "Right"
        }
      ],
      "default": "center"
    }
  ]
}
{% endschema %}`;

  const newContent = content.substring(0, schemaStart) + newSchema;
  fs.writeFileSync(file, newContent, 'utf8');
  console.log('Schema updated successfully.');
}
