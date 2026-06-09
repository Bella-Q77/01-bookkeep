// 汇率换算模拟数据
// 基准货币: USD (美元)
export const mockCurrencyRates = {
  timestamp: new Date().getTime(),
  base: 'USD',
  rates: {
    USD: 1,          // 美元
    CNY: 7.12,       // 人民币
    EUR: 0.92,       // 欧元
    GBP: 0.78,       // 英镑
    JPY: 149.32,     // 日元
    HKD: 7.81,       // 港币
    KRW: 1345.67,    // 韩元
    AUD: 1.52,       // 澳元
    CAD: 1.36,       // 加元
    SGD: 1.35,       // 新加坡元
    CHF: 0.89,       // 瑞士法郎
    THB: 35.67,      // 泰铢
    MYR: 4.65,       // 马来西亚林吉特
    RUB: 91.23,      // 俄罗斯卢布
    INR: 83.45,      // 印度卢比
    NZD: 1.67,       // 新西兰元
    MXN: 17.89,      // 墨西哥比索
    BRL: 5.43,       // 巴西雷亚尔
    ZAR: 18.76,      // 南非兰特
    TRY: 32.54       // 土耳其里拉
  }
};

// 货币信息
export const currencyInfo = {
  USD: { name: '美元', symbol: '$', flag: '🇺🇸' },
  CNY: { name: '人民币', symbol: '¥', flag: '🇨🇳' },
  EUR: { name: '欧元', symbol: '€', flag: '🇪🇺' },
  GBP: { name: '英镑', symbol: '£', flag: '🇬🇧' },
  JPY: { name: '日元', symbol: '¥', flag: '🇯🇵' },
  HKD: { name: '港币', symbol: 'HK$', flag: '🇭🇰' },
  KRW: { name: '韩元', symbol: '₩', flag: '🇰🇷' },
  AUD: { name: '澳元', symbol: 'A$', flag: '🇦🇺' },
  CAD: { name: '加元', symbol: 'C$', flag: '🇨🇦' },
  SGD: { name: '新加坡元', symbol: 'S$', flag: '🇸🇬' },
  CHF: { name: '瑞士法郎', symbol: 'Fr', flag: '🇨🇭' },
  THB: { name: '泰铢', symbol: '฿', flag: '🇹🇭' },
  MYR: { name: '马来西亚林吉特', symbol: 'RM', flag: '🇲🇾' },
  RUB: { name: '俄罗斯卢布', symbol: '₽', flag: '🇷🇺' },
  INR: { name: '印度卢比', symbol: '₹', flag: '🇮🇳' },
  NZD: { name: '新西兰元', symbol: 'NZ$', flag: '🇳🇿' },
  MXN: { name: '墨西哥比索', symbol: 'Mex$', flag: '🇲🇽' },
  BRL: { name: '巴西雷亚尔', symbol: 'R$', flag: '🇧🇷' },
  ZAR: { name: '南非兰特', symbol: 'R', flag: '🇿🇦' },
  TRY: { name: '土耳其里拉', symbol: '₺', flag: '🇹🇷' }
};

// 获取所有支持的货币
export const getSupportedCurrencies = () => {
  return Object.keys(currencyInfo).map(code => ({
    code,
    ...currencyInfo[code]
  }));
};

// 获取汇率数据
export const getCurrencyRates = (baseCurrency = 'USD') => {
  // 模拟API调用延迟
  return new Promise((resolve) => {
    setTimeout(() => {
      if (baseCurrency === 'USD') {
        resolve({
          success: true,
          timestamp: mockCurrencyRates.timestamp,
          base: mockCurrencyRates.base,
          rates: mockCurrencyRates.rates
        });
      } else {
        // 如果基准货币不是USD，需要转换汇率
        const baseRate = mockCurrencyRates.rates[baseCurrency];
        const convertedRates = {};
        
        Object.keys(mockCurrencyRates.rates).forEach(currency => {
          convertedRates[currency] = mockCurrencyRates.rates[currency] / baseRate;
        });
        
        resolve({
          success: true,
          timestamp: mockCurrencyRates.timestamp,
          base: baseCurrency,
          rates: convertedRates
        });
      }
    }, 300);
  });
};

// 货币转换计算
export const convertCurrency = (amount, fromCurrency, toCurrency) => {
  if (!amount || !fromCurrency || !toCurrency) {
    return 0;
  }
  
  const fromRate = mockCurrencyRates.rates[fromCurrency];
  const toRate = mockCurrencyRates.rates[toCurrency];
  
  if (!fromRate || !toRate) {
    return 0;
  }
  
  // 先转换为USD，再转换为目标货币
  const amountInUSD = amount / fromRate;
  const result = amountInUSD * toRate;
  
  return result;
};