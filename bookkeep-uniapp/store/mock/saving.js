export default {
  // 获取储蓄计划列表
  getPlans() {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          code: 200,
          data: [
            { 
              id: 1,
              name: '购房首付',
              targetAmount: '500000',
              currentSaving: '200000',
              monthlySaving: '10000',
              annualRate: '3.5',
              createdAt: '2023-05-10'
            },
            { 
              id: 2,
              name: '教育基金',
              targetAmount: '200000',
              currentSaving: '50000',
              monthlySaving: '5000',
              annualRate: '3',
              createdAt: '2023-06-15'
            }
          ],
          message: 'success'
        })
      }, 500)
    })
  },

  // 保存储蓄计划
  savePlan(plan) {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          code: 200,
          data: {
            ...plan,
            id: Math.floor(Math.random() * 1000),
            createdAt: new Date().toISOString()
          },
          message: 'success'
        })
      }, 500)
    })
  },

  // 计算储蓄计划
  calculatePlan(params) {
    const { targetAmount, currentSaving, monthlySaving, annualRate } = params
    
    let months = 0
    let total = parseFloat(currentSaving)
    let interest = 0
    const rate = parseFloat(annualRate) / 100 / 12
    const target = parseFloat(targetAmount)
    const monthly = parseFloat(monthlySaving)
    
    while (total < target) {
      months++
      interest += total * rate
      total += monthly + (total * rate)
    }
    
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          code: 200,
          data: {
            months,
            years: Math.floor(months / 12),
            totalSaving: total.toFixed(2),
            totalInterest: interest.toFixed(2)
          },
          message: 'success'
        })
      }, 500)
    })
  }
}