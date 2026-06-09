// 交易相关API接口
import { myRequest } from '@/common/utils/request';

/**
 * 获取交易列表
 */
export function getTransactions(params) {
  return request({
    url: '/api/transactions',
    method: 'get',
    params
  });
}

/**
 * 创建交易
 */
export function createTransaction(data) {
  return request({
    url: '/api/transactions',
    method: 'post',
    data
  });
}

/**
 * 更新交易
 */
export function updateTransaction(id, data) {
  return request({
    url: `/api/transactions/${id}`,
    method: 'put',
    data
  });
}

/**
 * 删除交易
 */
export function deleteTransaction(id) {
  return request({
    url: `/api/transactions/${id}`,
    method: 'delete'
  });
}

/**
 * 获取交易详情
 */
export function getTransactionDetail(id) {
  return request({
    url: `/api/transactions/${id}`,
    method: 'get'
  });
}

/**
 * 批量导入交易
 */
export function importTransactions(data) {
  return request({
    url: '/api/transactions/import',
    method: 'post',
    data
  });
}

/**
 * 导出交易数据
 */
export function exportTransactions(params) {
  return request({
    url: '/api/transactions/export',
    method: 'get',
    params,
    responseType: 'blob'
  });
}

export default {
  getTransactions,
  createTransaction,
  updateTransaction,
  deleteTransaction,
  getTransactionDetail,
  importTransactions,
  exportTransactions
};