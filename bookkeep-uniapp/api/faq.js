// 意见反馈相关API
import { myRequest } from '@/common/utils/request';

// 获取反馈列表
export const getFeedbackList = (params = {}) => {
  return myRequest({
    url: '/portalmember/api.AskFaq/getList',
    method: 'POST',
    data: params
  })
}

// 获取反馈详情
export const getFeedbackDetail = (id) => {
  if (!id) {
    return Promise.reject(new Error('反馈ID不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.AskFaq/getInfo',
    method: 'POST',
    data: { 'id': id }
  })
}

// 提交反馈
export const submitFeedback = (data) => {
  if (!data || !data.name) {
    return Promise.reject(new Error('反馈标题不能为空'))
  }
  if (!data.content) {
    return Promise.reject(new Error('反馈内容不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.AskFaq/add',
    method: 'POST',
    data: data
  })
}

// 更新反馈
export const updateFeedback = (id, data) => {
  if (!id) {
    return Promise.reject(new Error('反馈ID不能为空'))
  }
  if (!data || Object.keys(data).length === 0) {
    return Promise.reject(new Error('更新数据不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.AskFaq/edit',
    method: 'POST',
    data: { ...data, id }
  })
}

// 删除反馈
export const deleteFeedback = (id) => {
  if (!id) {
    return Promise.reject(new Error('反馈ID不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.AskFaq/del',
    method: 'POST',
    data: { 'id': id }
  })
}

// 获取反馈统计
export const getFeedbackStatistics = (params = {}) => {
  return myRequest({
    url: '/portalmember/api.AskFaq/statistics',
    method: 'POST',
    data: params
  })
}

// 回复反馈
export const replyFeedback = (faqId, replyData) => {
  if (!faqId) {
    return Promise.reject(new Error('反馈ID不能为空'))
  }
  if (!replyData || !replyData.content) {
    return Promise.reject(new Error('回复内容不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.AskComment/add',
    method: 'POST',
    data: { ...replyData, faq_id: faqId }
  })
}

// 获取反馈回复列表
export const getFeedbackReplies = (faqId, params = {}) => {
  if (!faqId) {
    return Promise.reject(new Error('反馈ID不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.AskComment/getList',
    method: 'POST',
    data: { ...params, faq_id: faqId }
  })
}

// 删除回复
export const deleteReply = (replyId) => {
  if (!replyId) {
    return Promise.reject(new Error('回复ID不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.AskComment/del',
    method: 'POST',
    data: { 'id': replyId }
  })
}

// 标记反馈为已读
export const markFeedbackAsRead = (id) => {
  if (!id) {
    return Promise.reject(new Error('反馈ID不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.AskFaq/markRead',
    method: 'POST',
    data: { 'id': id }
  })
}

// 获取FAQ分类列表
export const getFaqCategories = (params = {}) => {
  return myRequest({
    url: '/portalmember/api.AskFaq/categories',
    method: 'POST',
    data: params
  })
}

// 添加评论
export const addComment = (data) => {
  if (!data || !data.bus_id) {
    return Promise.reject(new Error('业务ID不能为空'))
  }
  if (!data.content) {
    return Promise.reject(new Error('评论内容不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.AskComment/add',
    method: 'POST',
    data: data
  })
}

// 添加回复
export const addReply = (data) => {
  if (!data || !data.comment_id) {
    return Promise.reject(new Error('评论ID不能为空'))
  }
  if (!data.content) {
    return Promise.reject(new Error('回复内容不能为空'))
  }
  return myRequest({
    url: '/portalmember/api.AskComment/addReply',
    method: 'POST',
    data: data
  })
}

// 默认导出所有API方法
export default {
  getFeedbackList,
  getFeedbackDetail,
  submitFeedback,
  updateFeedback,
  deleteFeedback,
  getFeedbackStatistics,
  replyFeedback,
  getFeedbackReplies,
  deleteReply,
  markFeedbackAsRead,
  getFaqCategories,
  addComment,
  addReply
}