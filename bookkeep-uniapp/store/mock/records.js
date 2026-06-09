export const mockRecords = {
  // 收入记录
  income: [
    { 
      id: 1, 
      type: 'income', 
      category: '工资', 
      amount: '15000.00', 
      date: '2025-09-01', 
      remark: '本月工资',
      categoryId: 'income-1',
      categoryIcon: 'wallet'
    },
    { 
      id: 2, 
      type: 'income', 
      category: '奖金', 
      amount: '5000.00', 
      date: '2025-09-15', 
      remark: '季度奖金',
      categoryId: 'income-2',
      categoryIcon: 'gift'
    },
    { 
      id: 3, 
      type: 'income', 
      category: '投资', 
      amount: '1200.50', 
      date: '2025-09-20', 
      remark: '股票收益',
      categoryId: 'income-3',
      categoryIcon: 'trending-up'
    }
  ],
  
  // 支出记录
  expense: [
    { 
      id: 4, 
      type: 'expense', 
      category: '餐饮', 
      amount: '150.00', 
      date: '2025-09-05', 
      remark: '午餐',
      categoryId: 'expense-1',
      categoryIcon: 'restaurant'
    },
    { 
      id: 5, 
      type: 'expense', 
      category: '交通', 
      amount: '300.00', 
      date: '2025-09-10', 
      remark: '地铁卡充值',
      categoryId: 'expense-2',
      categoryIcon: 'train'
    },
    { 
      id: 6, 
      type: 'expense', 
      category: '购物', 
      amount: '899.00', 
      date: '2025-09-12', 
      remark: '衣服',
      categoryId: 'expense-3',
      categoryIcon: 'shopping-cart'
    },
    { 
      id: 7, 
      type: 'expense', 
      category: '医疗', 
      amount: '350.00', 
      date: '2025-09-18', 
      remark: '药品',
      categoryId: 'expense-4',
      categoryIcon: 'medical'
    }
  ]
}

export const getMockRecords = (params) => {
  const { type, category, month, keyword } = params || {}
  let records = []
  
  // 合并收入支出记录
  if (!type || type === 'all') {
    records = [...mockRecords.income, ...mockRecords.expense]
  } else if (type === 'income') {
    records = [...mockRecords.income]
  } else if (type === 'expense') {
    records = [...mockRecords.expense]
  }
  
  // 按分类筛选
  if (category && category !== '全部') {
    records = records.filter(r => r.category === category)
  }
  
  // 按月份筛选
  if (month) {
    records = records.filter(r => r.date.startsWith(month))
  }
  
  // 按关键字筛选
  if (keyword) {
    const lowerKeyword = keyword.toLowerCase()
    records = records.filter(r => 
      r.remark?.toLowerCase().includes(lowerKeyword) || 
      r.category?.toLowerCase().includes(lowerKeyword)
    )
  }
  
  // 模拟分页
  const page = params?.page || 1
  const pageSize = params?.pageSize || 20
  const start = (page - 1) * pageSize
  const end = start + pageSize
  const paginatedRecords = records.slice(start, end)
  
  return {
    records: paginatedRecords,
    pagination: {
      page,
      pageSize,
      total: records.length,
      hasMore: end < records.length
    }
  }
}

// 月度统计模拟数据
export const mockMonthSummary = {
  income: '21200.50',
  expense: '1699.00',
  balance: '19501.50'
}

// 生成分页记录数据
export const generatePagedRecords = (page = 1, pageSize = 20) => {
  // 合并所有记录并按日期排序
  const allRecords = [...mockRecords.income, ...mockRecords.expense]
    .sort((a, b) => new Date(b.date) - new Date(a.date))
  
  // 分页处理
  const start = (page - 1) * pageSize
  const end = start + pageSize
  const paginatedRecords = allRecords.slice(start, end)
  
  return {
    records: paginatedRecords,
    pagination: {
      page,
      pageSize,
      total: allRecords.length,
      hasMore: end < allRecords.length
    }
  }
}