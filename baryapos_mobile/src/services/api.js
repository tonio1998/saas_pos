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

  // Products Catalog
  getProducts: async (params = {}) => {
    const response = await apiClient.get('/pos/products', { params });
    return response.data;
  },

  // Customers CRM
  getCustomers: async (params = {}) => {
    const response = await apiClient.get('/pos/customers', { params });
    return response.data;
  },

  storeCustomer: async (customerData) => {
    const response = await apiClient.post('/pos/customers', {
      tenant_id: customerData.tenant_id,
      name: customerData.name || customerData.CustomerName,
      phone: customerData.phone || customerData.mobile_number,
      address: customerData.address || '',
      tin: customerData.tin || '',
    });
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
