// 账本相关API接口
import { myRequest } from '@/common/utils/request';

/**
 * 获取账本列表
 */
export function getLedgers(params) {
  return myRequest({
    url: '/api/ledgers',
    method: 'get',
    params
  });
}

/**
 * 创建账本
 */
export function createLedger(data) {
  return myRequest({
    url: '/api/ledgers',
    method: 'post',
    data
  });
}

/**
 * 更新账本
 */
export function updateLedger(id, data) {
  return myRequest({
    url: `/api/ledgers/${id}`,
    method: 'put',
    data
  });
}

/**
 * 删除账本
 */
export function deleteLedger(id) {
  return myRequest({
    url: `/api/ledgers/${id}`,
    method: 'delete'
  });
}

/**
 * 获取账本详情
 */
export function getLedgerDetail(id) {
  return myRequest({
    url: `/api/ledgers/${id}`,
    method: 'get'
  });
}

/**
 * 切换当前账本
 */
export function switchLedger(id) {
  return myRequest({
    url: `/api/ledgers/${id}/switch`,
    method: 'post'
  });
}

export default {
  getLedgers,
  createLedger,
  updateLedger,
  deleteLedger,
  getLedgerDetail,
  switchLedger
};