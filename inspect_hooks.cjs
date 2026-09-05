const fs = require('fs');
const filePath = 'c:/react2/saas_pos_app/src/screens/pos/POSTerminalScreen.tsx';
const code = fs.readFileSync(filePath, 'utf8');
const lines = code.split('\n');

console.log('=== All useState hooks ===');
lines.forEach((l, i) => {
  if (l.includes('useState(') || l.includes('useState<')) {
    console.log((i+1) + ': ' + l.trim());
  }
});

console.log('=== All useEffect / useFocusEffect hooks ===');
lines.forEach((l, i) => {
  if (l.includes('useEffect(') || l.includes('useFocusEffect(')) {
    console.log((i+1) + ': ' + l.trim());
  }
});
