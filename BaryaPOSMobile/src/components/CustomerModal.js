import React, { useState, useEffect } from 'react';
import {
  StyleSheet,
  View,
  Text,
  Modal,
  TouchableOpacity,
  TextInput,
  FlatList,
  Alert,
} from 'react-native';
import posApi from '../services/api';

export default function CustomerModal({ visible, selectedCustomer, onClose, onSelectCustomer }) {
  const [search, setSearch] = useState('');
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (visible) {
      fetchCustomers('');
    }
  }, [visible]);

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
    <Modal
      visible={visible}
      transparent
      animationType="slide"
      onRequestClose={onClose}
    >
      <View style={styles.backdrop}>
        <View style={styles.card}>
          <View style={styles.header}>
            <Text style={styles.title}>👤 Select Customer / Suki Profile</Text>
            <TouchableOpacity onPress={onClose} style={styles.closeBtn}>
              <Text style={styles.closeBtnText}>✕</Text>
            </TouchableOpacity>
          </View>

          {/* Walk-in Customer Option */}
          <TouchableOpacity
            style={[styles.walkInBtn, !selectedCustomer && styles.walkInBtnActive]}
            onPress={() => {
              onSelectCustomer(null);
              onClose();
            }}
          >
            <Text style={styles.walkInText}>👤 Walk-in Customer (Standard Retail)</Text>
          </TouchableOpacity>

          {/* Search Bar */}
          <TextInput
            style={styles.searchInput}
            placeholder="Search Suki customer name or phone..."
            placeholderTextColor="#94a3b8"
            value={search}
            onChangeText={(text) => {
              setSearch(text);
              fetchCustomers(text);
            }}
          />

          {/* Customers List */}
          <FlatList
            data={customers}
            keyExtractor={(item) => String(item.id)}
            style={styles.list}
            renderItem={({ item }) => {
              const isSelected = selectedCustomer && selectedCustomer.id === item.id;
              const name = item.CustomerName || item.name || 'Unnamed Customer';

              return (
                <TouchableOpacity
                  style={[styles.customerItem, isSelected && styles.customerItemSelected]}
                  onPress={() => {
                    onSelectCustomer({
                      id: item.id,
                      name: name,
                      phone: item.mobile_number || item.phone || '',
                      credit_balance: item.credit?.running_balance || 0,
                    });
                    onClose();
                  }}
                >
                  <View style={styles.avatarCircle}>
                    <Text style={styles.avatarText}>{name.charAt(0).toUpperCase()}</Text>
                  </View>
                  <View style={styles.custInfo}>
                    <Text style={styles.custName}>{name}</Text>
                    <Text style={styles.custPhone}>
                      {item.mobile_number || item.phone || 'No contact number'}
                    </Text>
                  </View>

                  {isSelected && (
                    <View style={styles.checkBadge}>
                      <Text style={styles.checkBadgeText}>Selected ✓</Text>
                    </View>
                  )}
                </TouchableOpacity>
              );
            }}
          />
        </View>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  backdrop: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.65)',
    justifyContent: 'flex-end',
  },
  card: {
    width: '100%',
    maxHeight: '85%',
    backgroundColor: '#ffffff',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    padding: 20,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 14,
  },
  title: {
    fontSize: 17,
    fontWeight: '800',
    color: '#0f172a',
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
  walkInBtn: {
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    padding: 12,
    marginBottom: 12,
  },
  walkInBtnActive: {
    backgroundColor: '#f0fdf4',
    borderColor: '#86efac',
  },
  walkInText: {
    fontSize: 13,
    fontWeight: '700',
    color: '#059669',
  },
  searchInput: {
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#cbd5e1',
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 10,
    fontSize: 14,
    color: '#0f172a',
    marginBottom: 12,
  },
  list: {
    maxHeight: 380,
  },
  customerItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderRadius: 14,
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: '#f1f5f9',
    marginBottom: 8,
  },
  customerItemSelected: {
    backgroundColor: '#ecfdf5',
    borderColor: '#a7f3d0',
  },
  avatarCircle: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: '#059669',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  avatarText: {
    color: '#ffffff',
    fontWeight: '800',
    fontSize: 16,
  },
  custInfo: {
    flex: 1,
  },
  custName: {
    fontSize: 14,
    fontWeight: '700',
    color: '#0f172a',
  },
  custPhone: {
    fontSize: 11,
    color: '#64748b',
    marginTop: 2,
  },
  checkBadge: {
    backgroundColor: '#059669',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  checkBadgeText: {
    color: '#ffffff',
    fontSize: 11,
    fontWeight: '800',
  },
});
