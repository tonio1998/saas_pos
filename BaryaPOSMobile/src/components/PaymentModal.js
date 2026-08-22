import React, { useState, useEffect } from 'react';
import {
  StyleSheet,
  View,
  Text,
  Modal,
  TouchableOpacity,
  TextInput,
  ScrollView,
  ActivityIndicator,
  Alert,
} from 'react-native';
import posApi from '../services/api';
import useResponsive from '../hooks/useResponsive';

export default function PaymentModal({
  visible,
  totalAmount,
  cartItems,
  customer,
  priceMode,
  tenantId,
  cashierId,
  onClose,
  onSaleComplete,
}) {
  const { isTablet } = useResponsive();
  const [paymentMethod, setPaymentMethod] = useState('cash');
  const [tenderedInput, setTenderedInput] = useState('');
  const [referenceNo, setReferenceNo] = useState('');

  const [discountType, setDiscountType] = useState('none');
  const [discountValue, setDiscountValue] = useState(0);

  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    if (visible) {
      setTenderedInput(totalAmount > 0 ? String(totalAmount) : '');
      setPaymentMethod('cash');
      setReferenceNo('');
    }
  }, [visible, totalAmount]);

  const tenderedVal = parseFloat(tenderedInput) || 0;
  const finalTotal = Math.max(0, totalAmount - discountValue);
  const changeVal = Math.max(0, tenderedVal - finalTotal);

  const handleQuickTender = (val) => {
    if (val === 'exact') {
      setTenderedInput(String(finalTotal));
    } else {
      setTenderedInput(String(val));
    }
  };

  const handleConfirmSale = async () => {
    if (paymentMethod === 'cash' && tenderedVal < finalTotal) {
      Alert.alert('Insufficient Tender', `Amount tendered (₱${tenderedVal.toFixed(2)}) is less than total due (₱${finalTotal.toFixed(2)}).`);
      return;
    }

    if (paymentMethod !== 'cash' && paymentMethod !== 'credit' && !referenceNo.trim()) {
      Alert.alert('Reference Required', `Please enter ${paymentMethod.toUpperCase()} reference transaction number.`);
      return;
    }

    setSubmitting(true);
    try {
      const payload = {
        tenant_id: tenantId || 1,
        cashier_id: cashierId || 1,
        customer_id: customer?.id || null,
        subtotal: totalAmount,
        discount_amount: discountValue,
        discount_type: discountType !== 'none' ? discountType : null,
        total_amount: finalTotal,
        tendered_amount: tenderedVal,
        change_amount: changeVal,
        payment_method: paymentMethod,
        reference_number: referenceNo.trim(),
        price_mode: priceMode,
        items: cartItems.map(item => ({
          product_id: item.id,
          variant_id: item.variant_id || null,
          qty: item.qty,
          price: item.price,
          subtotal: item.qty * item.price,
        })),
      };

      const res = await posApi.submitSale(payload);

      if (res.success || res.status) {
        Alert.alert('Sale Completed! 🎉', `Invoice Code: ${res.sale_code || res.id || 'SUCCESS'}`);
        onSaleComplete(res);
      } else {
        Alert.alert('Checkout Failed', res.message || 'Could not complete sale.');
      }
    } catch (err) {
      console.error('Submit sale error:', err);
      Alert.alert('Error', err.response?.data?.message || 'Failed to submit sale to server.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <Modal
      visible={visible}
      transparent
      animationType="slide"
      onRequestClose={onClose}
    >
      <View style={styles.backdrop}>
        <View style={[styles.card, isTablet && styles.tabletCard]}>
          <View style={styles.header}>
            <View style={styles.headerLeft}>
              <View style={styles.headerIcon}>
                <Text style={styles.headerIconText}>💳</Text>
              </View>
              <View>
                <Text style={styles.title}>Payment & Checkout</Text>
                <Text style={styles.subtitle}>
                  {customer ? `Suki: ${customer.name}` : 'Walk-in Customer'}
                </Text>
              </View>
            </View>
            <TouchableOpacity onPress={onClose} style={styles.closeBtn}>
              <Text style={styles.closeBtnText}>✕</Text>
            </TouchableOpacity>
          </View>

          <ScrollView style={styles.scrollBody}>
            <View style={styles.totalBox}>
              <Text style={styles.totalLabel}>TOTAL AMOUNT DUE</Text>
              <Text style={styles.totalAmount}>₱{finalTotal.toFixed(2)}</Text>
            </View>

            <Text style={styles.sectionLabel}>Payment Method</Text>
            <View style={styles.methodGrid}>
              {[
                { key: 'cash', label: '💵 Cash', color: '#059669' },
                { key: 'gcash', label: '💙 GCash', color: '#0284c7' },
                { key: 'maya', label: '💚 Maya', color: '#0d9488' },
                { key: 'credit', label: '📕 Utang / Credit', color: '#dc2626' },
              ].map(m => (
                <TouchableOpacity
                  key={m.key}
                  style={[
                    styles.methodChip,
                    paymentMethod === m.key && { backgroundColor: m.color, borderColor: m.color }
                  ]}
                  onPress={() => setPaymentMethod(m.key)}
                >
                  <Text style={[
                    styles.methodChipText,
                    paymentMethod === m.key && styles.methodChipTextActive
                  ]}>
                    {m.label}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>

            {paymentMethod === 'cash' ? (
              <View style={styles.tenderSection}>
                <Text style={styles.sectionLabel}>Amount Tendered</Text>
                <TextInput
                  style={styles.tenderInput}
                  placeholder="0.00"
                  placeholderTextColor="#94a3b8"
                  value={tenderedInput}
                  onChangeText={setTenderedInput}
                  keyboardType="numeric"
                />

                <View style={styles.quickGrid}>
                  {['exact', 20, 50, 100, 200, 500, 1000].map((val) => (
                    <TouchableOpacity
                      key={String(val)}
                      style={styles.quickBtn}
                      onPress={() => handleQuickTender(val)}
                    >
                      <Text style={styles.quickBtnText}>
                        {val === 'exact' ? 'Exact' : `₱${val}`}
                      </Text>
                    </TouchableOpacity>
                  ))}
                </View>

                <View style={styles.changeBox}>
                  <Text style={styles.changeLabel}>CHANGE TO RETURN</Text>
                  <Text style={styles.changeAmount}>₱{changeVal.toFixed(2)}</Text>
                </View>
              </View>
            ) : (
              <View style={styles.refSection}>
                <Text style={styles.sectionLabel}>{paymentMethod.toUpperCase()} Reference Number</Text>
                <TextInput
                  style={styles.refInput}
                  placeholder="Enter reference ID..."
                  placeholderTextColor="#94a3b8"
                  value={referenceNo}
                  onChangeText={setReferenceNo}
                />
              </View>
            )}
          </ScrollView>

          <View style={styles.footer}>
            <TouchableOpacity
              style={[styles.confirmBtn, submitting && styles.confirmBtnDisabled]}
              onPress={handleConfirmSale}
              disabled={submitting}
            >
              {submitting ? (
                <ActivityIndicator color="#ffffff" />
              ) : (
                <Text style={styles.confirmBtnText}>COMPLETE SALE ➔</Text>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  backdrop: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.65)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 16,
  },
  card: {
    width: '100%',
    maxWidth: 480,
    backgroundColor: '#ffffff',
    borderRadius: 24,
    maxHeight: '90%',
    padding: 20,
  },
  tabletCard: {
    maxWidth: 540,
    padding: 24,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 16,
    paddingBottom: 12,
    borderBottomWidth: 1,
    borderColor: '#f1f5f9',
  },
  headerLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  headerIcon: {
    width: 42,
    height: 42,
    borderRadius: 12,
    backgroundColor: '#059669',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerIconText: {
    fontSize: 20,
  },
  title: {
    fontSize: 18,
    fontWeight: '800',
    color: '#0f172a',
  },
  subtitle: {
    fontSize: 12,
    color: '#64748b',
    marginTop: 2,
  },
  closeBtn: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#f1f5f9',
    alignItems: 'center',
    justifyContent: 'center',
  },
  closeBtnText: {
    color: '#64748b',
    fontWeight: '800',
  },
  scrollBody: {
    maxHeight: 460,
  },
  totalBox: {
    backgroundColor: '#0f172a',
    borderRadius: 16,
    padding: 18,
    alignItems: 'center',
    marginBottom: 16,
  },
  totalLabel: {
    color: '#94a3b8',
    fontSize: 11,
    fontWeight: '800',
  },
  totalAmount: {
    color: '#34d399',
    fontSize: 32,
    fontWeight: '900',
    marginTop: 4,
  },
  sectionLabel: {
    fontSize: 12,
    fontWeight: '800',
    color: '#475569',
    textTransform: 'uppercase',
    marginBottom: 8,
    marginTop: 8,
  },
  methodGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
    marginBottom: 14,
  },
  methodChip: {
    flex: 1,
    minWidth: '45%',
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    paddingVertical: 12,
    alignItems: 'center',
  },
  methodChipText: {
    fontSize: 13,
    fontWeight: '700',
    color: '#334155',
  },
  methodChipTextActive: {
    color: '#ffffff',
  },
  tenderSection: {
    marginTop: 6,
  },
  tenderInput: {
    backgroundColor: '#f8fafc',
    borderWidth: 1.5,
    borderColor: '#059669',
    borderRadius: 14,
    fontSize: 24,
    fontWeight: '800',
    color: '#0f172a',
    textAlign: 'center',
    paddingVertical: 10,
    marginBottom: 12,
  },
  quickGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 6,
    marginBottom: 14,
  },
  quickBtn: {
    backgroundColor: '#f1f5f9',
    borderRadius: 10,
    paddingHorizontal: 12,
    paddingVertical: 10,
    minWidth: '22%',
    alignItems: 'center',
  },
  quickBtnText: {
    fontSize: 13,
    fontWeight: '800',
    color: '#0f172a',
  },
  changeBox: {
    backgroundColor: '#f0fdf4',
    borderWidth: 1,
    borderColor: '#bbf7d0',
    borderRadius: 14,
    padding: 14,
    alignItems: 'center',
  },
  changeLabel: {
    fontSize: 11,
    fontWeight: '800',
    color: '#166534',
  },
  changeAmount: {
    fontSize: 22,
    fontWeight: '900',
    color: '#059669',
    marginTop: 2,
  },
  refSection: {
    marginTop: 10,
  },
  refInput: {
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#cbd5e1',
    borderRadius: 12,
    padding: 14,
    fontSize: 15,
    color: '#0f172a',
  },
  footer: {
    marginTop: 16,
    paddingTop: 12,
    borderTopWidth: 1,
    borderColor: '#f1f5f9',
  },
  confirmBtn: {
    backgroundColor: '#059669',
    borderRadius: 14,
    paddingVertical: 16,
    alignItems: 'center',
  },
  confirmBtnDisabled: {
    opacity: 0.7,
  },
  confirmBtnText: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: '900',
  },
});
