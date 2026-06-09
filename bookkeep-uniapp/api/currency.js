import { getCurrencyRates, convertCurrency, getSupportedCurrencies } from '@/store/mock/currency';

// 获取汇率数据
// 实际项目中可以使用以下API:
// - https://api.exchangerate-api.com/v4/latest/USD
// - https://api.exchangeratesapi.io/latest?base=USD
// - https://open.er-api.com/v6/latest/USD
export const fetchCurrencyRates = async (baseCurrency = 'USD') => {
  try {
    // 使用模拟数据
    return await getCurrencyRates(baseCurrency);
    
    // 实际API调用示例:
    // const response = await uni.request({
    //   url: `https://api.exchangerate-api.com/v4/latest/${baseCurrency}`,
    //   method: 'GET'
    // });
    // return response.data;
  } catch (error) {
    console.error('获取汇率数据失败:', error);
    throw error;
  }
};

// 获取支持的货币列表
export const fetchSupportedCurrencies = () => {
  try {
    // 使用模拟数据
    return getSupportedCurrencies();
    
    // 实际API调用示例:
    // const response = await uni.request({
    //   url: 'https://api.exchangerate-api.com/v4/currencies',
    //   method: 'GET'
    // });
    // return response.data;
  } catch (error) {
    console.error('获取支持的货币列表失败:', error);
    throw error;
  }
};

// 货币转换
export const convertCurrencyAmount = (amount, fromCurrency, toCurrency) => {
  try {
    // 使用模拟数据
    return convertCurrency(amount, fromCurrency, toCurrency);
    
    // 实际项目中可以直接使用汇率数据计算
    // const rates = await fetchCurrencyRates(fromCurrency);
    // return amount * rates.rates[toCurrency];
  } catch (error) {
    console.error('货币转换失败:', error);
    throw error;
  }
};