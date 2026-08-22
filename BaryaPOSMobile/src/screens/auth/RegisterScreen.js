import React, { useState } from 'react';
import {
  StyleSheet,
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  ScrollView,
  SafeAreaView,
} from 'react-native';
import posApi from '../../services/api';
import useResponsive from '../../hooks/useResponsive';

export default function RegisterScreen({ navigation }) {
  const { isTablet } = useResponsive();
  const [businessName, setBusinessName] = useState('');
  const [ownerName, setOwnerName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const handleRegister = async () => {
    if (!businessName.trim() || !ownerName.trim() || !email.trim() || !password.trim()) {
      Alert.alert('Required Fields', 'Please fill in all mandatory registration fields.');
      return;
    }

    if (password !== confirmPassword) {
      Alert.alert('Password Mismatch', 'Password and Confirm Password do not match.');
      return;
    }

    setSubmitting(true);
    try {
      const res = await posApi.register({
        business_name: businessName.trim(),
        name: ownerName.trim(),
        email: email.trim(),
        phone: phone.trim(),
        password: password,
      });

      if (res.success) {
        Alert.alert('Registration Successful 🎉', 'Your BaryaPOS store account has been created! Please log in.', [
          { text: 'OK', onPress: () => navigation.navigate('Login') }
        ]);
      } else {
        Alert.alert('Registration Failed', res.message || 'Could not register store account.');
      }
    } catch (err) {
      console.error('Registration error:', err);
      Alert.alert('Error', err.response?.data?.message || 'Server error during registration.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={[styles.card, isTablet && styles.tabletCard]}>
          {/* Header */}
          <View style={styles.header}>
            <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
              <Text style={styles.backBtnText}>◀ Back to Login</Text>
            </TouchableOpacity>
            <Text style={styles.title}>Register Store / Tenant</Text>
            <Text style={styles.subtitle}>Create your enterprise BaryaPOS account</Text>
          </View>

          <View style={styles.form}>
            <Text style={styles.label}>Business / Store Name *</Text>
            <TextInput
              style={styles.input}
              placeholder="e.g. Aling Nena Supermart"
              placeholderTextColor="#94a3b8"
              value={businessName}
              onChangeText={setBusinessName}
            />

            <Text style={styles.label}>Owner / Manager Full Name *</Text>
            <TextInput
              style={styles.input}
              placeholder="e.g. Juan Cruz"
              placeholderTextColor="#94a3b8"
              value={ownerName}
              onChangeText={setOwnerName}
            />

            <Text style={styles.label}>Email Address *</Text>
            <TextInput
              style={styles.input}
              placeholder="owner@store.com"
              placeholderTextColor="#94a3b8"
              value={email}
              onChangeText={setEmail}
              keyboardType="email-address"
              autoCapitalize="none"
            />

            <Text style={styles.label}>Mobile / Phone Number</Text>
            <TextInput
              style={styles.input}
              placeholder="09171234567"
              placeholderTextColor="#94a3b8"
              value={phone}
              onChangeText={setPhone}
              keyboardType="phone-pad"
            />

            <Text style={styles.label}>Account Password *</Text>
            <TextInput
              style={styles.input}
              placeholder="••••••••"
              placeholderTextColor="#94a3b8"
              value={password}
              onChangeText={setPassword}
              secureTextEntry
            />

            <Text style={styles.label}>Confirm Password *</Text>
            <TextInput
              style={styles.input}
              placeholder="••••••••"
              placeholderTextColor="#94a3b8"
              value={confirmPassword}
              onChangeText={setConfirmPassword}
              secureTextEntry
            />

            <TouchableOpacity
              style={[styles.submitBtn, submitting && styles.disabledBtn]}
              onPress={handleRegister}
              disabled={submitting}
            >
              {submitting ? (
                <ActivityIndicator color="#ffffff" />
              ) : (
                <Text style={styles.submitBtnText}>CREATE STORE ACCOUNT ➔</Text>
              )}
            </TouchableOpacity>
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0f172a',
  },
  scrollContent: {
    flexGrow: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  card: {
    width: '100%',
    maxWidth: 440,
    backgroundColor: '#ffffff',
    borderRadius: 24,
    padding: 28,
  },
  tabletCard: {
    maxWidth: 500,
    padding: 34,
  },
  header: {
    marginBottom: 20,
  },
  backBtn: {
    marginBottom: 12,
  },
  backBtnText: {
    color: '#0284c7',
    fontWeight: '700',
    fontSize: 13,
  },
  title: {
    fontSize: 22,
    fontWeight: '800',
    color: '#0f172a',
  },
  subtitle: {
    fontSize: 13,
    color: '#64748b',
    marginTop: 2,
  },
  form: {
    width: '100%',
  },
  label: {
    fontSize: 12,
    fontWeight: '700',
    color: '#475569',
    textTransform: 'uppercase',
    marginBottom: 6,
    marginTop: 10,
  },
  input: {
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 12,
    fontSize: 15,
    color: '#0f172a',
  },
  submitBtn: {
    backgroundColor: '#059669',
    borderRadius: 14,
    paddingVertical: 16,
    alignItems: 'center',
    marginTop: 24,
  },
  disabledBtn: {
    opacity: 0.7,
  },
  submitBtnText: {
    color: '#ffffff',
    fontSize: 15,
    fontWeight: '800',
  },
});
