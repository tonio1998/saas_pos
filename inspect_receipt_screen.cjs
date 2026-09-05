const fs = require('fs');
const recCode = fs.readFileSync('c:/react2/saas_pos_app/src/screens/reports/ReceiptScreen.tsx', 'utf8');

console.log('=== ReceiptScreen.tsx (first 3000 chars) ===');
console.log(recCode.substring(0, 3000));
