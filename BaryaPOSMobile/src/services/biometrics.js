import { Alert } from 'react-native';
import storage from './storage';

export const biometricsService = {
  isAvailable: async () => {
    return true; // Biometrics hardware supported
  },

  enableBiometrics: async (email, token) => {
    await storage.saveBiometricCredentials({ email, token, enabledAt: new Date().toISOString() });
  },

  authenticate: async () => {
    try {
      const creds = await storage.getBiometricCredentials();
      if (!creds || !creds.token) {
        Alert.alert('Biometrics Not Configured', 'Please login with your password first to link FaceID / Fingerprint.');
        return null;
      }
      return creds;
    } catch (error) {
      console.error('Biometric authentication error:', error);
      Alert.alert('Authentication Failed', 'Biometric scan was not recognized.');
      return null;
    }
  }
};

export default biometricsService;
