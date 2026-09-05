const fs = require('fs');

const bottomNavCode = `import React, { memo } from 'react';
import { StyleSheet, View, Text, TouchableOpacity } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import Ionicons from 'react-native-vector-icons/Ionicons';
import { ScreenType } from '../navigation/types';
import { useTheme } from '../theme';
import fonts from '../theme/fonts';

interface Props {
  activeScreen: ScreenType;
  onNavigate: (screen: ScreenType) => void;
  onOpenDrawer: () => void;
  cartCount?: number;
}

interface TabItem {
  id: ScreenType;
  label: string;
  activeIcon: string;
  inactiveIcon: string;
  badge?: number;
}

function BottomNavBarComponent({
  activeScreen,
  onNavigate,
  onOpenDrawer,
  cartCount = 0,
}: Props) {
  const insets = useSafeAreaInsets();
  const { colors } = useTheme();

  const tabs: TabItem[] = [
    { id: 'dashboard', label: 'Dashboard', activeIcon: 'grid', inactiveIcon: 'grid-outline' },
    { id: 'pos', label: 'POS Terminal', activeIcon: 'cart', inactiveIcon: 'cart-outline', badge: cartCount > 0 ? cartCount : undefined },
    { id: 'sales_history', label: 'Sales Ledger', activeIcon: 'receipt', inactiveIcon: 'receipt-outline' },
    { id: 'inventory', label: 'Products', activeIcon: 'cube', inactiveIcon: 'cube-outline' },
  ];

  return (
    <View style={[styles.container, { paddingBottom: Math.max(insets.bottom, 8) }]}>
      {tabs.map((tab) => {
        const isActive = activeScreen === tab.id;

        return (
          <TouchableOpacity
            key={tab.id}
            style={[
              styles.tabBtn,
              isActive && styles.tabBtnActive,
            ]}
            onPress={() => onNavigate(tab.id)}
            activeOpacity={0.7}
          >
            <View style={styles.iconContainer}>
              <Ionicons
                name={isActive ? tab.activeIcon : tab.inactiveIcon}
                size={22}
                color={isActive ? (colors.primary || '#059669') : '#64748b'}
              />
              {tab.badge !== undefined && (
                <View style={[styles.badgeBox, { backgroundColor: colors.primary || '#059669' }]}>
                  <Text style={styles.badgeText}>{tab.badge > 99 ? '99+' : tab.badge}</Text>
                </View>
              )}
            </View>
            <Text
              style={[
                styles.tabLabel,
                isActive && [styles.tabLabelActive, { color: colors.primary || '#059669' }],
              ]}
              numberOfLines={1}
            >
              {tab.label}
            </Text>
          </TouchableOpacity>
        );
      })}
    </View>
  );
}

const BottomNavBar = memo(BottomNavBarComponent);
export default BottomNavBar;

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    backgroundColor: '#ffffff',
    borderTopWidth: 1,
    borderColor: '#e2e8f0',
    paddingTop: 8,
    paddingHorizontal: 8,
    elevation: 10,
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: -3 },
    shadowOpacity: 0.06,
    shadowRadius: 10,
  },
  tabBtn: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 5,
    borderRadius: 12,
  },
  tabBtnActive: {
    backgroundColor: '#ecfdf5',
  },
  iconContainer: {
    position: 'relative',
    height: 24,
    justifyContent: 'center',
    alignItems: 'center',
  },
  badgeBox: {
    position: 'absolute',
    top: -4,
    right: -12,
    backgroundColor: '#059669',
    borderRadius: 10,
    paddingHorizontal: 5,
    paddingVertical: 1,
    minWidth: 18,
    alignItems: 'center',
    borderWidth: 1.5,
    borderColor: '#ffffff',
  },
  badgeText: {
    color: '#ffffff',
    fontSize: 9,
    fontWeight: '900',
  },
  tabLabel: {
    fontFamily: fonts.medium,
    fontSize: 11,
    color: '#64748b',
    marginTop: 3,
    fontWeight: '600',
  },
  tabLabelActive: {
    fontFamily: fonts.bold,
    fontWeight: '800',
  },
});
`;

fs.writeFileSync('C:/react2/saas_pos_app/src/components/BottomNavBar.tsx', bottomNavCode, 'utf8');
console.log('Successfully updated BottomNavBar.tsx with Ionicons vector icons');
