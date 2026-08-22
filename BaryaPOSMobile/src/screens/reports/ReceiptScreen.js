import React from 'react';
import {
  StyleSheet,
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  Alert,
  SafeAreaView,
} from 'react-native';

export default function ReceiptScreen({ route, navigation }) {
  const sale = route.params?.sale || {};
  const items = sale.items || [];
  const tenant = sale.tenant || {};

  const handlePrintBluetooth = () => {
    Alert.alert('ESC/POS Thermal Printer', 'Printing official receipt via Bluetooth...');
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.topHeader}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
          <Text style={styles.backBtnText}>◀ Back</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Official Receipt Preview</Text>
        <TouchableOpacity onPress={handlePrintBluetooth} style={styles.printBtn}>
          <Text style={styles.printBtnText}>🖨️ Print</Text>
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.receiptCard}>
          <Text style={styles.businessName}>{tenant.business_name || 'BaryaPOS Store'}</Text>
          <Text style={styles.receiptSub}>TIN: {tenant.tin || '000-000-000-000'}</Text>
          <Text style={styles.receiptSub}>Branch: {tenant.branch_code || 'Main'}</Text>
          <View style={styles.divider} />

          <View style={styles.metaRow}>
            <Text style={styles.metaText}>Invoice No:</Text>
            <Text style={styles.metaVal}>#{sale.sale_code || sale.invoice_no || sale.id || '260822-0001'}</Text>
          </View>
          <View style={styles.metaRow}>
            <Text style={styles.metaText}>Date:</Text>
            <Text style={styles.metaVal}>{new Date().toLocaleString()}</Text>
          </View>
          <View style={styles.divider} />

          <View style={styles.itemHeaderRow}>
            <Text style={styles.colItem}>ITEM</Text>
            <Text style={styles.colQty}>QTY</Text>
            <Text style={styles.colPrice}>PRICE</Text>
            <Text style={styles.colTotal}>TOTAL</Text>
          </View>

          {items.map((it, idx) => (
            <View key={idx} style={styles.itemRow}>
              <Text style={styles.colItem} numberOfLines={1}>{it.name || it.product_name}</Text>
              <Text style={styles.colQty}>{it.qty}</Text>
              <Text style={styles.colPrice}>₱{Number(it.price || it.unit_price || 0).toFixed(2)}</Text>
              <Text style={styles.colTotal}>₱{Number(it.subtotal || it.line_total || (it.qty * (it.price || 0))).toFixed(2)}</Text>
            </View>
          ))}

          <View style={styles.divider} />

          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>Subtotal:</Text>
            <Text style={styles.summaryVal}>₱{Number(sale.subtotal || 0).toFixed(2)}</Text>
          </View>

          <View style={[styles.summaryRow, styles.totalRow]}>
            <Text style={styles.totalLabel}>TOTAL AMOUNT:</Text>
            <Text style={styles.totalVal}>₱{Number(sale.total_amount || sale.total || 0).toFixed(2)}</Text>
          </View>

          <View style={styles.divider} />
          <Text style={styles.birFooter}>THIS SERVES AS YOUR OFFICIAL RECEIPT</Text>
          <Text style={styles.birFooterSub}>Thank you for your purchase!</Text>
        </View>

        <TouchableOpacity style={styles.actionPrintBtn} onPress={handlePrintBluetooth}>
          <Text style={styles.actionPrintText}>🖨️ PRINT ESC/POS THERMAL RECEIPT</Text>
        </TouchableOpacity>

        <TouchableOpacity style={styles.actionDoneBtn} onPress={() => navigation.navigate('POSTerminal')}>
          <Text style={styles.actionDoneText}>NEW SALE TRANSACTION ➔</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0f172a',
  },
  topHeader: {
    height: 56,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    borderBottomWidth: 1,
    borderColor: '#1e293b',
  },
  backBtnText: {
    color: '#38bdf8',
    fontWeight: '700',
    fontSize: 14,
  },
  headerTitle: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: '800',
  },
  printBtn: {
    backgroundColor: '#059669',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
  },
  printBtnText: {
    color: '#ffffff',
    fontWeight: '800',
    fontSize: 12,
  },
  scrollContent: {
    padding: 16,
    alignItems: 'center',
  },
  receiptCard: {
    width: '100%',
    maxWidth: 380,
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 20,
  },
  businessName: {
    fontSize: 18,
    fontWeight: '900',
    color: '#0f172a',
    textAlign: 'center',
  },
  receiptSub: {
    fontSize: 11,
    color: '#64748b',
    textAlign: 'center',
    marginTop: 2,
  },
  divider: {
    height: 1,
    backgroundColor: '#e2e8f0',
    marginVertical: 12,
  },
  metaRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  metaText: {
    fontSize: 11,
    color: '#64748b',
  },
  metaVal: {
    fontSize: 11,
    fontWeight: '700',
    color: '#0f172a',
  },
  itemHeaderRow: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    borderColor: '#cbd5e1',
    paddingBottom: 6,
    marginBottom: 6,
  },
  colItem: {
    flex: 2,
    fontSize: 11,
    fontWeight: '800',
    color: '#475569',
  },
  colQty: {
    width: 36,
    fontSize: 11,
    fontWeight: '800',
    color: '#475569',
    textAlign: 'center',
  },
  colPrice: {
    width: 60,
    fontSize: 11,
    fontWeight: '800',
    color: '#475569',
    textAlign: 'right',
  },
  colTotal: {
    width: 70,
    fontSize: 11,
    fontWeight: '800',
    color: '#475569',
    textAlign: 'right',
  },
  itemRow: {
    flexDirection: 'row',
    paddingVertical: 4,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  summaryLabel: {
    fontSize: 12,
    color: '#475569',
  },
  summaryVal: {
    fontSize: 12,
    fontWeight: '700',
    color: '#0f172a',
  },
  totalRow: {
    marginTop: 6,
    paddingTop: 6,
    borderTopWidth: 1,
    borderColor: '#e2e8f0',
  },
  totalLabel: {
    fontSize: 14,
    fontWeight: '900',
    color: '#0f172a',
  },
  totalVal: {
    fontSize: 16,
    fontWeight: '900',
    color: '#059669',
  },
  birFooter: {
    fontSize: 11,
    fontWeight: '800',
    color: '#0f172a',
    textAlign: 'center',
    marginTop: 6,
  },
  birFooterSub: {
    fontSize: 10,
    color: '#64748b',
    textAlign: 'center',
    marginTop: 2,
  },
  actionPrintBtn: {
    width: '100%',
    maxWidth: 380,
    backgroundColor: '#059669',
    borderRadius: 14,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 20,
  },
  actionPrintText: {
    color: '#ffffff',
    fontWeight: '800',
    fontSize: 13,
  },
  actionDoneBtn: {
    width: '100%',
    maxWidth: 380,
    backgroundColor: '#1e293b',
    borderRadius: 14,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 10,
  },
  actionDoneText: {
    color: '#ffffff',
    fontWeight: '800',
    fontSize: 13,
  },
});
