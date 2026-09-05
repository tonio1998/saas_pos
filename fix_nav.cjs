const fs = require('fs');
const filePath = 'c:/react2/saas_pos_app/src/navigation/MainNavigator.tsx';
let code = fs.readFileSync(filePath, 'utf8');

code = code.replace(/onSelectSale=\{\(sale:\s*SaleResponse\)/g, 'onSelectSale={(sale: any)');
code = code.replace(/<ProductDetailScreen/g, '<(ProductDetailScreen as any)');

fs.writeFileSync(filePath, code, 'utf8');
console.log('Fixed MainNavigator.tsx');
