import { myRequest } from '@/common/utils/request';

/**
 * 获取记录列表
 * @param {Object} params - 查询参数
 * @param {number} params.page - 页码
 * @param {number} params.pageSize - 每页条数
 * @param {string} params.ledgerId - 账本ID
 * @param {string} params.month - 月份，格式：YYYY-MM
 * @param {string} params.type - 收支类型（income/expense）
 * @param {string} params.category - 分类
 * @param {string} params.keyword - 关键词
 * @param {string} params.startDate - 开始日期
 * @param {string} params.endDate - 结束日期
 * @returns {Promise} 返回记录列表
 */
export function getRecords(params = {}) {
  // 构建查询参数
  const queryParams = {
    pageNum: params.pageNum || 1,
    pageSize: params.pageSize || 20,
    ...params
  };
  
  // 调用真实API
  return myRequest({
    url: '/portalmember/api.BkTransaction/getList',
    method: 'POST',
    data: queryParams,
    loading: false, // 页面已有加载状态，这里不重复显示
    toast: false    // 错误时由页面统一处理
  }).then(response => {
    // 适配后端返回的数据结构
	const data = response.data || {};
    return {
      records: data.data || [],
      pagination: {
        pageNum: data.current_page || params.pageNum || 1,
        pageSize: response.per_page || params.pageSize || 20,
        total: response.total || 0,
        hasMore: response.data?.hasMore !== false
      }
    };
  });
}

/**
 * 获取月度统计数据
 * @param {Object} params - 查询参数
 * @param {string} params.ledgerId - 账本ID
 * @param {string} params.month - 月份，格式：YYYY-MM
 * @returns {Promise} 返回月度统计数据
 */
export function getMonthSummary(params = {}) {
  // 调用真实API
  return myRequest({
    url: '/portalmember/api.BkTransaction/getMonthSummary',
    method: 'POST',
    data: params,
    loading: false, // 页面已有加载状态，这里不重复显示
    toast: false    // 错误时由页面统一处理
  }).then(response => {
    // 适配后端返回的数据结构
    const data = response.data || {};
    return {
      income: data.income_amount || '0.00',
      expense: data.expense_amount || '0.00',
      balance: data.balance_amount || '0.00'
    };
  });
}

/**
 * 添加记录
 * @param {Object} data - 记录数据
 * @returns {Promise} 返回添加结果
 */
export function addRecord(data) {

  // 调用真实API
  return myRequest({
    url: '/portalmember/api.BkTransaction/add',
    method: 'POST',
    data: data
  }).then(response => {
	  
	  return response;
	  
  });
}

/**
 * 删除记录
 * @param {number|string} id - 记录ID
 * @returns {Promise} 返回删除结果
 */
export function deleteRecord(id) {
  // 调用真实API
  return myRequest({
    url: `/portalmember/api.BkTransaction/del`,
    method: 'POST',
	data: {'id':id}
  }).then(response => {
    return response;
  });
}

/**
 * 获取记录详情
 * @param {number|string} id - 记录ID
 * @returns {Promise} 返回记录详情
 */
export function getRecordDetail(id) {

  // 调用真实API
  return myRequest({
    url: '/portalmember/api.BkTransaction/getInfo',
    method: 'POST',
    data: {'id':id}
  }).then(response => {
	  
	  return response;
	  
  });
}

/**
 * 更新记录
 * @param {Object} data - 记录数据
 * @returns {Promise} 返回更新结果
 */
export function updateRecord(data) {
  // 调用真实API
  return myRequest({
    url: '/portalmember/api.BkTransaction/edit',
    method: 'POST',
    data: data
  }).then(response => {
    return response;
  });
}

//获得账单统计功能
export const getStatInfo = (listtype, data) => {
	data.listtype = listtype;
	return myRequest({
		url: '/portalmember/api.BkStat/getStatInfo',
		method: 'POST',
		data: data
	})
}


export default {
  getRecords,
  getMonthSummary,
  addRecord,
  getRecordDetail,
  deleteRecord,
  updateRecord,
  getStatInfo
};