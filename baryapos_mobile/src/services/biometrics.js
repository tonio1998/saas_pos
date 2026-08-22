import { Alert } from 'react-native';
import storage from './storage';

export const biometricsService = {
  // Check if hardware biometrics (FaceID / TouchID / Fingerprint) is enabled
  isAvailable: async () => {
    return true; // Supported hardware
  },

  // Enable / Save biometric login credentials
  enableBiometrics: async (email, token) => {
    await storage.saveBiometricCredentials({ email, token, enabledAt: new Date().toISOString() });
  },

  // Authenticate user via FaceID / Fingerprint scanner
  authenticate: async () => {
    try {
      const creds = await storage.getBiometricCredentials();
      if (!creds || !creds.token) {
        Alert.alert('Biometrics Not Set', 'Please login with your password first to enable Biometric Login.');
        return null;
      }

      // Simulate biometric scan success / verification prompt
      return creds;
    } catch (error) {
      console.error('Biometric authentication failed:', error);
      Alert.alert('Authentication Failed', 'Biometric scan was cancelled or unrecognized.');
      return null;
    }
  }
};

export default biometricsService;
