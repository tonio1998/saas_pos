const fs = require('fs');

const updatedPromoScreenContent = `import React, { useState, useEffect, useCallback, useMemo } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  TextInput,
  StatusBar,
  Modal,
  ScrollView,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Ionicons from 'react-native-vector-icons/Ionicons';
import fonts from '../../theme/fonts';
import { colors } from '../../theme/colors';
import { useTheme } from '../../theme';
import posApi from '../../services/api';
import AppHeader from '../../components/AppHeader';

interface PromotionsScreenProps {
  onOpenDrawer: () => void;
  onNavigateToPOS: () => void;
  onManageItems?: (promo: any) => void;
  navigation?: any;
}

export default function PromotionsScreen({
  onOpenDrawer,
  onNavigateToPOS,
  onManageItems,
  navigation,
}: PromotionsScreenProps) {
  const { theme } = useTheme();
  const isDark = theme?.dark_mode ?? false;

  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [promotions, setPromotions] = useState<any[]>([]);
  const [search, setSearch] = useState('');
  const [activeFilter, setActiveFilter] = useState<'all' | 'active' | 'targeted' | 'inactive'>('all');

  // Modal state
  const [modalVisible, setModalVisible] = useState(false);
  const [editingPromo, setEditingPromo] = useState<any | null>(null);
  const [saving, setSaving] = useState(false);

  // Form state
  const [title, setTitle] = useState('');
  const [promoCode, setPromoCode] = useState('');
  const [promoType, setPromoType] = useState<'percentage' | 'fixed_amount'>('percentage');
  const [discountValue, setDiscountValue] = useState('');
  const [minSpend, setMinSpend] = useState('');
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');
  const [appliesTo, setAppliesTo] = useState<'all' | 'product' | 'category'>('all');
  const [isActive, setIsActive] = useState(true);
  const [description, setDescription] = useState('');

  // Calendar Modal Picker State
  const [calendarVisible, setCalendarVisible] = useState(false);
  const [calendarField, setCalendarField] = useState<'start' | 'end'>('start');
  const [pickerYear, setPickerYear] = useState(2026);
  const [pickerMonth, setPickerMonth] = useState(8);

  // Navigate to dedicated Manage Promo Items screen
  const handleGoToManageItems = (promo: any) => {
    if (onManageItems) {
      onManageItems(promo);
    } else if (navigation?.navigate) {
      navigation.navigate('ManagePromoItems', { promoId: promo.id, promo });
    }
  };

  // Helper date formatter
  const formatDateToYMD = (date: Date): string => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + d;
  };

  // Fetch Promotions
  const fetchPromotions = useCallback(async () => {
    try {
      const res = await posApi.getPromotions();
      if (res?.data) {
        setPromotions(res.data);
      }
    } catch (err: any) {
      console.warn('Failed to load promotions:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    fetchPromotions();
  }, [fetchPromotions]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchPromotions();
  };

  // Compute KPI summary metrics (matching Web version)
  const kpiStats = useMemo(() => {
    const total = promotions.length;
    const active = promotions.filter(p => p.is_active !== false).length;
    const targeted = promotions.filter(p => p.applies_to === 'product' || p.applies_to === 'category' || p.promo_type === 'bulk_tier').length;
    return { total, active, targeted };
  }, [promotions]);

  // Open Create Modal
  const openCreateModal = () => {
    const now = new Date();
    const defaultStart = formatDateToYMD(now);
    const end = new Date(now);
    end.setDate(end.getDate() + 14);
    const defaultEnd = formatDateToYMD(end);

    setEditingPromo(null);
    setTitle('');
    setPromoCode('');
    setPromoType('percentage');
    setDiscountValue('');
    setMinSpend('0');
    setStartDate(defaultStart);
    setEndDate(defaultEnd);
    setAppliesTo('product');
    setIsActive(true);
    setDescription('');
    setModalVisible(true);
  };

  // Open Edit Modal
  const openEditModal = (promo: any) => {
    setEditingPromo(promo);
    setTitle(promo.title || promo.name || '');
    setPromoCode(promo.promo_code || promo.code || '');
    setPromoType(promo.promo_type === 'fixed_amount' ? 'fixed_amount' : 'percentage');
    setDiscountValue(promo.discount_value != null ? String(promo.discount_value) : '');
    setMinSpend(promo.min_spend != null ? String(promo.min_spend) : '0');
    setStartDate(promo.start_date ? promo.start_date.substring(0, 10) : formatDateToYMD(new Date()));
    setEndDate(promo.end_date ? promo.end_date.substring(0, 10) : formatDateToYMD(new Date()));
    setAppliesTo(promo.applies_to || 'all');
    setIsActive(promo.is_active !== false);
    setDescription(promo.description || '');
    setModalVisible(true);
  };

  // Open Calendar Picker
  const openCalendarFor = (field: 'start' | 'end') => {
    setCalendarField(field);
    const currentDateStr = field === 'start' ? startDate : endDate;
    if (currentDateStr) {
      const parts = currentDateStr.split('-');
      if (parts.length === 3) {
        setPickerYear(parseInt(parts[0], 10));
        setPickerMonth(parseInt(parts[1], 10) - 1);
      }
    }
    setCalendarVisible(true);
  };

  // Handle Date Selection from Calendar
  const handleSelectCalendarDate = (day: number) => {
    const selected = new Date(pickerYear, pickerMonth, day);
    const formatted = formatDateToYMD(selected);

    if (calendarField === 'start') {
      setStartDate(formatted);
      if (endDate && formatted > endDate) {
        setEndDate(formatted);
      }
    } else {
      if (startDate && formatted < startDate) {
        Alert.alert('Invalid Date Range', 'End date cannot be earlier than start date (' + startDate + ').');
        return;
      }
      setEndDate(formatted);
    }
    setCalendarVisible(false);
  };

  // Save Promotion Campaign
  const handleSave = async () => {
    if (!title.trim()) {
      Alert.alert('Validation Error', 'Please enter a promotion title.');
      return;
    }
    const valNum = parseFloat(discountValue);
    if (isNaN(valNum) || valNum <= 0) {
      Alert.alert('Validation Error', 'Please enter a valid discount value greater than 0.');
      return;
    }
    if (promoType === 'percentage' && valNum > 100) {
      Alert.alert('Validation Error', 'Percentage discount cannot exceed 100%.');
      return;
    }
    if (!startDate || !endDate) {
      Alert.alert('Validation Error', 'Please select both start and end dates.');
      return;
    }
    if (endDate < startDate) {
      Alert.alert('Validation Error', 'End date cannot be earlier than start date.');
      return;
    }

    setSaving(true);
    try {
      const payload: any = {
        title: title.trim(),
        name: title.trim(),
        promo_code: promoCode.trim() ? promoCode.trim().toUpperCase() : null,
        promo_type: promoType,
        discount_type: promoType,
        discount_value: valNum,
        min_spend: parseFloat(minSpend) || 0,
        start_date: startDate,
        end_date: endDate,
        applies_to: appliesTo,
        is_active: isActive ? 1 : 0,
        description: description.trim() || null,
      };

      let savedPromo: any = null;

      if (editingPromo) {
        const res = await posApi.updatePromotion(editingPromo.id, payload);
        if (res?.success) {
          savedPromo = res.data || { ...editingPromo, ...payload };
          Alert.alert('Success', 'Promotion updated successfully!');
        } else {
          Alert.alert('Error', res?.message || 'Failed to update promotion.');
          return;
        }
      } else {
        const res = await posApi.createPromotion(payload);
        if (res?.success) {
          savedPromo = res.data;
        } else {
          Alert.alert('Error', res?.message || 'Failed to create promotion.');
          return;
        }
      }

      setModalVisible(false);
      await fetchPromotions();

      // If specific products/categories, DIRECTLY navigate to Manage Items Screen!
      if (appliesTo !== 'all' && savedPromo) {
        handleGoToManageItems(savedPromo);
      }
    } catch (err: any) {
      Alert.alert('Error', err?.message || 'An unexpected error occurred while saving.');
    } finally {
      setSaving(false);
    }
  };

  // Toggle Active
  const handleToggleActive = async (promo: any) => {
    try {
      const res = await posApi.togglePromotion(promo.id);
      if (res?.success) {
        setPromotions(prev =>
          prev.map(p => (p.id === promo.id ? { ...p, is_active: !p.is_active } : p))
        );
      } else {
        Alert.alert('Error', res?.message || 'Could not toggle status.');
      }
    } catch (err: any) {
      Alert.alert('Error', err?.message || 'Network error.');
    }
  };

  // Delete Promotion
  const handleDelete = (promo: any) => {
    Alert.alert(
      'Delete Promotion',
      'Are you sure you want to delete "' + (promo.title || promo.name) + '"?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              const res = await posApi.deletePromotion(promo.id);
              if (res?.success) {
                setPromotions(prev => prev.filter(p => p.id !== promo.id));
              } else {
                Alert.alert('Error', res?.message || 'Failed to delete promotion.');
              }
            } catch (err: any) {
              Alert.alert('Error', err?.message || 'Network error deleting promotion.');
            }
          },
        },
      ]
    );
  };

  // Filtered promotions
  const filteredPromotions = useMemo(() => {
    return promotions.filter(p => {
      if (activeFilter === 'active' && p.is_active === false) return false;
      if (activeFilter === 'inactive' && p.is_active !== false) return false;
      if (activeFilter === 'targeted' && p.applies_to === 'all') return false;
      if (!search.trim()) return true;
      const q = search.toLowerCase();
      const title = (p.title || p.name || '').toLowerCase();
      const code = (p.promo_code || p.code || '').toLowerCase();
      return title.includes(q) || code.includes(q);
    });
  }, [promotions, activeFilter, search]);

  // Calendar Days generator
  const renderCalendarGrid = () => {
    const daysInMonth = new Date(pickerYear, pickerMonth + 1, 0).getDate();
    const firstDayIndex = new Date(pickerYear, pickerMonth, 1).getDay();
    const monthNames = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ];

    const days = [];
    for (let i = 0; i < firstDayIndex; i++) {
      days.push(<View key={'empty-' + i} style={styles.calendarDayEmpty} />);
    }

    const currentSelected = calendarField === 'start' ? startDate : endDate;

    for (let d = 1; d <= daysInMonth; d++) {
      const thisDate = new Date(pickerYear, pickerMonth, d);
      const ymd = formatDateToYMD(thisDate);
      const isSelected = ymd === currentSelected;
      const isToday = formatDateToYMD(new Date()) === ymd;
      const isDisabled = Boolean(calendarField === 'end' && startDate && ymd < startDate);

      days.push(
        <TouchableOpacity
          key={'day-' + d}
          style={[
            styles.calendarDay,
            isSelected && { backgroundColor: colors.primary },
            isToday && !isSelected && { borderColor: colors.primary, borderWidth: 1 },
            isDisabled ? { opacity: 0.3 } : undefined,
          ]}
          disabled={isDisabled}
          onPress={() => handleSelectCalendarDate(d)}
        >
          <Text
            style={[
              styles.calendarDayText,
              { color: isSelected ? '#FFFFFF' : (isDark ? '#F8FAFC' : '#0F172A') },
              isDisabled ? { color: isDark ? '#64748B' : '#94A3B8' } : undefined,
            ]}
          >
            {d}
          </Text>
        </TouchableOpacity>
      );
    }

    return (
      <View style={styles.calendarBody}>
        {/* Month Navigation */}
        <View style={styles.calendarMonthHeader}>
          <TouchableOpacity
            style={styles.calendarMonthNavBtn}
            onPress={() => {
              if (pickerMonth === 0) {
                setPickerMonth(11);
                setPickerYear(y => y - 1);
              } else {
                setPickerMonth(m => m - 1);
              }
            }}
          >
            <Ionicons name="chevron-back" size={20} color={isDark ? '#F8FAFC' : '#0F172A'} />
          </TouchableOpacity>

          <Text style={[styles.calendarMonthTitle, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
            {monthNames[pickerMonth]} {pickerYear}
          </Text>

          <TouchableOpacity
            style={styles.calendarMonthNavBtn}
            onPress={() => {
              if (pickerMonth === 11) {
                setPickerMonth(0);
                setPickerYear(y => y + 1);
              } else {
                setPickerMonth(m => m + 1);
              }
            }}
          >
            <Ionicons name="chevron-forward" size={20} color={isDark ? '#F8FAFC' : '#0F172A'} />
          </TouchableOpacity>
        </View>

        {/* Days of week */}
        <View style={styles.calendarWeekRow}>
          {['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'].map((w, idx) => (
            <Text key={'weekday-' + idx} style={[styles.calendarWeekText, { color: isDark ? '#94A3B8' : '#64748B' }]}>
              {w}
            </Text>
          ))}
        </View>

        {/* Day Grid */}
        <View style={styles.calendarDaysGrid}>{days}</View>
      </View>
    );
  };

  // Render 3 KPI Metric Cards
  const renderKpiCards = () => (
    <View style={styles.kpiContainer}>
      {/* 1. Active Promotions */}
      <TouchableOpacity
        style={[
          styles.kpiCard,
          {
            backgroundColor: isDark ? '#1E293B' : '#FFFFFF',
            borderColor: activeFilter === 'active' ? '#10B981' : (isDark ? '#334155' : '#E2E8F0'),
            borderWidth: activeFilter === 'active' ? 2 : 1,
          },
        ]}
        onPress={() => setActiveFilter(prev => prev === 'active' ? 'all' : 'active')}
        activeOpacity={0.8}
      >
        <View style={styles.kpiTopRow}>
          <Text style={[styles.kpiLabel, { color: isDark ? '#94A3B8' : '#64748B' }]}>ACTIVE</Text>
          <View style={[styles.kpiIconCircle, { backgroundColor: '#D1FAE5' }]}>
            <Ionicons name="checkmark-circle" size={16} color="#059669" />
          </View>
        </View>
        <Text style={[styles.kpiValue, { color: '#059669' }]}>{kpiStats.active}</Text>
        <Text style={[styles.kpiSubtext, { color: isDark ? '#94A3B8' : '#64748B' }]} numberOfLines={1}>
          Auto-applying in POS
        </Text>
      </TouchableOpacity>

      {/* 2. Targeted / Specific Items */}
      <TouchableOpacity
        style={[
          styles.kpiCard,
          {
            backgroundColor: isDark ? '#1E293B' : '#FFFFFF',
            borderColor: activeFilter === 'targeted' ? '#F59E0B' : (isDark ? '#334155' : '#E2E8F0'),
            borderWidth: activeFilter === 'targeted' ? 2 : 1,
          },
        ]}
        onPress={() => setActiveFilter(prev => prev === 'targeted' ? 'all' : 'targeted')}
        activeOpacity={0.8}
      >
        <View style={styles.kpiTopRow}>
          <Text style={[styles.kpiLabel, { color: isDark ? '#94A3B8' : '#64748B' }]}>TARGETED</Text>
          <View style={[styles.kpiIconCircle, { backgroundColor: '#FEF3C7' }]}>
            <Ionicons name="cube" size={16} color="#D97706" />
          </View>
        </View>
        <Text style={[styles.kpiValue, { color: '#D97706' }]}>{kpiStats.targeted}</Text>
        <Text style={[styles.kpiSubtext, { color: isDark ? '#94A3B8' : '#64748B' }]} numberOfLines={1}>
          Specific item rules
        </Text>
      </TouchableOpacity>

      {/* 3. Total Campaigns */}
      <TouchableOpacity
        style={[
          styles.kpiCard,
          {
            backgroundColor: isDark ? '#1E293B' : '#FFFFFF',
            borderColor: activeFilter === 'all' ? colors.primary : (isDark ? '#334155' : '#E2E8F0'),
            borderWidth: activeFilter === 'all' ? 2 : 1,
          },
        ]}
        onPress={() => setActiveFilter('all')}
        activeOpacity={0.8}
      >
        <View style={styles.kpiTopRow}>
          <Text style={[styles.kpiLabel, { color: isDark ? '#94A3B8' : '#64748B' }]}>TOTAL</Text>
          <View style={[styles.kpiIconCircle, { backgroundColor: colors.primarySoft }]}>
            <Ionicons name="pricetags" size={16} color={colors.primary} />
          </View>
        </View>
        <Text style={[styles.kpiValue, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>{kpiStats.total}</Text>
        <Text style={[styles.kpiSubtext, { color: isDark ? '#94A3B8' : '#64748B' }]} numberOfLines={1}>
          All campaigns
        </Text>
      </TouchableOpacity>
    </View>
  );

  // Render Promo Card
  const renderPromoCard = ({ item }: { item: any }) => {
    const isPercent = item.promo_type === 'percentage' || item.discount_type === 'percentage';
    const discountVal = parseFloat(item.discount_value || 0);
    const discountLabel = isPercent
      ? (discountVal + '% OFF')
      : ('₱' + discountVal.toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' OFF');

    const itemCount = item.items_count ?? (item.items ? item.items.length : (item.target_ids ? item.target_ids.length : 0));
    const startStr = item.start_date ? item.start_date.substring(0, 10) : 'Anytime';
    const endStr = item.end_date ? item.end_date.substring(0, 10) : 'Ongoing';

    let scopeLabel = 'Storewide (All Products)';
    let scopeIcon = 'globe-outline';
    if (item.applies_to === 'product') {
      scopeLabel = itemCount > 0 ? (itemCount + ' Specific Products') : 'Specific Products';
      scopeIcon = 'cube-outline';
    } else if (item.applies_to === 'category') {
      scopeLabel = itemCount > 0 ? (itemCount + ' Categories') : 'Specific Category';
      scopeIcon = 'grid-outline';
    }

    return (
      <View
        style={[
          styles.promoCard,
          {
            backgroundColor: isDark ? '#1E293B' : '#FFFFFF',
            borderColor: isDark ? '#334155' : '#E2E8F0',
          },
        ]}
      >
        {/* Top Header of Card */}
        <View style={styles.cardHeader}>
          <View style={{ flex: 1 }}>
            <View style={styles.cardBadgeRow}>
              <View style={[styles.discountBadge, { backgroundColor: colors.primary }]}>
                <Ionicons name="pricetag" size={12} color="#FFFFFF" style={{ marginRight: 4 }} />
                <Text style={styles.discountBadgeText}>{discountLabel}</Text>
              </View>

              {item.promo_code ? (
                <View style={[styles.codeBadge, { backgroundColor: isDark ? '#334155' : '#F1F5F9' }]}>
                  <Ionicons name="key-outline" size={11} color={isDark ? '#CBD5E1' : '#475569'} style={{ marginRight: 3 }} />
                  <Text style={[styles.codeBadgeText, { color: isDark ? '#CBD5E1' : '#475569' }]}>
                    {item.promo_code}
                  </Text>
                </View>
              ) : null}

              <TouchableOpacity
                style={[
                  styles.statusBadge,
                  { backgroundColor: item.is_active ? '#D1FAE5' : '#FEE2E2' },
                ]}
                onPress={() => handleToggleActive(item)}
              >
                <View style={[styles.statusDot, { backgroundColor: item.is_active ? '#10B981' : '#EF4444' }]} />
                <Text style={[styles.statusText, { color: item.is_active ? '#065F46' : '#991B1B' }]}>
                  {item.is_active ? 'Active' : 'Inactive'}
                </Text>
              </TouchableOpacity>
            </View>

            <Text style={[styles.cardTitle, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
              {item.title || item.name}
            </Text>
          </View>
        </View>

        {/* Card Details */}
        <View style={[styles.cardDetails, { borderTopColor: isDark ? '#334155' : '#F1F5F9' }]}>
          <View style={styles.detailRow}>
            <Ionicons name="calendar-outline" size={14} color={isDark ? '#94A3B8' : '#64748B'} style={{ marginRight: 6 }} />
            <Text style={[styles.detailText, { color: isDark ? '#CBD5E1' : '#475569' }]}>
              {startStr} to {endStr}
            </Text>
          </View>

          <View style={styles.detailRow}>
            <Ionicons name={scopeIcon as any} size={14} color={colors.primary} style={{ marginRight: 6 }} />
            <Text style={[styles.detailText, { color: isDark ? '#CBD5E1' : '#475569' }]}>
              {scopeLabel}
            </Text>
          </View>

          {item.min_spend && parseFloat(item.min_spend) > 0 ? (
            <View style={styles.detailRow}>
              <Ionicons name="cart-outline" size={14} color={isDark ? '#94A3B8' : '#64748B'} style={{ marginRight: 6 }} />
              <Text style={[styles.detailText, { color: isDark ? '#CBD5E1' : '#475569' }]}>
                Min. Spend: ₱{parseFloat(item.min_spend).toFixed(2)}
              </Text>
            </View>
          ) : null}
        </View>

        {/* Card Actions */}
        <View style={[styles.cardActions, { borderTopColor: isDark ? '#334155' : '#F1F5F9' }]}>
          {item.applies_to !== 'all' ? (
            <TouchableOpacity
              style={[styles.manageItemsBtn, { backgroundColor: colors.primary }]}
              onPress={() => handleGoToManageItems(item)}
            >
              <Ionicons name="list-circle-outline" size={16} color="#FFFFFF" style={{ marginRight: 6 }} />
              <Text style={styles.manageItemsBtnText}>
                Manage Products ({itemCount})
              </Text>
            </TouchableOpacity>
          ) : (
            <View style={styles.storewideTag}>
              <Ionicons name="checkmark-done" size={14} color={colors.primary} style={{ marginRight: 4 }} />
              <Text style={[styles.storewideTagText, { color: colors.primaryDark }]}>Applies to All Products</Text>
            </View>
          )}

          <View style={styles.actionIconRow}>
            <TouchableOpacity
              style={[styles.iconButton, { backgroundColor: isDark ? '#334155' : '#F1F5F9' }]}
              onPress={() => openEditModal(item)}
            >
              <Ionicons name="create-outline" size={18} color={isDark ? '#94A3B8' : '#475569'} />
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.iconButton, { backgroundColor: '#FEE2E2' }]}
              onPress={() => handleDelete(item)}
            >
              <Ionicons name="trash-outline" size={18} color="#EF4444" />
            </TouchableOpacity>
          </View>
        </View>
      </View>
    );
  };

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: isDark ? '#0F172A' : '#F8FAFC' }]} edges={['top']}>
      <StatusBar barStyle={isDark ? 'light-content' : 'dark-content'} />

      {/* Screen Header */}
      <AppHeader
        title="Promotions & Discounts"
        subtitle="Manage campaign rules and targeted product discounts"
        onOpenDrawer={onOpenDrawer}
        rightAction={
          <TouchableOpacity style={styles.headerAddBtn} onPress={openCreateModal}>
            <Ionicons name="add" size={20} color="#FFFFFF" />
            <Text style={styles.headerAddBtnText}>New Promo</Text>
          </TouchableOpacity>
        }
      />

      {/* 3 KPI Metric Cards */}
      {renderKpiCards()}

      {/* Search & Filter Bar */}
      <View style={styles.searchSection}>
        <View style={[styles.searchBox, { backgroundColor: isDark ? '#1E293B' : '#FFFFFF', borderColor: isDark ? '#334155' : '#CBD5E1' }]}>
          <Ionicons name="search-outline" size={18} color={isDark ? '#94A3B8' : '#64748B'} style={{ marginRight: 8 }} />
          <TextInput
            style={[styles.searchInput, { color: isDark ? '#F8FAFC' : '#0F172A' }]}
            placeholder="Search promotions by title or code..."
            placeholderTextColor={isDark ? '#64748B' : '#94A3B8'}
            value={search}
            onChangeText={setSearch}
          />
          {search ? (
            <TouchableOpacity onPress={() => setSearch('')}>
              <Ionicons name="close-circle" size={18} color={isDark ? '#94A3B8' : '#64748B'} />
            </TouchableOpacity>
          ) : null}
        </View>

        {/* Filter Pills */}
        <View style={styles.filterPillsRow}>
          {[
            { id: 'all', label: 'All (' + kpiStats.total + ')' },
            { id: 'active', label: 'Active (' + kpiStats.active + ')' },
            { id: 'targeted', label: 'Targeted Items (' + kpiStats.targeted + ')' },
            { id: 'inactive', label: 'Inactive' },
          ].map((f: any) => {
            const isSelected = activeFilter === f.id;
            return (
              <TouchableOpacity
                key={'filter-' + f.id}
                style={[
                  styles.filterPill,
                  isSelected
                    ? { backgroundColor: colors.primary, borderColor: colors.primary }
                    : { backgroundColor: isDark ? '#1E293B' : '#FFFFFF', borderColor: isDark ? '#334155' : '#CBD5E1' },
                ]}
                onPress={() => setActiveFilter(f.id)}
              >
                <Text
                  style={[
                    styles.filterPillText,
                    { color: isSelected ? '#FFFFFF' : (isDark ? '#CBD5E1' : '#475569') },
                  ]}
                >
                  {f.label}
                </Text>
              </TouchableOpacity>
            );
          })}
        </View>
      </View>

      {/* Promotions List */}
      {loading ? (
        <View style={styles.centerLoading}>
          <ActivityIndicator size="large" color={colors.primary} />
          <Text style={[styles.loadingText, { color: isDark ? '#94A3B8' : '#64748B' }]}>
            Loading promotions...
          </Text>
        </View>
      ) : (
        <FlatList
          data={filteredPromotions}
          keyExtractor={(item) => 'promo-' + item.id}
          renderItem={renderPromoCard}
          contentContainerStyle={styles.listContent}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[colors.primary]} />}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="pricetags-outline" size={54} color={isDark ? '#334155' : '#CBD5E1'} />
              <Text style={[styles.emptyTitle, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                No promotions found
              </Text>
              <Text style={[styles.emptySubtitle, { color: isDark ? '#94A3B8' : '#64748B' }]}>
                {search ? 'Try adjusting your search query.' : 'Create promotional campaigns to boost store sales.'}
              </Text>
              <TouchableOpacity style={styles.emptyCreateBtn} onPress={openCreateModal}>
                <Ionicons name="add-circle-outline" size={18} color="#FFFFFF" style={{ marginRight: 6 }} />
                <Text style={styles.emptyCreateBtnText}>Create New Promotion</Text>
              </TouchableOpacity>
            </View>
          }
        />
      )}

      {/* Campaign Settings Modal (Zero Lag) */}
      <Modal
        visible={modalVisible}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[styles.modalSheet, { backgroundColor: isDark ? '#1E293B' : '#FFFFFF' }]}>
            {/* Modal Header */}
            <View style={[styles.modalHeader, { borderBottomColor: isDark ? '#334155' : '#E2E8F0' }]}>
              <View>
                <Text style={[styles.modalHeaderTitle, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                  {editingPromo ? 'Edit Promotion Campaign' : 'New Promotion Campaign'}
                </Text>
                <Text style={[styles.modalHeaderSubtitle, { color: isDark ? '#94A3B8' : '#64748B' }]}>
                  Configure discount rules and schedule
                </Text>
              </View>
              <TouchableOpacity style={styles.modalCloseBtn} onPress={() => setModalVisible(false)}>
                <Ionicons name="close" size={22} color={isDark ? '#94A3B8' : '#64748B'} />
              </TouchableOpacity>
            </View>

            {/* Modal Form Content */}
            <ScrollView style={styles.modalBody} showsVerticalScrollIndicator={false}>
              {/* Promotion Title */}
              <View style={styles.formGroup}>
                <Text style={[styles.formLabel, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                  Promotion Title <Text style={{ color: '#EF4444' }}>*</Text>
                </Text>
                <TextInput
                  style={[styles.formInput, { backgroundColor: isDark ? '#0F172A' : '#F8FAFC', borderColor: isDark ? '#334155' : '#CBD5E1', color: isDark ? '#F8FAFC' : '#0F172A' }]}
                  placeholder="e.g. Weekend Mega Sale 20% OFF"
                  placeholderTextColor={isDark ? '#64748B' : '#94A3B8'}
                  value={title}
                  onChangeText={setTitle}
                />
              </View>

              {/* Promo Code */}
              <View style={styles.formGroup}>
                <Text style={[styles.formLabel, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                  Promo Code (Optional)
                </Text>
                <TextInput
                  style={[styles.formInput, { backgroundColor: isDark ? '#0F172A' : '#F8FAFC', borderColor: isDark ? '#334155' : '#CBD5E1', color: isDark ? '#F8FAFC' : '#0F172A' }]}
                  placeholder="e.g. SUMMER20 (Leave blank for automatic)"
                  placeholderTextColor={isDark ? '#64748B' : '#94A3B8'}
                  value={promoCode}
                  onChangeText={setPromoCode}
                  autoCapitalize="characters"
                />
              </View>

              {/* Discount Type Selector (Percentage vs Fixed Amount) */}
              <View style={styles.formGroup}>
                <Text style={[styles.formLabel, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                  Discount Type <Text style={{ color: '#EF4444' }}>*</Text>
                </Text>
                <View style={styles.typeSelectorRow}>
                  <TouchableOpacity
                    style={[
                      styles.typeSelectorBtn,
                      promoType === 'percentage'
                        ? { backgroundColor: colors.primary, borderColor: colors.primary }
                        : { backgroundColor: isDark ? '#0F172A' : '#F8FAFC', borderColor: isDark ? '#334155' : '#CBD5E1' },
                    ]}
                    onPress={() => setPromoType('percentage')}
                  >
                    <Ionicons
                      name="pie-chart-outline"
                      size={18}
                      color={promoType === 'percentage' ? '#FFFFFF' : (isDark ? '#94A3B8' : '#64748B')}
                      style={{ marginRight: 6 }}
                    />
                    <Text
                      style={[
                        styles.typeSelectorBtnText,
                        { color: promoType === 'percentage' ? '#FFFFFF' : (isDark ? '#CBD5E1' : '#475569') },
                      ]}
                    >
                      Percentage (%)
                    </Text>
                  </TouchableOpacity>

                  <TouchableOpacity
                    style={[
                      styles.typeSelectorBtn,
                      promoType === 'fixed_amount'
                        ? { backgroundColor: colors.primary, borderColor: colors.primary }
                        : { backgroundColor: isDark ? '#0F172A' : '#F8FAFC', borderColor: isDark ? '#334155' : '#CBD5E1' },
                    ]}
                    onPress={() => setPromoType('fixed_amount')}
                  >
                    <Ionicons
                      name="cash-outline"
                      size={18}
                      color={promoType === 'fixed_amount' ? '#FFFFFF' : (isDark ? '#94A3B8' : '#64748B')}
                      style={{ marginRight: 6 }}
                    />
                    <Text
                      style={[
                        styles.typeSelectorBtnText,
                        { color: promoType === 'fixed_amount' ? '#FFFFFF' : (isDark ? '#CBD5E1' : '#475569') },
                      ]}
                    >
                      Fixed Amount (₱)
                    </Text>
                  </TouchableOpacity>
                </View>
              </View>

              {/* Discount Value & Min Spend */}
              <View style={styles.formRow}>
                <View style={[styles.formGroup, { flex: 1, marginRight: 8 }]}>
                  <Text style={[styles.formLabel, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                    Discount Value <Text style={{ color: '#EF4444' }}>*</Text>
                  </Text>
                  <View style={[styles.inputWithAddon, { backgroundColor: isDark ? '#0F172A' : '#F8FAFC', borderColor: isDark ? '#334155' : '#CBD5E1' }]}>
                    <Text style={[styles.inputAddonText, { color: colors.primary }]}>
                      {promoType === 'percentage' ? '%' : '₱'}
                    </Text>
                    <TextInput
                      style={[styles.inputWithAddonField, { color: isDark ? '#F8FAFC' : '#0F172A' }]}
                      placeholder={promoType === 'percentage' ? '20' : '50.00'}
                      placeholderTextColor={isDark ? '#64748B' : '#94A3B8'}
                      keyboardType="numeric"
                      value={discountValue}
                      onChangeText={setDiscountValue}
                    />
                  </View>
                </View>

                <View style={[styles.formGroup, { flex: 1, marginLeft: 8 }]}>
                  <Text style={[styles.formLabel, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                    Min. Spend (₱)
                  </Text>
                  <View style={[styles.inputWithAddon, { backgroundColor: isDark ? '#0F172A' : '#F8FAFC', borderColor: isDark ? '#334155' : '#CBD5E1' }]}>
                    <Text style={[styles.inputAddonText, { color: colors.primary }]}>₱</Text>
                    <TextInput
                      style={[styles.inputWithAddonField, { color: isDark ? '#F8FAFC' : '#0F172A' }]}
                      placeholder="0.00"
                      placeholderTextColor={isDark ? '#64748B' : '#94A3B8'}
                      keyboardType="numeric"
                      value={minSpend}
                      onChangeText={setMinSpend}
                    />
                  </View>
                </View>
              </View>

              {/* Schedule Dates with Calendar Picker */}
              <View style={styles.formRow}>
                <View style={[styles.formGroup, { flex: 1, marginRight: 8 }]}>
                  <Text style={[styles.formLabel, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                    Start Date <Text style={{ color: '#EF4444' }}>*</Text>
                  </Text>
                  <TouchableOpacity
                    style={[styles.datePickerBtn, { backgroundColor: isDark ? '#0F172A' : '#F8FAFC', borderColor: isDark ? '#334155' : '#CBD5E1' }]}
                    onPress={() => openCalendarFor('start')}
                  >
                    <Ionicons name="calendar-outline" size={16} color={colors.primary} style={{ marginRight: 6 }} />
                    <Text style={[styles.datePickerBtnText, { color: startDate ? (isDark ? '#F8FAFC' : '#0F172A') : '#94A3B8' }]}>
                      {startDate || 'Select start date'}
                    </Text>
                  </TouchableOpacity>
                </View>

                <View style={[styles.formGroup, { flex: 1, marginLeft: 8 }]}>
                  <Text style={[styles.formLabel, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                    End Date <Text style={{ color: '#EF4444' }}>*</Text>
                  </Text>
                  <TouchableOpacity
                    style={[styles.datePickerBtn, { backgroundColor: isDark ? '#0F172A' : '#F8FAFC', borderColor: isDark ? '#334155' : '#CBD5E1' }]}
                    onPress={() => openCalendarFor('end')}
                  >
                    <Ionicons name="calendar-outline" size={16} color={colors.primary} style={{ marginRight: 6 }} />
                    <Text style={[styles.datePickerBtnText, { color: endDate ? (isDark ? '#F8FAFC' : '#0F172A') : '#94A3B8' }]}>
                      {endDate || 'Select end date'}
                    </Text>
                  </TouchableOpacity>
                </View>
              </View>

              {/* Target Scope (Applies To) */}
              <View style={styles.formGroup}>
                <Text style={[styles.formLabel, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                  Target Scope <Text style={{ color: '#EF4444' }}>*</Text>
                </Text>
                <View style={styles.scopeSelector}>
                  {[
                    { id: 'all', title: 'Storewide', desc: 'Applies to all products' },
                    { id: 'product', title: 'Specific Products', desc: 'Select individual products' },
                    { id: 'category', title: 'Specific Categories', desc: 'Select product categories' },
                  ].map((s: any) => {
                    const isSelected = appliesTo === s.id;
                    return (
                      <TouchableOpacity
                        key={'scope-' + s.id}
                        style={[
                          styles.scopeOption,
                          isSelected
                            ? { backgroundColor: isDark ? '#064E3B' : '#ECFDF5', borderColor: colors.primary, borderWidth: 1.5 }
                            : { backgroundColor: isDark ? '#0F172A' : '#F8FAFC', borderColor: isDark ? '#334155' : '#CBD5E1', borderWidth: 1 },
                        ]}
                        onPress={() => setAppliesTo(s.id)}
                      >
                        <Ionicons
                          name={isSelected ? 'radio-button-on' : 'radio-button-off'}
                          size={18}
                          color={isSelected ? colors.primary : (isDark ? '#64748B' : '#94A3B8')}
                          style={{ marginRight: 10 }}
                        />
                        <View style={{ flex: 1 }}>
                          <Text style={[styles.scopeOptionTitle, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                            {s.title}
                          </Text>
                          <Text style={[styles.scopeOptionDesc, { color: isDark ? '#94A3B8' : '#64748B' }]}>
                            {s.desc}
                          </Text>
                        </View>
                      </TouchableOpacity>
                    );
                  })}
                </View>
              </View>

              {/* Description */}
              <View style={styles.formGroup}>
                <Text style={[styles.formLabel, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                  Description / Internal Notes
                </Text>
                <TextInput
                  style={[styles.formTextArea, { backgroundColor: isDark ? '#0F172A' : '#F8FAFC', borderColor: isDark ? '#334155' : '#CBD5E1', color: isDark ? '#F8FAFC' : '#0F172A' }]}
                  placeholder="Additional campaign details..."
                  placeholderTextColor={isDark ? '#64748B' : '#94A3B8'}
                  multiline={true}
                  numberOfLines={3}
                  value={description}
                  onChangeText={setDescription}
                />
              </View>
            </ScrollView>

            {/* Modal Footer Actions */}
            <View style={[styles.modalFooter, { borderTopColor: isDark ? '#334155' : '#E2E8F0' }]}>
              <TouchableOpacity
                style={[styles.modalCancelBtn, { backgroundColor: isDark ? '#334155' : '#F1F5F9' }]}
                onPress={() => setModalVisible(false)}
                disabled={saving}
              >
                <Text style={[styles.modalCancelBtnText, { color: isDark ? '#CBD5E1' : '#475569' }]}>
                  Cancel
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[styles.modalSaveBtn, { backgroundColor: colors.primary }]}
                onPress={handleSave}
                disabled={saving}
              >
                {saving ? (
                  <ActivityIndicator size="small" color="#FFFFFF" />
                ) : (
                  <>
                    <Ionicons name="checkmark-circle-outline" size={18} color="#FFFFFF" style={{ marginRight: 6 }} />
                    <Text style={styles.modalSaveBtnText}>
                      {appliesTo !== 'all' ? 'Save & Add Products →' : 'Save Campaign'}
                    </Text>
                  </>
                )}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

      {/* Calendar Picker Modal */}
      <Modal
        visible={calendarVisible}
        transparent={true}
        animationType="fade"
        onRequestClose={() => setCalendarVisible(false)}
      >
        <View style={styles.calendarOverlay}>
          <View style={[styles.calendarDialog, { backgroundColor: isDark ? '#1E293B' : '#FFFFFF' }]}>
            <View style={styles.calendarDialogHeader}>
              <Text style={[styles.calendarDialogTitle, { color: isDark ? '#F8FAFC' : '#0F172A' }]}>
                Select {calendarField === 'start' ? 'Start Date' : 'End Date'}
              </Text>
              <TouchableOpacity onPress={() => setCalendarVisible(false)}>
                <Ionicons name="close" size={22} color={isDark ? '#94A3B8' : '#64748B'} />
              </TouchableOpacity>
            </View>

            {renderCalendarGrid()}

            <View style={styles.calendarDialogFooter}>
              <TouchableOpacity
                style={[styles.calendarTodayBtn, { backgroundColor: colors.primarySoft }]}
                onPress={() => {
                  const now = new Date();
                  setPickerYear(now.getFullYear());
                  setPickerMonth(now.getMonth());
                  handleSelectCalendarDate(now.getDate());
                }}
              >
                <Text style={[styles.calendarTodayBtnText, { color: colors.primaryDark }]}>Today</Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[styles.calendarCloseBtn, { backgroundColor: colors.primary }]}
                onPress={() => setCalendarVisible(false)}
              >
                <Text style={styles.calendarCloseBtnText}>Done</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  headerAddBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.primary,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 8,
  },
  headerAddBtnText: {
    fontFamily: fonts.bold,
    fontSize: 13,
    color: '#FFFFFF',
    marginLeft: 4,
  },
  kpiContainer: {
    flexDirection: 'row',
    paddingHorizontal: 16,
    paddingTop: 12,
    gap: 10,
  },
  kpiCard: {
    flex: 1,
    padding: 12,
    borderRadius: 14,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.06,
    shadowRadius: 3,
  },
  kpiTopRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  kpiLabel: {
    fontFamily: fonts.bold,
    fontSize: 10,
    letterSpacing: 0.5,
  },
  kpiIconCircle: {
    width: 26,
    height: 26,
    borderRadius: 13,
    alignItems: 'center',
    justifyContent: 'center',
  },
  kpiValue: {
    fontFamily: fonts.bold,
    fontSize: 20,
    lineHeight: 26,
    marginBottom: 2,
  },
  kpiSubtext: {
    fontFamily: fonts.regular,
    fontSize: 10,
  },
  searchSection: {
    paddingHorizontal: 16,
    paddingTop: 10,
    paddingBottom: 6,
  },
  searchBox: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 10,
    borderWidth: 1,
  },
  searchInput: {
    flex: 1,
    fontFamily: fonts.regular,
    fontSize: 14,
    padding: 0,
  },
  filterPillsRow: {
    flexDirection: 'row',
    marginTop: 10,
    gap: 8,
  },
  filterPill: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 20,
    borderWidth: 1,
  },
  filterPillText: {
    fontFamily: fonts.medium,
    fontSize: 12,
  },
  listContent: {
    padding: 16,
    paddingBottom: 32,
  },
  promoCard: {
    borderRadius: 14,
    borderWidth: 1,
    marginBottom: 14,
    overflow: 'hidden',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 3,
  },
  cardHeader: {
    padding: 14,
  },
  cardBadgeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    flexWrap: 'wrap',
    gap: 8,
    marginBottom: 8,
  },
  discountBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 6,
  },
  discountBadgeText: {
    fontFamily: fonts.bold,
    fontSize: 12,
    color: '#FFFFFF',
  },
  codeBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 6,
  },
  codeBadgeText: {
    fontFamily: fonts.bold,
    fontSize: 11,
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 6,
    marginLeft: 'auto',
  },
  statusDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    marginRight: 5,
  },
  statusText: {
    fontFamily: fonts.bold,
    fontSize: 11,
  },
  cardTitle: {
    fontFamily: fonts.bold,
    fontSize: 16,
    lineHeight: 22,
  },
  cardDetails: {
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderTopWidth: 1,
    gap: 6,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  detailText: {
    fontFamily: fonts.regular,
    fontSize: 13,
  },
  cardActions: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderTopWidth: 1,
  },
  manageItemsBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 7,
    borderRadius: 8,
  },
  manageItemsBtnText: {
    fontFamily: fonts.bold,
    fontSize: 12,
    color: '#FFFFFF',
  },
  storewideTag: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  storewideTagText: {
    fontFamily: fonts.medium,
    fontSize: 12,
  },
  actionIconRow: {
    flexDirection: 'row',
    gap: 8,
  },
  iconButton: {
    width: 34,
    height: 34,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  centerLoading: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: 32,
  },
  loadingText: {
    fontFamily: fonts.regular,
    fontSize: 14,
    marginTop: 10,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    padding: 40,
  },
  emptyTitle: {
    fontFamily: fonts.bold,
    fontSize: 16,
    marginTop: 12,
    marginBottom: 4,
  },
  emptySubtitle: {
    fontFamily: fonts.regular,
    fontSize: 13,
    textAlign: 'center',
    lineHeight: 18,
    marginBottom: 16,
  },
  emptyCreateBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.primary,
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderRadius: 8,
  },
  emptyCreateBtnText: {
    fontFamily: fonts.bold,
    fontSize: 13,
    color: '#FFFFFF',
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalSheet: {
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    maxHeight: '90%',
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 16,
    borderBottomWidth: 1,
  },
  modalHeaderTitle: {
    fontFamily: fonts.bold,
    fontSize: 17,
  },
  modalHeaderSubtitle: {
    fontFamily: fonts.regular,
    fontSize: 12,
    marginTop: 2,
  },
  modalCloseBtn: {
    padding: 4,
  },
  modalBody: {
    paddingHorizontal: 20,
    paddingVertical: 14,
  },
  formGroup: {
    marginBottom: 14,
  },
  formRow: {
    flexDirection: 'row',
  },
  formLabel: {
    fontFamily: fonts.semiBold,
    fontSize: 13,
    marginBottom: 6,
  },
  formInput: {
    fontFamily: fonts.regular,
    fontSize: 14,
    paddingHorizontal: 12,
    paddingVertical: 10,
    borderRadius: 8,
    borderWidth: 1,
  },
  formTextArea: {
    fontFamily: fonts.regular,
    fontSize: 14,
    paddingHorizontal: 12,
    paddingVertical: 10,
    borderRadius: 8,
    borderWidth: 1,
    textAlignVertical: 'top',
    height: 70,
  },
  typeSelectorRow: {
    flexDirection: 'row',
    gap: 10,
  },
  typeSelectorBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    borderRadius: 8,
    borderWidth: 1,
  },
  typeSelectorBtnText: {
    fontFamily: fonts.semiBold,
    fontSize: 13,
  },
  inputWithAddon: {
    flexDirection: 'row',
    alignItems: 'center',
    borderRadius: 8,
    borderWidth: 1,
    paddingHorizontal: 10,
  },
  inputAddonText: {
    fontFamily: fonts.bold,
    fontSize: 14,
    marginRight: 6,
  },
  inputWithAddonField: {
    flex: 1,
    fontFamily: fonts.regular,
    fontSize: 14,
    paddingVertical: 10,
    paddingHorizontal: 0,
  },
  datePickerBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 10,
    paddingVertical: 11,
    borderRadius: 8,
    borderWidth: 1,
  },
  datePickerBtnText: {
    fontFamily: fonts.medium,
    fontSize: 13,
  },
  scopeSelector: {
    gap: 8,
  },
  scopeOption: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderRadius: 10,
  },
  scopeOptionTitle: {
    fontFamily: fonts.semiBold,
    fontSize: 13,
  },
  scopeOptionDesc: {
    fontFamily: fonts.regular,
    fontSize: 11,
    marginTop: 1,
  },
  modalFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    paddingVertical: 14,
    borderTopWidth: 1,
    gap: 12,
  },
  modalCancelBtn: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    borderRadius: 10,
  },
  modalCancelBtnText: {
    fontFamily: fonts.bold,
    fontSize: 14,
  },
  modalSaveBtn: {
    flex: 1.5,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    borderRadius: 10,
  },
  modalSaveBtnText: {
    fontFamily: fonts.bold,
    fontSize: 14,
    color: '#FFFFFF',
  },
  calendarOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.6)',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 20,
  },
  calendarDialog: {
    width: '100%',
    maxWidth: 360,
    borderRadius: 16,
    padding: 16,
    elevation: 5,
  },
  calendarDialogHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  calendarDialogTitle: {
    fontFamily: fonts.bold,
    fontSize: 16,
  },
  calendarBody: {},
  calendarMonthHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  calendarMonthTitle: {
    fontFamily: fonts.bold,
    fontSize: 15,
  },
  calendarMonthNavBtn: {
    padding: 6,
    borderRadius: 6,
  },
  calendarWeekRow: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    marginBottom: 8,
  },
  calendarWeekText: {
    fontFamily: fonts.semiBold,
    fontSize: 12,
    width: 36,
    textAlign: 'center',
  },
  calendarDaysGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-around',
  },
  calendarDay: {
    width: 36,
    height: 36,
    borderRadius: 18,
    alignItems: 'center',
    justifyContent: 'center',
    marginVertical: 2,
  },
  calendarDayEmpty: {
    width: 36,
    height: 36,
    marginVertical: 2,
  },
  calendarDayText: {
    fontFamily: fonts.medium,
    fontSize: 13,
  },
  calendarDialogFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 16,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#E2E8F0',
  },
  calendarTodayBtn: {
    paddingHorizontal: 14,
    paddingVertical: 8,
    borderRadius: 8,
  },
  calendarTodayBtnText: {
    fontFamily: fonts.bold,
    fontSize: 13,
  },
  calendarCloseBtn: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
  },
  calendarCloseBtnText: {
    fontFamily: fonts.bold,
    fontSize: 13,
    color: '#FFFFFF',
  },
});
`;

fs.writeFileSync('c:/react2/saas_pos_app/src/screens/promotions/PromotionsScreen.tsx', updatedPromoScreenContent, 'utf8');
console.log('PromotionsScreen.tsx updated with 3 KPI Cards and Interactive Filtering!');
