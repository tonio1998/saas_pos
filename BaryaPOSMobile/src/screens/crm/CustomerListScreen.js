import React, { useState, useEffect } from 'react';
import {
  StyleSheet,
  View,
  Text,
  TextInput,
  TouchableOpacity,
  FlatList,
  ActivityIndicator,
  SafeAreaView,
  StatusBar,
} from 'react-native';
import posApi from '../../services/api';
import useResponsive from '../../hooks/useResponsive';

export default function CustomerListScreen({ navigation }) {
  const { isTablet } = useResponsive();
  const [search, setSearch] = useState('');
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetchCustomers('');
  }, []);

  const fetchCustomers = async (keyword) => {
    setLoading(true);
    try {
      const res = await posApi.getCustomers({ search: keyword });
      if (res.success && Array.isArray(res.data)) {
        setCustomers(res.data);
      } else {
        setCustomers([]);
      }
    } catch (err) {
      console.error('Fetch customers error:', err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#0f172a" />

      {/* Top Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
          <Text style={styles.backBtnText}>◀ Terminal</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Customer CRM & Suki Directory</Text>
        <TouchableOpacity
          onPress={() => fetchCustomers(search)}
          style={styles.refreshBtn}
        >
          <Text style={styles.refreshBtnText}>🔄 Refresh</Text>
        </TouchableOpacity>
      </View>

      {/* Search & Filter Bar */}
      <View style={styles.searchBarBox}>
        <TextInput
          style={styles.searchInput}
          placeholder="Search Suki customer name, mobile, or TIN..."
          placeholderTextColor="#94a3b8"
          value={search}
          onChangeText={(text) => {
            setSearch(text);
            fetchCustomers(text);
          }}
        />
      </View>

      {/* Customer List */}
      {loading ? (
        <View style={styles.loadingBox}>
          <ActivityIndicator size="large" color="#059669" />
          <Text style={styles.loadingText}>Loading Suki directory...</Text>
        </View>
      ) : (
        <FlatList
          data={customers}
          keyExtractor={(item) => String(item.id)}
          contentContainerStyle={styles.listContainer}
          renderItem={({ item }) => {
            const name = item.CustomerName || item.name || 'Unnamed Customer';
            const phone = item.mobile_number || item.phone || 'No phone';
            const balance = Number(item.credit?.running_balance || item.running_balance || 0);
            const limit = Number(item.credit_limit || 5000);
            const tier = item.customer_type || 'Regular Suki';

            return (
              <TouchableOpacity
                style={styles.card}
                onPress={() => navigation.navigate('CustomerDetail', { customerId: item.id })}
                activeOpacity={0.7}
              >
                <View style={styles.avatarCircle}>
                  <Text style={styles.avatarText}>{name.charAt(0).toUpperCase()}</Text>
                </View>

                <View style={styles.custInfo}>
                  <View style={styles.titleRow}>
                    <Text style={styles.custName}>{name}</Text>
                    <View style={styles.tierBadge}>
                      <Text style={styles.tierText}>{tier}</Text>
                    </View>
                  </View>
                  <Text style={styles.custPhone}>📱 {phone}</Text>
                </View>

                {/* Utang Balance Indicator */}
                <View style={styles.creditBox}>
                  <Text style={styles.creditLabel}>Utang Balance</Text>
                  <Text style={[styles.creditVal, balance > 0 ? styles.textDebt : styles.textClean]}>
                    ₱{balance.toFixed(2)}
                  </Text>
                  <Text style={styles.creditLimit}>Limit: ₱{limit.toFixed(2)}</Text>
                </View>

                <Text style={styles.arrowText}>➔</Text>
              </TouchableOpacity>
            );
          }}
        />
      )}
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
  refreshBtnText: {
    color: '#34d399',
    fontWeight: '700',
    fontSize: 12,
  },
  searchBarBox: {
    padding: 12,
    backgroundColor: '#1e293b',
  },
  searchInput: {
    backgroundColor: '#0f172a',
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 10,
    fontSize: 14,
    color: '#ffffff',
    borderWidth: 1,
    borderColor: '#334155',
  },
  listContainer: {
    padding: 12,
    backgroundColor: '#f8fafc',
    flexGrow: 1,
  },
  loadingBox: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f8fafc',
  },
  loadingText: {
    marginTop: 10,
    color: '#64748b',
    fontSize: 13,
  },
  card: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 14,
    marginBottom: 10,
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  avatarCircle: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: '#059669',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  avatarText: {
    color: '#ffffff',
    fontWeight: '900',
    fontSize: 18,
  },
  custInfo: {
    flex: 1,
  },
  titleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  custName: {
    fontSize: 15,
    fontWeight: '800',
    color: '#0f172a',
  },
  tierBadge: {
    backgroundColor: '#e0f2fe',
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 10,
  },
  tierText: {
    fontSize: 10,
    fontWeight: '800',
    color: '#0369a1',
  },
  custPhone: {
    fontSize: 12,
    color: '#64748b',
    marginTop: 3,
  },
  creditBox: {
    alignItems: 'flex-end',
    marginRight: 10,
  },
  creditLabel: {
    fontSize: 10,
    fontWeight: '700',
    color: '#64748b',
  },
  creditVal: {
    fontSize: 14,
    fontWeight: '900',
    marginTop: 1,
  },
  creditLimit: {
    fontSize: 9,
    color: '#94a3b8',
    marginTop: 1,
  },
  textDebt: {
    color: '#dc2626',
  },
  textClean: {
    color: '#059669',
  },
  arrowText: {
    color: '#cbd5e1',
    fontWeight: '800',
  },
});
