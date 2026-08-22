import AsyncStorage from '@react-native-async-storage/async-storage';

const KEYS = {
  TOKEN: '@baryapos_token',
  USER: '@baryapos_user',
  TENANT: '@baryapos_tenant',
  BASE_URL: '@baryapos_base_url',
  CART: '@baryapos_active_cart',
  BIOMETRICS: '@baryapos_biometrics_creds',
  OFFLINE_SALES: '@baryapos_offline_sales',
};

export const storage = {
  saveToken: async (token) => AsyncStorage.setItem(KEYS.TOKEN, token),
  getToken: async () => AsyncStorage.getItem(KEYS.TOKEN),
  removeToken: async () => AsyncStorage.removeItem(KEYS.TOKEN),

  saveUser: async (user) => AsyncStorage.setItem(KEYS.USER, JSON.stringify(user)),
  getUser: async () => {
    const val = await AsyncStorage.getItem(KEYS.USER);
    return val ? JSON.parse(val) : null;
  },

  saveTenant: async (tenant) => AsyncStorage.setItem(KEYS.TENANT, JSON.stringify(tenant)),
  getTenant: async () => {
    const val = await AsyncStorage.getItem(KEYS.TENANT);
    return val ? JSON.parse(val) : null;
  },

  saveBaseUrl: async (url) => AsyncStorage.setItem(KEYS.BASE_URL, url),
  getBaseUrl: async () => AsyncStorage.getItem(KEYS.BASE_URL),

  saveCart: async (cartItems) => AsyncStorage.setItem(KEYS.CART, JSON.stringify(cartItems)),
  getCart: async () => {
    const val = await AsyncStorage.getItem(KEYS.CART);
    return val ? JSON.parse(val) : [];
  },
  clearCart: async () => AsyncStorage.removeItem(KEYS.CART),

  saveBiometricCredentials: async (creds) => AsyncStorage.setItem(KEYS.BIOMETRICS, JSON.stringify(creds)),
  getBiometricCredentials: async () => {
    const val = await AsyncStorage.getItem(KEYS.BIOMETRICS);
    return val ? JSON.parse(val) : null;
  },

  logout: async () => {
    await AsyncStorage.multiRemove([KEYS.TOKEN, KEYS.USER, KEYS.TENANT, KEYS.CART]);
  }
};

export default storage;
