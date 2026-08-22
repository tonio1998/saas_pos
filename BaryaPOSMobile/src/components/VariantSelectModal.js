import React from 'react';
import {
  StyleSheet,
  View,
  Text,
  Modal,
  TouchableOpacity,
  ScrollView,
} from 'react-native';

export default function VariantSelectModal({ visible, product, priceMode, onClose, onSelectVariant }) {
  if (!product) return null;

  const isWholesale = priceMode === 'wholesale';
  const baseRetail = Number(product.selling_price || 0);
  const baseWholesale = Number(product.wholesale_price || 0);
  const baseActive = (isWholesale && baseWholesale > 0) ? baseWholesale : baseRetail;
  const baseUnit = product.unit?.name || 'unit';
  const variants = product.variants || [];

  return (
    <Modal
      visible={visible}
      transparent
      animationType="fade"
      onRequestClose={onClose}
    >
      <TouchableOpacity style={styles.backdrop} activeOpacity={1} onPress={onClose}>
        <View style={styles.card} onStartShouldSetResponder={() => true}>
          <View style={styles.header}>
            <View style={styles.headerTitleBox}>
              <Text style={styles.title} numberOfLines={1}>{product.name}</Text>
              <Text style={styles.subtitle}>Select size or pack option ({variants.length + 1} options)</Text>
            </View>
            <TouchableOpacity onPress={onClose} style={styles.closeBtn}>
              <Text style={styles.closeBtnText}>✕</Text>
            </TouchableOpacity>
          </View>

          <ScrollView style={styles.scrollList}>
            {/* Base Option */}
            <TouchableOpacity
              style={styles.optionCard}
              onPress={() => onSelectVariant({
                id: product.id,
                variant_id: null,
                name: `${product.name} (Base)`,
                unit: baseUnit,
                price: baseActive,
                stock: Number(product.stock_on_hand || 0),
                barcode: product.barcode,
              })}
            >
              <View style={styles.optionLeft}>
                <Text style={styles.optionIcon}>📦</Text>
                <View>
                  <Text style={styles.optionName}>Base Unit (1 {baseUnit})</Text>
                  <Text style={styles.optionStock}>Stock: {product.stock_on_hand || 0}</Text>
                </View>
              </View>
              <Text style={styles.optionPrice}>₱{baseActive.toFixed(2)}</Text>
            </TouchableOpacity>

            {/* Pack / Variant Options */}
            {variants.map((v) => {
              const vRetail = Number(v.selling_price || 0);
              const vWholesale = Number(v.wholesale_price || 0);
              const vActive = (isWholesale && vWholesale > 0) ? vWholesale : vRetail;

              return (
                <TouchableOpacity
                  key={v.id}
                  style={styles.optionCard}
                  onPress={() => onSelectVariant({
                    id: product.id,
                    variant_id: v.id,
                    name: `${product.name} (${v.variant_name})`,
                    unit: v.unit?.name || baseUnit,
                    price: vActive,
                    stock: Number(v.stock_on_hand || product.stock_on_hand || 0),
                    barcode: v.barcode || product.barcode,
                  })}
                >
                  <View style={styles.optionLeft}>
                    <Text style={styles.optionIcon}>📚</Text>
                    <View>
                      <Text style={styles.optionName}>{v.variant_name}</Text>
                      <Text style={styles.optionStock}>Pack Variant</Text>
                    </View>
                  </View>
                  <Text style={[styles.optionPrice, styles.optionPriceVariant]}>₱{vActive.toFixed(2)}</Text>
                </TouchableOpacity>
              );
            })}
          </ScrollView>
        </View>
      </TouchableOpacity>
    </Modal>
  );
}

const styles = StyleSheet.create({
  backdrop: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.65)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  card: {
    width: '100%',
    maxWidth: 440,
    backgroundColor: '#ffffff',
    borderRadius: 20,
    padding: 20,
    maxHeight: '80%',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 14,
  },
  headerTitleBox: {
    flex: 1,
    marginRight: 10,
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
  scrollList: {
    maxHeight: 360,
  },
  optionCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 14,
    padding: 14,
    marginBottom: 10,
  },
  optionLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    flex: 1,
  },
  optionIcon: {
    fontSize: 22,
  },
  optionName: {
    fontSize: 14,
    fontWeight: '700',
    color: '#0f172a',
  },
  optionStock: {
    fontSize: 11,
    color: '#64748b',
    marginTop: 2,
  },
  optionPrice: {
    fontSize: 16,
    fontWeight: '900',
    color: '#059669',
  },
  optionPriceVariant: {
    color: '#7e22ce',
  },
});
