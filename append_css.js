const fs = require('fs');
const file = 'extensions/form-builder-block/assets/form-builder-dynamic1.css';
const cssData = `
/* Layout Fixes */
.form-builder-container form {
  display: flex !important;
  flex-wrap: wrap !important;
  width: 100% !important;
  margin: 0 !important;
  padding: 0 !important;
}
.form-builder-container * {
  box-sizing: border-box !important;
}
.code-form-control {
  flex-shrink: 0 !important;
  padding-right: 5px !important;
  padding-left: 5px !important;
}
.code-form-control input:not([type="checkbox"]):not([type="radio"]),
.code-form-control textarea,
.code-form-control select {
  width: 100% !important;
}
`;
fs.appendFileSync(file, cssData, 'utf8');
console.log('Appended successfully');
