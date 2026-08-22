import React, { useState, useEffect } from 'react';
import {
  StyleSheet,
  View,
  Text,
  TextInput,
  TouchableOpacity,
  FlatList,
  Image,
  ActivityIndicator,
  Alert,
  SafeAreaView,
  StatusBar,
} from 'react-native';
import posApi from '../../services/api';
import storage from '../../services/storage';
import useResponsive from '../../hooks/useResponsive';
import PaymentModal from '../../components/PaymentModal';
import VariantSelectModal from '../../components/VariantSelectModal';
import CustomerModal from '../../components/CustomerModal';

export default function POSTerminalScreen({ navigation }) {
  const { isTablet, numColumns } = useResponsive();

  const [products, setProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);
  const [loadingProducts, setLoadingProducts] = useState(false);
  const [search, setSearch] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('');
  const [categories, setCategories] = useState([]);

  const [viewMode, setViewMode] = useState('table');
  const [priceMode, setPriceMode] = useState('retail');

  const [cart, setCart] = useState([]);
  const [customer, setCustomer] = useState(null);
  const [mobileActiveTab, setMobileActiveTab] = useState('catalog');

  const [showPaymentModal, setShowPaymentModal] = useState(false);
  const [showCustomerModal, setShowCustomerModal] = useState(false);
  const [selectedProductForVariant, setSelectedProductForVariant] = useState(null);

  useEffect(() => {
    loadProducts();
    loadSavedCart();
  }, []);

  const loadProducts = async () => {
    setLoadingProducts(true);
    try {
      const res = await posApi.getProducts();
      if (res.success && Array.isArray(res.data)) {
        setProducts(res.data);
        setFilteredProducts(res.data);

        const catMap = {};
        res.data.forEach(p => {
          if (p.category && p.category.name) catMap[p.category_id] = p.category.name;
        });
        const catList = Object.keys(catMap).map(id => ({ id, name: catMap[id] }));
        setCategories(catList);
      }
    } catch (err) {
      console.error('Load products error:', err);
    } finally {
      setLoadingProducts(false);
    }
  };

  const loadSavedCart = async () => {
    const saved = await storage.getCart();
    if (Array.isArray(saved)) setCart(saved);
  };

  const filterCatalog = (keyword, categoryId) => {
    let list = [...products];
    if (categoryId) {
      list = list.filter(p => String(p.category_id) === String(categoryId));
    }
    if (keyword.trim()) {
      const query = keyword.toLowerCase().trim();
      list = list.filter(p =>
        (p.name && p.name.toLowerCase().includes(query)) ||
        (p.barcode && p.barcode.toLowerCase().includes(query))
      );
    }
    setFilteredProducts(list);
  };

  const handleProductPress = (product) => {
    if (product.variants && Array.isArray(product.variants) && product.variants.length > 0) {
      setSelectedProductForVariant(product);
      return;
    }

    const isWholesale = priceMode === 'wholesale';
    const retail = Number(product.selling_price || 0);
    const wholesale = Number(product.wholesale_price || 0);
    const activePrice = (isWholesale && wholesale > 0) ? wholesale : retail;

    addToCart({
      id: product.id,
      variant_id: null,
      name: product.name,
      unit: product.unit?.name || 'unit',
      barcode: product.barcode,
      price: activePrice,
      stock: Number(product.stock_on_hand || 0),
    });
  };

  const addToCart = (item) => {
    setCart(prev => {
      const key = `${item.id}_${item.variant_id || 'base'}`;
      const existingIndex = prev.findIndex(i => `${i.id}_${i.variant_id || 'base'}` === key);

      let updated = [...prev];
      if (existingIndex > -1) {
        updated[existingIndex].qty += 1;
      } else {
        updated.push({ ...item, qty: 1 });
      }

      storage.saveCart(updated);
      return updated;
    });
  };

  const updateQty = (index, delta) => {
    setCart(prev => {
      let updated = [...prev];
      const newQty = updated[index].qty + delta;
      if (newQty <= 0) {
        updated.splice(index, 1);
      } else {
        updated[index].qty = newQty;
      }
      storage.saveCart(updated);
      return updated;
    });
  };

  const cartTotal = cart.reduce((sum, item) => sum + (item.qty * item.price), 0);
  const cartItemCount = cart.reduce((sum, item) => sum + item.qty, 0);

  const handleLogout = async () => {
    Alert.alert('Logout', 'Log out of BaryaPOS Terminal?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Logout',
        style: 'destructive',
        onPress: async () => {
          await storage.logout();
          navigation.replace('Login');
        }
      }
    ]);
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#0f172a" />

      {/* Top Header */}
      <View style={styles.topHeader}>
        <View style={styles.headerLeft}>
          <TouchableOpacity onPress={handleLogout} style={styles.headerLogoBox}>
            <Text style={styles.headerLogo}>₱</Text>
          </TouchableOpacity>
          <View>
            <Text style={styles.headerTitle}>BaryaPOS</Text>
            <Text style={styles.headerSubtitle}>Enterprise Mobile & Tablet</Text>
          </View>
        </View>

        <TouchableOpacity onPress={() => navigation.navigate('CustomerList')} style={styles.crmNavBtn}>
          <Text style={styles.crmNavText}>👥 Suki CRM</Text>
        </TouchableOpacity>

        {/* Pricing Mode Switcher */}
        <View style={styles.priceToggleBox}>
          <TouchableOpacity
            style={[styles.priceToggleBtn, priceMode === 'retail' && styles.priceToggleActiveRt]}
            onPress={() => setPriceMode('retail')}
          >
            <Text style={[styles.priceToggleText, priceMode === 'retail' && styles.priceTextActive]}>Retail</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.priceToggleBtn, priceMode === 'wholesale' && styles.priceToggleActiveWs]}
            onPress={() => setPriceMode('wholesale')}
          >
            <Text style={[styles.priceToggleText, priceMode === 'wholesale' && styles.priceTextActive]}>Wholesale</Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* Main Responsive Shell */}
      <View style={styles.shell}>
        {(isTablet || mobileActiveTab === 'catalog') && (
          <View style={[styles.leftPane, !isTablet && styles.fullPane]}>
            {/* Search & Mode Bar */}
            <View style={styles.searchBarBox}>
              <TextInput
                style={styles.searchInput}
                placeholder="🔍 Search product or barcode..."
                placeholderTextColor="#94a3b8"
                value={search}
                onChangeText={(text) => {
                  setSearch(text);
                  filterCatalog(text, selectedCategory);
                }}
              />

              <View style={styles.viewModeBox}>
                <TouchableOpacity
                  style={[styles.viewModeBtn, viewMode === 'table' && styles.viewModeActive]}
                  onPress={() => setViewMode('table')}
                >
                  <Text style={styles.viewModeText}>≡ Table</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={[styles.viewModeBtn, viewMode === 'grid' && styles.viewModeActive]}
                  onPress={() => setViewMode('grid')}
                >
                  <Text style={styles.viewModeText}>⊞ Grid</Text>
                </TouchableOpacity>
              </View>
            </View>

            {/* Catalog List */}
            {loadingProducts ? (
              <View style={styles.loadingBox}>
                <ActivityIndicator size="large" color="#059669" />
                <Text style={styles.loadingText}>Syncing DB catalog...</Text>
              </View>
            ) : viewMode === 'table' ? (
              <FlatList
                data={filteredProducts}
                keyExtractor={(item) => String(item.id)}
                contentContainerStyle={styles.tableListContainer}
                renderItem={({ item }) => {
                  const isWholesale = priceMode === 'wholesale';
                  const retail = Number(item.selling_price || 0);
                  const wholesale = Number(item.wholesale_price || 0);
                  const activePrice = (isWholesale && wholesale > 0) ? wholesale : retail;
                  const stock = Number(item.stock_on_hand || 0);

                  return (
                    <TouchableOpacity
                      style={styles.tableRow}
                      onPress={() => handleProductPress(item)}
                    >
                      <View style={styles.tableInfo}>
                        <Text style={styles.tableName} numberOfLines={1}>{item.name}</Text>
                        <Text style={styles.tableStock}>Stock: {stock}</Text>
                      </View>
                      <View style={styles.tableAction}>
                        <Text style={styles.tablePrice}>₱{activePrice.toFixed(2)}</Text>
                        <View style={styles.addBtnSmall}>
                          <Text style={styles.addBtnSmallText}>+ ADD</Text>
                        </View>
                      </View>
                    </TouchableOpacity>
                  );
                }}
              />
            ) : (
              <FlatList
                data={filteredProducts}
                keyExtractor={(item) => String(item.id)}
                numColumns={numColumns}
                key={numColumns}
                contentContainerStyle={styles.gridContainer}
                renderItem={({ item }) => {
                  const isWholesale = priceMode === 'wholesale';
                  const retail = Number(item.selling_price || 0);
                  const wholesale = Number(item.wholesale_price || 0);
                  const activePrice = (isWholesale && wholesale > 0) ? wholesale : retail;

                  return (
                    <TouchableOpacity style={styles.gridCard} onPress={() => handleProductPress(item)}>
                      <Text style={styles.gridName} numberOfLines={2}>{item.name}</Text>
                      <Text style={styles.gridPrice}>₱{activePrice.toFixed(2)}</Text>
                    </TouchableOpacity>
                  );
                }}
              />
            )}
          </View>
        )}

        {(isTablet || mobileActiveTab === 'cart') && (
          <View style={[styles.rightPane, !isTablet && styles.fullPane]}>
            <TouchableOpacity style={styles.customerBar} onPress={() => setShowCustomerModal(true)}>
              <Text style={styles.customerName}>👤 {customer ? customer.name : 'Walk-in Customer'}</Text>
              <Text style={styles.customerArrow}>➔</Text>
            </TouchableOpacity>

            <FlatList
              data={cart}
              keyExtractor={(item, idx) => `${item.id}_${item.variant_id}_${idx}`}
              renderItem={({ item, index }) => (
                <View style={styles.cartItemRow}>
                  <View style={styles.cartItemLeft}>
                    <Text style={styles.cartItemName}>{item.name}</Text>
                    <Text style={styles.cartItemUnitPrice}>₱{item.price.toFixed(2)}</Text>
                  </View>
                  <View style={styles.stepperBox}>
                    <TouchableOpacity style={styles.stepBtn} onPress={() => updateQty(index, -1)}>
                      <Text style={styles.stepBtnText}>-</Text>
                    </TouchableOpacity>
                    <Text style={styles.stepQty}>{item.qty}</Text>
                    <TouchableOpacity style={styles.stepBtn} onPress={() => updateQty(index, 1)}>
                      <Text style={styles.stepBtnText}>+</Text>
                    </TouchableOpacity>
                  </View>
                  <Text style={styles.cartItemSubtotal}>₱{(item.qty * item.price).toFixed(2)}</Text>
                </View>
              )}
            />

            <View style={styles.cartBottomBox}>
              <View style={styles.summaryTotalRow}>
                <Text style={styles.summaryTotalLabel}>TOTAL DUE</Text>
                <Text style={styles.summaryTotalVal}>₱{cartTotal.toFixed(2)}</Text>
              </View>
              <TouchableOpacity
                style={[styles.checkoutBtn, cart.length === 0 && styles.checkoutBtnDisabled]}
                onPress={() => cart.length > 0 && setShowPaymentModal(true)}
                disabled={cart.length === 0}
              >
                <Text style={styles.checkoutBtnText}>CHECKOUT ➔</Text>
              </TouchableOpacity>
            </View>
          </View>
        )}
      </View>

      {!isTablet && (
        <View style={styles.phoneBottomNav}>
          <TouchableOpacity
            style={[styles.phoneTabBtn, mobileActiveTab === 'catalog' && styles.phoneTabActive]}
            onPress={() => setMobileActiveTab('catalog')}
          >
            <Text style={styles.phoneTabText}>📦 Catalog</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.phoneTabBtn, mobileActiveTab === 'cart' && styles.phoneTabActive]}
            onPress={() => setMobileActiveTab('cart')}
          >
            <Text style={styles.phoneTabText}>🛒 Cart ({cartItemCount}) • ₱{cartTotal.toFixed(2)}</Text>
          </TouchableOpacity>
        </View>
      )}

      <PaymentModal
        visible={showPaymentModal}
        totalAmount={cartTotal}
        cartItems={cart}
        customer={customer}
        priceMode={priceMode}
        onClose={() => setShowPaymentModal(false)}
        onSaleComplete={(res) => {
          setShowPaymentModal(false);
          setCart([]);
          storage.clearCart();
          navigation.navigate('Receipt', { sale: res.sale || res });
        }}
      />

      <VariantSelectModal
        visible={!!selectedProductForVariant}
        product={selectedProductForVariant}
        priceMode={priceMode}
        onClose={() => setSelectedProductForVariant(null)}
        onSelectVariant={(variantItem) => {
          setSelectedProductForVariant(null);
          addToCart(variantItem);
        }}
      />

      <CustomerModal
        visible={showCustomerModal}
        selectedCustomer={customer}
        onClose={() => setShowCustomerModal(false)}
        onSelectCustomer={setCustomer}
      />
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
    paddingHorizontal: 14,
    borderBottomWidth: 1,
    borderColor: '#1e293b',
  },
  headerLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  headerLogoBox: {
    width: 32,
    height: 32,
    borderRadius: 8,
    backgroundColor: '#059669',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerLogo: {
    color: '#ffffff',
    fontSize: 16,
    fontWeight: '900',
  },
  headerTitle: {
    color: '#ffffff',
    fontSize: 15,
    fontWeight: '800',
  },
  headerSubtitle: {
    color: '#94a3b8',
    fontSize: 10,
  },
  crmNavBtn: {
    backgroundColor: '#0369a1',
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: 12,
  },
  crmNavText: {
    color: '#ffffff',
    fontWeight: '800',
    fontSize: 11,
  },
  priceToggleBox: {
    flexDirection: 'row',
    backgroundColor: '#1e293b',
    borderRadius: 16,
    padding: 2,
  },
  priceToggleBtn: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  priceToggleActiveRt: {
    backgroundColor: '#059669',
  },
  priceToggleActiveWs: {
    backgroundColor: '#0284c7',
  },
  priceToggleText: {
    fontSize: 11,
    fontWeight: '700',
    color: '#94a3b8',
  },
  priceTextActive: {
    color: '#ffffff',
  },
  shell: {
    flex: 1,
    flexDirection: 'row',
    backgroundColor: '#f8fafc',
  },
  leftPane: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderRightWidth: 1,
    borderColor: '#e2e8f0',
  },
  rightPane: {
    width: 360,
    backgroundColor: '#ffffff',
  },
  fullPane: {
    width: '100%',
  },
  searchBarBox: {
    padding: 10,
    flexDirection: 'row',
    gap: 8,
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderColor: '#e2e8f0',
  },
  searchInput: {
    flex: 1,
    backgroundColor: '#f1f5f9',
    borderRadius: 10,
    paddingHorizontal: 12,
    paddingVertical: 8,
    fontSize: 13,
    color: '#0f172a',
  },
  viewModeBox: {
    flexDirection: 'row',
    backgroundColor: '#f1f5f9',
    borderRadius: 10,
    padding: 2,
  },
  viewModeBtn: {
    paddingHorizontal: 8,
    paddingVertical: 6,
    borderRadius: 8,
  },
  viewModeActive: {
    backgroundColor: '#ffffff',
  },
  viewModeText: {
    fontSize: 11,
    fontWeight: '700',
    color: '#334155',
  },
  tableListContainer: {
    padding: 10,
  },
  tableRow: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    borderRadius: 12,
    padding: 12,
    marginBottom: 8,
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  tableInfo: {
    flex: 1,
  },
  tableName: {
    fontSize: 13,
    fontWeight: '700',
    color: '#0f172a',
  },
  tableStock: {
    fontSize: 10,
    color: '#64748b',
    marginTop: 2,
  },
  tableAction: {
    alignItems: 'flex-end',
  },
  tablePrice: {
    fontSize: 14,
    fontWeight: '900',
    color: '#059669',
  },
  addBtnSmall: {
    backgroundColor: '#059669',
    borderRadius: 6,
    paddingHorizontal: 8,
    paddingVertical: 3,
    marginTop: 4,
  },
  addBtnSmallText: {
    color: '#ffffff',
    fontSize: 10,
    fontWeight: '900',
  },
  gridContainer: {
    padding: 6,
  },
  gridCard: {
    flex: 1,
    margin: 4,
    backgroundColor: '#ffffff',
    borderRadius: 12,
    padding: 12,
    borderWidth: 1,
    borderColor: '#e2e8f0',
  },
  gridName: {
    fontSize: 12,
    fontWeight: '700',
    color: '#0f172a',
    height: 32,
  },
  gridPrice: {
    fontSize: 14,
    fontWeight: '900',
    color: '#059669',
    marginTop: 6,
  },
  loadingBox: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 10,
    color: '#64748b',
  },
  customerBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#f0fdf4',
    padding: 12,
    borderBottomWidth: 1,
    borderColor: '#bbf7d0',
  },
  customerName: {
    fontSize: 13,
    fontWeight: '800',
    color: '#166534',
  },
  customerArrow: {
    color: '#166534',
  },
  cartItemRow: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderBottomWidth: 1,
    borderColor: '#f8fafc',
  },
  cartItemLeft: {
    flex: 1,
  },
  cartItemName: {
    fontSize: 13,
    fontWeight: '700',
    color: '#0f172a',
  },
  cartItemUnitPrice: {
    fontSize: 11,
    color: '#64748b',
  },
  stepperBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f1f5f9',
    borderRadius: 8,
    marginHorizontal: 6,
  },
  stepBtn: {
    width: 26,
    height: 26,
    alignItems: 'center',
    justifyContent: 'center',
  },
  stepBtnText: {
    fontSize: 14,
    fontWeight: '800',
  },
  stepQty: {
    fontSize: 12,
    fontWeight: '800',
    paddingHorizontal: 4,
  },
  cartItemSubtotal: {
    fontSize: 13,
    fontWeight: '800',
    minWidth: 55,
    textAlign: 'right',
  },
  cartBottomBox: {
    padding: 14,
    borderTopWidth: 1,
    borderColor: '#e2e8f0',
  },
  summaryTotalRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 10,
  },
  summaryTotalLabel: {
    fontSize: 14,
    fontWeight: '900',
  },
  summaryTotalVal: {
    fontSize: 20,
    fontWeight: '900',
    color: '#059669',
  },
  checkoutBtn: {
    backgroundColor: '#059669',
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
  },
  checkoutBtnDisabled: {
    backgroundColor: '#cbd5e1',
  },
  checkoutBtnText: {
    color: '#ffffff',
    fontWeight: '900',
    fontSize: 14,
  },
  phoneBottomNav: {
    flexDirection: 'row',
    backgroundColor: '#0f172a',
    padding: 8,
  },
  phoneTabBtn: {
    flex: 1,
    paddingVertical: 10,
    alignItems: 'center',
    borderRadius: 10,
  },
  phoneTabActive: {
    backgroundColor: '#059669',
  },
  phoneTabText: {
    color: '#ffffff',
    fontWeight: '800',
  },
});
