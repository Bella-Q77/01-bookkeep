export const mockCategories = {
  // 支出分类
  expense: [
    { id: 'expense-1', name: '餐饮', icon: 'shop' },
    { id: 'expense-2', name: '交通', icon: 'car' },
    { id: 'expense-3', name: '购物', icon: 'cart' },
    { id: 'expense-4', name: '娱乐', icon: 'star' },
    { id: 'expense-5', name: '居家', icon: 'home' },
    { id: 'expense-6', name: '医疗', icon: 'help' },
    { id: 'expense-7', name: '教育', icon: 'book' },
    { id: 'expense-8', name: '旅行', icon: 'paperplane' },
    { id: 'expense-9', name: '服饰', icon: 'shop' },
    { id: 'expense-10', name: '数码', icon: 'phone' },
    { id: 'expense-11', name: '美容', icon: 'heart' },
    { id: 'expense-12', name: '其他', icon: 'more' }
  ],
  
  // 收入分类
  income: [
    { id: 'income-1', name: '工资', icon: 'wallet' },
    { id: 'income-2', name: '奖金', icon: 'gift' },
    { id: 'income-3', name: '投资', icon: 'upload' },
    { id: 'income-4', name: '兼职', icon: 'staff' },
    { id: 'income-5', name: '红包', icon: 'notification' },
    { id: 'income-6', name: '退款', icon: 'undo' },
    { id: 'income-7', name: '其他', icon: 'more' }
  ]
}

export const getMockCategories = (type = 'all') => {
  if (type === 'income') {
    return mockCategories.income
  } else if (type === 'expense') {
    return mockCategories.expense
  }
  return [...mockCategories.income, ...mockCategories.expense]
}

export default {
  mockCategories,
  getMockCategories
}