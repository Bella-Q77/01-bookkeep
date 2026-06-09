// 资产管理相关API
import myRequest from '@/common/utils/request';

// 获取资产账户类型
export const getAssetsType = (bookId) => {
  return myRequest({
    url: '/portalmember/api.BkAccount/getAccountType',
    method: 'POST',
    data: {
      book_id: bookId || uni.getStorageSync('currentBookId')
    }
  })
}

// 获取资产数据
export const getAssetsData = (bookId) => {
  return myRequest({
    url: '/portalmember/api.BkAccount/getAccountData',
    method: 'POST',
    data: {
      book_id: bookId || uni.getStorageSync('currentBookId')
    }
  })
}

// 添加资产
export const addAsset = (data) => {
  return myRequest({
    url: '/portalmember/api.BkAccount/add',
    method: 'POST',
    data: {
      ...data,
      book_id: uni.getStorageSync('currentBookId')
    }
  })
}

// 修改资产
export const updateAsset = (data) => {
  return myRequest({
    url: '/portalmember/api.BkAccount/edit',
    method: 'POST',
    data: {
      ...data,
      book_id: uni.getStorageSync('currentBookId')
    }
  })
}

// 删除资产
export const deleteAsset = (data) => {
  return myRequest({
    url: '/portalmember/api.BkAccount/del',
    method: 'POST',
    data: {
      ...data,
      book_id: uni.getStorageSync('currentBookId')
    }
  })
}

// 获取资产详情
export const getAssetDetail = (id) => {
  return myRequest({
    url: '/portalmember/api.BkAccount/getDetail',
    method: 'POST',
    data: {
      id: id
    }
  })
}

// 获取资产流水
export const getAssetFlows = (id,data) => {
  return myRequest({
    url: '/portalmember/api.BkAccount/getFlows',
    method: 'POST',
    data: {
		...data,
		id:id,
	   book_id: uni.getStorageSync('currentBookId')
    }
  })
}

// 辅助函数：获取资产图标
function getAssetIcon(type) {
  const icons = {
    '现金': '💰',
    '银行卡': '💳',
    '支付宝': '📱',
    '微信钱包': '💬',
    '投资账户': '📈',
    '其他': '🏦'
  }
  return icons[type] || '🏦'
}

// 默认导出所有API方法
export default {
  getAssetsType,
  getAssetsData,
  addAsset,
  updateAsset,
  deleteAsset,
  getAssetDetail,
  getAssetFlows
};