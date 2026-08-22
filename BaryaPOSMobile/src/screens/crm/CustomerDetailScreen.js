import React, { useState, useEffect } from 'react';
import {
  StyleSheet,
  View,
  Text,
  TouchableOpacity,
  ScrollView,
  TextInput,
  ActivityIndicator,
  Alert,
  SafeAreaView,
} from 'react-native';
import posApi from '../../services/api';

export default function CustomerDetailScreen({ route, navigation }) {
  const { customerId } = route.params || {};
  const [customer, setCustomer] = useState(null);
  const [loading, setLoading] = useState(true);

  // Edit Credit Limit modal/form state
  const [editingLimit, setEditingLimit] = useState(false);
  const [newLimit, setNewLimit] = useState('');
  const [updating, setUpdating] = useState(false);

  // Pay Utang collection state
  const [payAmount, setPayAmount] = useState('');
  const [payMethod, setPayMethod] = useState('cash');
  const [paying, setPaying] = useState(false);

  useEffect(() => {
    loadCustomer();
  }, [customerId]);

  const loadCustomer = async () => {
    setLoading(true);
    try {
      const res = await posApi.getCustomerDetails(customerId);
      if (res.success && res.data) {
        setCustomer(res.data);
        setNewLimit(String(res.data.credit_limit || 5000));
      }
    } catch (err) {
      console.error('Load customer error:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleUpdateCreditLimit = async () => {
    if (!newLimit || isNaN(newLimit)) {
      Alert.alert('Invalid Input', 'Please enter a valid numeric credit limit.');
      return;
    }

    setUpdating(true);
    try {
      const res = await posApi.updateCreditLimit(customerId, Number(newLimit));
      if (res.success) {
        Alert.alert('Success', 'Customer credit limit updated!');
        setEditingLimit(false);
        loadCustomer();
      }
    } catch (err) {
      Alert.alert('Error', 'Failed to update credit limit.');
    } finally {
      setUpdating(false);
    }
  };

  const handlePayUtang = async () => {
    const amountVal = parseFloat(payAmount);
    if (!amountVal || amountVal <= 0) {
      Alert.alert('Invalid Amount', 'Please enter payment amount.');
      return;
    }

    setPaying(true);
    try {
      const res = await posApi.payUtangCollection({
        customer_id: customerId,
        amount: amountVal,
        payment_method: payMethod,
      });

      if (res.success) {
        Alert.alert('Payment Received 🎉', `₱${amountVal.toFixed(2)} recorded to Suki ledger!`);
        setPayAmount('');
        loadCustomer();
      }
    } catch (err) {
      Alert.alert('Error', 'Could not record collection payment.');
    } finally {
      setPaying(false);
    }
  };

  if (loading || !customer) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#059669" />
        <Text style={styles.loadingText}>Fetching customer profile & ledger...</Text>
      </View>
    );
  }

  const name = customer.CustomerName || customer.name || 'Unnamed Suki';
  const balance = Number(customer.credit?.running_balance || customer.running_balance || 0);
  const limit = Number(customer.credit_limit || 5000);

  return (
    <SafeAreaView style={styles.container}>
      {/* Top Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
          <Text style={styles.backBtnText}>◀ Directory</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Suki Ledger & Profile</Text>
        <View style={{ width: 60 }} />
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Profile Card */}
        <View style={styles.profileCard}>
          <View style={styles.avatarCircle}>
            <Text style={styles.avatarText}>{name.charAt(0).toUpperCase()}</Text>
          </View>

          <Text style={styles.custName}>{name}</Text>
          <Text style={styles.custPhone}>📱 {customer.mobile_number || customer.phone || 'No phone'}</Text>
          <Text style={styles.custAddress}>📍 {customer.address || 'Standard Retail Account'}</Text>

          {/* Balance Banner */}
          <View style={styles.balanceBox}>
            <Text style={styles.balanceLabel}>CURRENT UTANG BALANCE</Text>
            <Text style={[styles.balanceVal, balance > 0 ? styles.textDebt : styles.textClean]}>
              ₱{balance.toFixed(2)}
            </Text>

            <View style={styles.limitRow}>
              <Text style={styles.limitText}>Credit Limit: ₱{limit.toFixed(2)}</Text>
              <TouchableOpacity onPress={() => setEditingLimit(!editingLimit)}>
                <Text style={styles.editLimitBtn}>[Edit Limit]</Text>
              </TouchableOpacity>
            </View>

            {editingLimit && (
              <View style={styles.editLimitBox}>
                <TextInput
                  style={styles.limitInput}
                  value={newLimit}
                  onChangeText={setNewLimit}
                  keyboardType="numeric"
                  placeholder="Enter new credit limit"
                />
                <TouchableOpacity
                  style={styles.saveLimitBtn}
                  onPress={handleUpdateCreditLimit}
                  disabled={updating}
                >
                  <Text style={styles.saveLimitText}>Save</Text>
                </TouchableOpacity>
              </View>
            )}
          </View>
        </View>

        {/* Receive / Pay Utang Card */}
        {balance > 0 && (
          <View style={styles.payCard}>
            <Text style={styles.payTitle}>💵 Receive / Settle Utang Payment</Text>
            <TextInput
              style={styles.payInput}
              placeholder="Enter amount (₱)"
              placeholderTextColor="#94a3b8"
              value={payAmount}
              onChangeText={setPayAmount}
              keyboardType="numeric"
            />

            <TouchableOpacity
              style={styles.paySubmitBtn}
              onPress={handlePayUtang}
              disabled={paying}
            >
              {paying ? (
                <ActivityIndicator color="#ffffff" />
              ) : (
                <Text style={styles.paySubmitText}>RECORD UTANG PAYMENT ➔</Text>
              )}
            </TouchableOpacity>
          </View>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0f172a',
  },
  header: {
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
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#0f172a',
  },
  loadingText: {
    marginTop: 10,
    color: '#94a3b8',
  },
  scrollContent: {
    padding: 16,
  },
  profileCard: {
    backgroundColor: '#ffffff',
    borderRadius: 20,
    padding: 20,
    alignItems: 'center',
    marginBottom: 16,
  },
  avatarCircle: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: '#059669',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 12,
  },
  avatarText: {
    color: '#ffffff',
    fontWeight: '900',
    fontSize: 26,
  },
  custName: {
    fontSize: 20,
    fontWeight: '900',
    color: '#0f172a',
  },
  custPhone: {
    fontSize: 13,
    color: '#64748b',
    marginTop: 4,
  },
  custAddress: {
    fontSize: 12,
    color: '#94a3b8',
    marginTop: 2,
  },
  balanceBox: {
    width: '100%',
    backgroundColor: '#f8fafc',
    borderRadius: 16,
    padding: 16,
    alignItems: 'center',
    marginTop: 16,
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  balanceLabel: {
    fontSize: 11,
    fontWeight: '800',
    color: '#64748b',
    letterSpacing: 0.5,
  },
  balanceVal: {
    fontSize: 28,
    fontWeight: '900',
    marginTop: 4,
  },
  textDebt: {
    color: '#dc2626',
  },
  textClean: {
    color: '#059669',
  },
  limitRow: {
    flexDirection: 'row',
    gap: 8,
    marginTop: 6,
  },
  limitText: {
    fontSize: 12,
    color: '#475569',
  },
  editLimitBtn: {
    fontSize: 12,
    fontWeight: '800',
    color: '#0284c7',
  },
  editLimitBox: {
    flexDirection: 'row',
    gap: 8,
    marginTop: 10,
    width: '100%',
  },
  limitInput: {
    flex: 1,
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: '#cbd5e1',
    borderRadius: 10,
    paddingHorizontal: 12,
    paddingVertical: 8,
    fontSize: 14,
  },
  saveLimitBtn: {
    backgroundColor: '#0284c7',
    borderRadius: 10,
    paddingHorizontal: 16,
    justifyContent: 'center',
  },
  saveLimitText: {
    color: '#ffffff',
    fontWeight: '800',
  },
  payCard: {
    backgroundColor: '#f0fdf4',
    borderWidth: 1,
    borderColor: '#bbf7d0',
    borderRadius: 20,
    padding: 18,
  },
  payTitle: {
    fontSize: 15,
    fontWeight: '800',
    color: '#166534',
    marginBottom: 12,
  },
  payInput: {
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: '#86efac',
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 12,
    fontSize: 16,
    color: '#0f172a',
    marginBottom: 12,
  },
  paySubmitBtn: {
    backgroundColor: '#059669',
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
  },
  paySubmitText: {
    color: '#ffffff',
    fontWeight: '800',
    fontSize: 14,
  },
});
