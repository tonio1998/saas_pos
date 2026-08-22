import React, { useState, useEffect } from 'react';
import {
  StyleSheet,
  View,
  Text,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  SafeAreaView,
} from 'react-native';
import posApi from '../../services/api';

export default function ReportsScreen({ navigation }) {
  const [reportType, setReportType] = useState('x-reading');
  const [reportData, setReportData] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    loadReport(reportType);
  }, [reportType]);

  const loadReport = async (type) => {
    setLoading(true);
    try {
      const res = type === 'x-reading' ? await posApi.getXReading() : await posApi.getZReading();
      if (res.success || res.data) {
        setReportData(res.data || res);
      }
    } catch (err) {
      console.error('Fetch report error:', err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.topHeader}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
          <Text style={styles.backBtnText}>◀ Terminal</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>BIR Reports (X/Z-Reading)</Text>
        <View style={{ width: 60 }} />
      </View>

      <View style={styles.tabBar}>
        <TouchableOpacity
          style={[styles.tabBtn, reportType === 'x-reading' && styles.tabBtnActive]}
          onPress={() => setReportType('x-reading')}
        >
          <Text style={[styles.tabText, reportType === 'x-reading' && styles.tabTextActive]}>
            📊 Daily X-Reading
          </Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.tabBtn, reportType === 'z-reading' && styles.tabBtnActive]}
          onPress={() => setReportType('z-reading')}
        >
          <Text style={[styles.tabText, reportType === 'z-reading' && styles.tabTextActive]}>
            🔒 End-of-Day Z-Reading
          </Text>
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.scrollContent}>
        {loading ? (
          <ActivityIndicator size="large" color="#059669" style={{ marginTop: 40 }} />
        ) : (
          <View style={styles.reportCard}>
            <Text style={styles.reportHeaderTitle}>
              {reportType === 'x-reading' ? 'BIR X-READING REPORT' : 'BIR Z-READING END OF DAY'}
            </Text>
            <Text style={styles.reportDate}>Generated: {new Date().toLocaleString()}</Text>
            <View style={styles.divider} />

            <View style={styles.metricRow}>
              <Text style={styles.metricLabel}>Gross Sales:</Text>
              <Text style={styles.metricVal}>₱{Number(reportData?.gross_sales || 0).toFixed(2)}</Text>
            </View>

            <View style={styles.metricRow}>
              <Text style={styles.metricLabel}>Total Discounts:</Text>
              <Text style={styles.metricVal}>-₱{Number(reportData?.total_discounts || 0).toFixed(2)}</Text>
            </View>

            <View style={styles.metricRow}>
              <Text style={styles.metricLabel}>Net Sales:</Text>
              <Text style={[styles.metricVal, styles.netVal]}>₱{Number(reportData?.net_sales || 0).toFixed(2)}</Text>
            </View>

            <View style={styles.divider} />

            <View style={styles.metricRow}>
              <Text style={styles.metricLabel}>Vatable Sales:</Text>
              <Text style={styles.metricVal}>₱{Number(reportData?.vatable_sales || 0).toFixed(2)}</Text>
            </View>

            <View style={styles.metricRow}>
              <Text style={styles.metricLabel}>VAT Amount (12%):</Text>
              <Text style={styles.metricVal}>₱{Number(reportData?.vat_amount || 0).toFixed(2)}</Text>
            </View>

            <View style={styles.metricRow}>
              <Text style={styles.metricLabel}>VAT Exempt Sales:</Text>
              <Text style={styles.metricVal}>₱{Number(reportData?.vat_exempt_sales || 0).toFixed(2)}</Text>
            </View>
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
  tabBar: {
    flexDirection: 'row',
    backgroundColor: '#1e293b',
    padding: 6,
  },
  tabBtn: {
    flex: 1,
    paddingVertical: 10,
    alignItems: 'center',
    borderRadius: 10,
  },
  tabBtnActive: {
    backgroundColor: '#059669',
  },
  tabText: {
    color: '#94a3b8',
    fontWeight: '700',
    fontSize: 13,
  },
  tabTextActive: {
    color: '#ffffff',
  },
  scrollContent: {
    padding: 16,
    alignItems: 'center',
  },
  reportCard: {
    width: '100%',
    maxWidth: 400,
    backgroundColor: '#ffffff',
    borderRadius: 20,
    padding: 20,
  },
  reportHeaderTitle: {
    fontSize: 16,
    fontWeight: '900',
    color: '#0f172a',
    textAlign: 'center',
  },
  reportDate: {
    fontSize: 11,
    color: '#64748b',
    textAlign: 'center',
    marginTop: 2,
  },
  divider: {
    height: 1,
    backgroundColor: '#e2e8f0',
    marginVertical: 14,
  },
  metricRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 8,
  },
  metricLabel: {
    fontSize: 13,
    color: '#475569',
  },
  metricVal: {
    fontSize: 13,
    fontWeight: '700',
    color: '#0f172a',
  },
  netVal: {
    fontSize: 16,
    fontWeight: '900',
    color: '#059669',
  },
});
