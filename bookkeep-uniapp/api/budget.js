// 预算管理相关API
import request from '@/common/utils/request'

// 获取预算数据
export const getBudgetData = (bookId, periodType = 'month',periodDate = '') => {
  return request({
    url: '/portalmember/api.BkBudget/getBudgetData',
    method: 'POST',
    data: { 
      book_id: bookId,
      period_type: periodType,
	  period_date:periodDate
    }
  })
}

// 保存预算数据
export const saveBudgetData = (data) => {
  return request({
    url: '/portalmember/api.BkBudget/add',
    method: 'POST',
    data
  })
}

// 获取预算列表
export const getBudgetList = (bookId) => {
  return request({
    url: '/api/budget/list',
    method: 'GET',
    data: { book_id: bookId }
  })
}

// 添加预算分类
export const addBudgetCategory = (data) => {
  return request({
    url: '/portalmember/api.BkBudget/addCategory',
    method: 'POST',
    data
  })
}

// 更新预算分类
export const updateBudgetCategory = (data) => {
  return request({
    url: '/portalmember/api.BkBudget/editCategory',
    method: 'POST',
    data
  })
}

// 删除预算
export const deleteBudgetCategory = (id) => {
  return request({
    url: `/portalmember/api.BkBudget/del`,
    method: 'POST',
    data:{'id':id}
  })
}


// 导出API模块
export default {
	getBudgetData,
	saveBudgetData,
	getBudgetList,
	addBudgetCategory,
	updateBudgetCategory,
	deleteBudgetCategory
};