import React, { useState, useEffect } from 'react';
import {
  StyleSheet,
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
} from 'react-native';
import posApi, { setApiBaseUrl } from '../../services/api';
import storage from '../../services/storage';
import biometricsService from '../../services/biometrics';
import useResponsive from '../../hooks/useResponsive';

export default function LoginScreen({ navigation }) {
  const { isTablet } = useResponsive();
  const [login, setLogin] = useState('');
  const [password, setPassword] = useState('');
  const [serverUrl, setServerUrl] = useState('http://pos.dev.com');
  const [loading, setLoading] = useState(false);
  const [showServerConfig, setShowServerConfig] = useState(false);

  useEffect(() => {
    checkExistingSession();
  }, []);

  const checkExistingSession = async () => {
    const savedUrl = await storage.getBaseUrl();
    if (savedUrl) setServerUrl(savedUrl);

    const token = await storage.getToken();
    const user = await storage.getUser();
    if (token && user) {
      navigation.replace('POSTerminal');
    }
  };

  const handleLogin = async () => {
    if (!login.trim() || !password.trim()) {
      Alert.alert('Required Fields', 'Please enter your username/email and password.');
      return;
    }

    setLoading(true);
    try {
      if (serverUrl) {
        setApiBaseUrl(serverUrl);
        await storage.saveBaseUrl(serverUrl);
      }

      const res = await posApi.login(login, password, serverUrl);

      if (res.success && res.api_token) {
        await storage.saveToken(res.api_token);
        await storage.saveUser(res.user);
        if (res.tenant) await storage.saveTenant(res.tenant);

        // Enable biometrics for future quick login
        await biometricsService.enableBiometrics(login, res.api_token);

        navigation.replace('POSTerminal');
      } else {
        Alert.alert('Login Failed', res.message || 'Invalid credentials.');
      }
    } catch (err) {
      console.error(err);
      Alert.alert(
        'Connection Error',
        err.response?.data?.message || 'Could not connect to BaryaPOS server. Check server URL and network.'
      );
    } finally {
      setLoading(false);
    }
  };

  // Google OAuth Login
  const handleGoogleLogin = async () => {
    setLoading(true);
    try {
      // Simulate Google OAuth token exchange
      const res = await posApi.loginWithGoogle('google_auth_token_simulated');
      if (res.success && res.api_token) {
        await storage.saveToken(res.api_token);
        await storage.saveUser(res.user);
        navigation.replace('POSTerminal');
      } else {
        Alert.alert('Google Auth Failed', res.message || 'Could not verify Google Account.');
      }
    } catch (err) {
      console.error(err);
      Alert.alert('Google Sign-In', 'Google Authentication initiated.');
    } finally {
      setLoading(false);
    }
  };

  // Biometric Auth Login (FaceID / Fingerprint)
  const handleBiometricLogin = async () => {
    const creds = await biometricsService.authenticate();
    if (creds && creds.token) {
      await storage.saveToken(creds.token);
      navigation.replace('POSTerminal');
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.container}
    >
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={[styles.card, isTablet && styles.tabletCard]}>
          {/* Header & Logo */}
          <View style={styles.header}>
            <View style={styles.logoBadge}>
              <Text style={styles.logoIcon}>₱</Text>
            </View>
            <Text style={styles.title}>BaryaPOS Enterprise</Text>
            <Text style={styles.subtitle}>Mobile & Tablet Point of Sale System</Text>
          </View>

          {/* Form */}
          <View style={styles.form}>
            <Text style={styles.label}>Username or Email</Text>
            <TextInput
              style={styles.input}
              placeholder="cashier@store.com"
              placeholderTextColor="#94a3b8"
              value={login}
              onChangeText={setLogin}
              autoCapitalize="none"
            />

            <Text style={styles.label}>Password</Text>
            <TextInput
              style={styles.input}
              placeholder="••••••••"
              placeholderTextColor="#94a3b8"
              value={password}
              onChangeText={setPassword}
              secureTextEntry
            />

            {/* Server Config Toggle */}
            <TouchableOpacity
              onPress={() => setShowServerConfig(!showServerConfig)}
              style={styles.serverToggle}
            >
              <Text style={styles.serverToggleText}>
                ⚙️ {showServerConfig ? 'Hide Server Config' : 'Configure POS Server URL'}
              </Text>
            </TouchableOpacity>

            {showServerConfig && (
              <View style={styles.serverBox}>
                <Text style={styles.label}>Server Base URL</Text>
                <TextInput
                  style={styles.input}
                  placeholder="http://192.168.1.10:8000"
                  placeholderTextColor="#94a3b8"
                  value={serverUrl}
                  onChangeText={setServerUrl}
                  autoCapitalize="none"
                />
              </View>
            )}

            {/* Standard Login */}
            <TouchableOpacity
              style={[styles.loginBtn, loading && styles.disabledBtn]}
              onPress={handleLogin}
              disabled={loading}
            >
              {loading ? (
                <ActivityIndicator color="#ffffff" />
              ) : (
                <Text style={styles.loginBtnText}>LOGIN TO TERMINAL ➔</Text>
              )}
            </TouchableOpacity>

            {/* Divider */}
            <View style={styles.dividerBox}>
              <View style={styles.dividerLine} />
              <Text style={styles.dividerText}>OR SIGN IN WITH</Text>
              <View style={styles.dividerLine} />
            </View>

            {/* Google & Biometrics Row */}
            <View style={styles.altAuthRow}>
              <TouchableOpacity
                style={styles.altBtn}
                onPress={handleGoogleLogin}
              >
                <Text style={styles.altBtnText}>🌐 Google</Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[styles.altBtn, styles.bioBtn]}
                onPress={handleBiometricLogin}
              >
                <Text style={styles.bioBtnText}>👆 Biometrics</Text>
              </TouchableOpacity>
            </View>
          </View>

          {/* Registration Link */}
          <View style={styles.registerRow}>
            <Text style={styles.registerSub}>New to BaryaPOS?</Text>
            <TouchableOpacity onPress={() => navigation.navigate('Register')}>
              <Text style={styles.registerLink}> Register Store / Cashier Account</Text>
            </TouchableOpacity>
          </View>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
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
    maxWidth: 420,
    backgroundColor: '#ffffff',
    borderRadius: 24,
    padding: 28,
    elevation: 8,
  },
  tabletCard: {
    maxWidth: 480,
    padding: 36,
  },
  header: {
    alignItems: 'center',
    marginBottom: 20,
  },
  logoBadge: {
    width: 64,
    height: 64,
    borderRadius: 20,
    backgroundColor: '#059669',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 12,
  },
  logoIcon: {
    color: '#ffffff',
    fontSize: 32,
    fontWeight: '900',
  },
  title: {
    fontSize: 22,
    fontWeight: '800',
    color: '#0f172a',
  },
  subtitle: {
    fontSize: 13,
    color: '#64748b',
    marginTop: 4,
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
  serverToggle: {
    marginVertical: 10,
  },
  serverToggleText: {
    fontSize: 12,
    fontWeight: '700',
    color: '#0284c7',
  },
  serverBox: {
    backgroundColor: '#f0f9ff',
    padding: 12,
    borderRadius: 12,
    marginBottom: 10,
  },
  loginBtn: {
    backgroundColor: '#059669',
    borderRadius: 14,
    paddingVertical: 16,
    alignItems: 'center',
    marginTop: 16,
  },
  disabledBtn: {
    opacity: 0.7,
  },
  loginBtnText: {
    color: '#ffffff',
    fontSize: 15,
    fontWeight: '800',
  },
  dividerBox: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: 18,
  },
  dividerLine: {
    flex: 1,
    height: 1,
    backgroundColor: '#e2e8f0',
  },
  dividerText: {
    fontSize: 10,
    fontWeight: '800',
    color: '#94a3b8',
    marginHorizontal: 10,
  },
  altAuthRow: {
    flexDirection: 'row',
    gap: 10,
  },
  altBtn: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#cbd5e1',
    borderRadius: 12,
    paddingVertical: 12,
    alignItems: 'center',
  },
  altBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: '#0f172a',
  },
  bioBtn: {
    backgroundColor: '#f0fdf4',
    borderColor: '#86efac',
  },
  bioBtnText: {
    color: '#15803d',
    fontWeight: '800',
    fontSize: 13,
  },
  registerRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 20,
  },
  registerSub: {
    fontSize: 12,
    color: '#64748b',
  },
  registerLink: {
    fontSize: 12,
    fontWeight: '800',
    color: '#059669',
  },
});
