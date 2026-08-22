import axios from 'axios';
import storage from './storage';

let DEFAULT_BASE_URL = 'http://pos.dev.com/api/v1';

const apiClient = axios.create({
  baseURL: DEFAULT_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 15000,
});

export const setApiBaseUrl = (url) => {
  if (!url) return;
  let formattedUrl = url.trim();
  if (!formattedUrl.endsWith('/api/v1')) {
    formattedUrl = formattedUrl.replace(/\/$/, '') + '/api/v1';
  }
  apiClient.defaults.baseURL = formattedUrl;
};

apiClient.interceptors.request.use(async (config) => {
  const token = await storage.getToken();
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => Promise.reject(error));

export const posApi = {
  // Authentication
  login: async (login, password, customUrl) => {
    if (customUrl) setApiBaseUrl(customUrl);
    const response = await apiClient.post('/auth/login', { login, password });
    return response.data;
  },

  // Google OAuth Login Simulation
  loginWithGoogle: async (googleToken) => {
    const response = await apiClient.post('/auth/google', { google_token: googleToken });
    return response.data;
  },

  // Registration
  register: async (registrationData) => {
    const response = await apiClient.post('/auth/register', registrationData);
    return response.data;
  },

  // Products Catalog
  getProducts: async (params = {}) => {
    const response = await apiClient.get('/pos/products', { params });
    return response.data;
  },

  // Customers CRM & Credit
  getCustomers: async (params = {}) => {
    const response = await apiClient.get('/pos/customers', { params });
    return response.data;
  },

  getCustomerDetails: async (id) => {
    const response = await apiClient.get(`/pos/customers/${id}`);
    return response.data;
  },

  storeCustomer: async (customerData) => {
    const response = await apiClient.post('/pos/customers', {
      tenant_id: customerData.tenant_id,
      name: customerData.name || customerData.CustomerName,
      phone: customerData.phone || customerData.mobile_number,
      address: customerData.address || '',
      tin: customerData.tin || '',
      customer_type: customerData.customer_type || 'Regular',
      credit_limit: customerData.credit_limit || 0,
    });
    return response.data;
  },

  updateCreditLimit: async (customerId, creditLimit) => {
    const response = await apiClient.post(`/pos/customers/${customerId}/credit-limit`, { credit_limit: creditLimit });
    return response.data;
  },

  payUtangCollection: async (collectionData) => {
    const response = await apiClient.post('/pos/customers/collections', collectionData);
    return response.data;
  },

  // Sales & Checkout (100% Coincides with POSApiController DB Schema)
  submitSale: async (saleData) => {
    const payload = {
      tenant_id: saleData.tenant_id,
      cashier_id: saleData.cashier_id,
      customer_id: saleData.customer_id || null,
      subtotal: Number(saleData.subtotal || 0),
      discount: Number(saleData.discount_amount || saleData.discount || 0),
      total: Number(saleData.total_amount || saleData.total || 0),
      discount_type: saleData.discount_type || null,
      discount_holder: saleData.discount_holder || null,
      discount_id_no: saleData.discount_id_no || null,
      payments: [
        {
          method: saleData.payment_method || 'cash',
          amount: Number(saleData.tendered_amount || saleData.total_amount || 0),
        }
      ],
      items: (saleData.items || []).map(item => ({
        product_id: item.product_id || item.id,
        qty: Number(item.qty || 1),
        price: Number(item.price || 0),
      })),
    };

    const response = await apiClient.post('/pos/sales', payload);
    return response.data;
  },

  getSaleDetails: async (id) => {
    const response = await apiClient.get(`/pos/sales/${id}`);
    return response.data;
  },

  // Reports
  getXReading: async () => {
    const response = await apiClient.get('/pos/reports/x-reading');
    return response.data;
  },

  getZReading: async () => {
    const response = await apiClient.get('/pos/reports/z-reading');
    return response.data;
  }
};

export default posApi;
