// 账本管理相关API
import { myRequest } from '@/common/utils/request';

// 获取账本列表
export const getBooksList = (params = {}) => {
  return myRequest({
    url: '/portalmember/api.BkBook/getList',
    method: 'POST',
    data: params
  })
}

// 获取账本详情
export const getBookDetail = (id) => {
  if (!id) {
    return Promise.reject(new Error('账本ID不能为空'))
  }
  return myRequest({
    url: `/portalmember/api.BkBook/getInfo/${id}`,
    method: 'POST',
	data:{'id':id}
  })
}

// 创建账本
export const createBook = (data) => {
  if (!data || !data.name) {
    return Promise.reject(new Error('账本名称不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.BkBook/add',
    method: 'POST',
    data: data
  })
}

// 更新账本
export const updateBook = (id, data) => {
  if (!id) {
    return Promise.reject(new Error('账本ID不能为空'))
  }
  if (!data || Object.keys(data).length === 0) {
    return Promise.reject(new Error('更新数据不能为空'))
  }
  return myRequest({
    url: `/portalmember/api.BkBook/edit/`,
    method: 'POST',
    data: data
  })
}

// 删除账本
export const deleteBook = (id) => {
  if (!id) {
    return Promise.reject(new Error('账本ID不能为空'))
  }
  return myRequest({
    url: `/portalmember/api.BkBook/del/`,
    method: 'POST',
	data:{'id':id}
  })
}

// 切换当前账本
export const changeBook = (id) => {
  if (!id) {
    return Promise.reject(new Error('账本ID不能为空'))
  }
  return myRequest({
    url: `/portalmember/api.BkBook/change/`,
    method: 'POST',
	data:{'id':id}
  })
}

// 获取账本统计信息
export const getBookStatistics = (id, params = {}) => {
  if (!id) {
    return Promise.reject(new Error('账本ID不能为空'))
  }
  return myRequest({
    url: `/portalmember/api.BkBook/statistics/${id}`,
    method: 'GET',
    data: params
  })
}

// 获取账本成员列表
export const getBookMembers = (id) => {
  if (!id) {
    return Promise.reject(new Error('账本ID不能为空'))
  }
  return myRequest({
    url: `/portalmember/api.BkBook/getBookMembers`,
    method: 'GET'
  })
}

// 添加账本成员
export const addBookMember = (id, memberData) => {
  if (!id) {
    return Promise.reject(new Error('账本ID不能为空'))
  }
  if (!memberData || !memberData.userId) {
    return Promise.reject(new Error('成员信息不能为空'))
  }
  return myRequest({
    url: `/portalmember/api.BkBook/addBookMembers`,
    method: 'POST',
    data: memberData
  })
}

// 移除账本成员
export const removeBookMember = (id, memberId) => {
  if (!id || !memberId) {
    return Promise.reject(new Error('账本ID和成员ID不能为空'))
  }
  return myRequest({
    url: `/portalmember/api.BkBook/delBookMembers`,
    method: 'DELETE'
  })
}

// 导出账本数据
export const exportBookData = (id, format = 'excel') => {
  if (!id) {
    return Promise.reject(new Error('账本ID不能为空'))
  }
  return myRequest({
    url: `/api/books/${id}/export`,
    method: 'GET',
    data: { format }
  })
}

// 默认导出所有API方法
export default {
  getBooksList,
  getBookDetail,
  createBook,
  updateBook,
  deleteBook,
  changeBook,
  getBookStatistics,
  getBookMembers,
  addBookMember,
  removeBookMember,
  exportBookData
}