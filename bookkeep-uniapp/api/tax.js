/**
 * 获取税率数据
 * @returns {Promise<Array>} 返回各地区税率数据
 */
export const getTaxRates = async () => {
  // 实际API调用示例 (保留注释)
  // try {
  //   const response = await uni.request({
  //     url: 'https://api.example.com/tax/rates',
  //     method: 'GET'
  //   });
  //   return response.data;
  // } catch (error) {
  //   console.error('获取税率数据失败:', error);
  //   throw error;
  // }
  
  // 使用模拟数据
  return import('@/store/mock/tax').then(module => module.regionTaxData);
};

/**
 * 计算税费
 * @param {Object} params 计算参数
 * @param {number} params.income 收入金额
 * @param {string} params.region 地区
 * @param {boolean} params.includeSocialSecurity 是否包含社保公积金
 * @returns {Promise<Object>} 返回计算结果
 */
export const calculateTax = async (params) => {
  // 实际API调用示例 (保留注释)
  // try {
  //   const response = await uni.request({
  //     url: 'https://api.example.com/tax/calculate',
  //     method: 'POST',
  //     data: params
  //   });
  //   return response.data;
  // } catch (error) {
  //   console.error('计算税费失败:', error);
  //   throw error;
  // }
  
  // 使用模拟数据
  return new Promise(resolve => {
    setTimeout(() => {
      const { income, region, includeSocialSecurity } = params;
      const regionData = require('@/store/mock/tax').regionTaxData.find(r => r.name === region);
      
      // 模拟计算逻辑
      const socialSecurityAmount = includeSocialSecurity ? Math.min(income * 0.22, 3100) : 0;
      const taxableIncome = Math.max(0, income - socialSecurityAmount - 5000);
      let taxAmount = 0;
      
      if (taxableIncome > 0) {
        for (let i = regionData.rates.length - 1; i >= 0; i--) {
          const rate = regionData.rates[i];
          if (taxableIncome > rate.min) {
            taxAmount = taxableIncome * rate.rate / 100 - rate.quickDeduction;
            break;
          }
        }
      }
      
      resolve({
        taxableIncome,
        taxAmount,
        socialSecurityAmount,
        netIncome: income - taxAmount - socialSecurityAmount
      });
    }, 500);
  });
};