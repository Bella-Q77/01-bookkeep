<template>
  <view class="faq-detail-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="反馈详情" 
      left-icon="left" 
      :status-bar="true" 
      @click-left="goBack"
    >
      <view slot="left" @click="goBack" style="padding: 0 20rpx;">
        <uni-icons type="left" size="24" color="#000000"></uni-icons>
      </view>
    </uni-nav-bar>
    
    <view class="detail-container" v-if="feedbackDetail">
      <!-- 反馈主体内容 -->
      <view class="main-question">
        <view class="question-header">
          <view class="question-title">{{ feedbackDetail.name }}</view>
          <view class="question-meta">
            <text class="question-status"  :class="statusClass">
              {{ getStatusText(feedbackDetail.status || feedbackDetail.type_id) }}
            </text>
            <text class="question-time">{{ formatTime(feedbackDetail.create_time) }}</text>
          </view>
        </view>
        
        <view class="question-content">
          <view class="question-author">
            <text class="author-name">{{ feedbackDetail.member_info && feedbackDetail.member_info.nickname || '用户' }}</text>
            <text class="author-tag">提问者</text>
          </view>
          <view class="question-text">{{ feedbackDetail.content }}</view>
        </view>
      </view>
      <!-- 评论列表 -->
      <view class="comment-section" v-if="feedbackDetail.comment_list && feedbackDetail.comment_list.length > 0">
        <view class="section-title">
          <text class="title-text">回复与讨论</text>
          <text class="comment-count">{{ feedbackDetail.comment_list.length }}条回复</text>
        </view>
        
        <view class="comment-list">
          <view 
            class="comment-item" 
            v-for="(comment, index) in feedbackDetail.comment_list" 
            :key="index"
          >
            <view class="comment-main">
              <view class="comment-header">
                <view class="comment-author">
                  <text class="author-name">{{ comment.comment_user_name }}</text>
                  <text 
                    class="author-role" 
                    :class="comment.comment_user_id === -1 ? 'staff' : 'user'"
                  >
                    {{ comment.comment_user_id === -1 ? '官方客服' : '用户' }}
                  </text>
                </view>
                <text class="comment-time">{{ formatTime(comment.create_time) }}</text>
              </view>
              
              <view class="comment-content">
                <text class="content-text">{{ comment.content }}</text>
              </view>
              
              <view class="comment-actions">
                <view class="reply-btn" @click="toggleReply(comment.id)">
                  <uni-icons type="chat" size="14" color="#999"></uni-icons>
                  <text class="reply-text">回复</text>
                </view>
              </view>
            </view>
            
            <!-- 回复列表 -->
            <view class="reply-list" v-if="comment.reply_list && comment.reply_list.length > 0">
              <view 
                class="reply-item" 
                v-for="(reply, replyIndex) in comment.reply_list" 
                :key="replyIndex"
              >
                <view class="reply-header">
                  <view class="reply-author">
                    <text class="author-name">{{ reply.reply_user_name }}</text>
                    <text class="reply-arrow">→</text>
                    <text class="receive-name">{{ reply.receive_user_name }}</text>
                  </view>
                  <text class="reply-time">{{ formatTime(reply.create_time) }}</text>
                </view>
                <view class="reply-content">
                  <text class="content-text">{{ reply.content }}</text>
                </view>
              </view>
            </view>
            
            <!-- 回复输入框 -->
            <view class="reply-input-container" v-if="showReplyInput === comment.id">
              <view class="reply-input">
                <input 
                  v-model="replyContents[comment.id]" 
                  :placeholder="'回复给' + comment.comment_user_name"
                  class="reply-input-field"
                  maxlength="200"
                />
                <view class="reply-actions">
                  <text class="cancel-btn" @click="cancelReply(comment.id)">取消</text>
                  <text 
                    class="submit-btn" 
                    :class="{ 'disabled': !replyContents[comment.id] || !replyContents[comment.id].trim() }"
                    @click="submitReply(comment)"
                  >
                    发送
                  </text>
                </view>
              </view>
            </view>
          </view>
        </view>
      </view>
      
      <!-- 空评论状态 -->
      <view class="empty-comments" v-else>
        <text class="empty-icon">💬</text>
        <text class="empty-text">暂无回复与讨论</text>
      </view>
      
      <!-- 添加新评论 -->
      <view class="add-comment-section">
        <view class="comment-input-container">
          <textarea 
            v-model="newCommentContent" 
            placeholder="添加您的评论或回复..."
            class="comment-input"
            maxlength="200"
            @input="validateComment"
          />
          <view class="comment-actions">
            <text class="char-count">{{ newCommentLength }}/200</text>
            <text 
              class="submit-comment-btn" 
              :class="{ 'disabled': !isCommentValid || submittingComment }"
              @click="submitComment"
            >
              {{ submittingComment ? '发送中...' : '发送评论' }}
            </text>
          </view>
        </view>
      </view>
    </view>
    
    <view class="loading-container" v-else>
      <text>加载中...</text>
    </view>
  </view>
</template>

<script>
// 导入真实API接口
import faqApi from '@/api/faq.js'

export default {
  data() {
    return {
      feedbackDetail: null,
      // 新评论相关数据
      newCommentContent: '',
      submittingComment: false,
      isCommentValid: false,
      // 回复相关数据
      showReplyInput: null,
      replyContents: {}
    }
  },
  computed: {
    newCommentLength() {
      return this.newCommentContent.length
    },
    
    // 计算状态样式类名（微信小程序兼容方案）
    statusClass() {
      if (!this.feedbackDetail) return ''
      const statusValue = this.feedbackDetail.status || 0
      const classMap = {
        0: 'status-pending',
        1: 'status-processing',
        2: 'status-replied',
        3: 'status-resolved',
        4: 'status-closed'
      }
      return classMap[statusValue] || ''
    }
  },
  onLoad(option) {
    // 通过ID获取反馈详情
    if (option.id) {
      this.loadFeedbackDetail(option.id)
    } else {
      uni.showToast({
        title: '参数错误',
        icon: 'none'
      })
      setTimeout(() => {
        uni.navigateBack()
      }, 1500)
    }
  },
  methods: {
    // 获取状态文本
    getStatusText(status) {
      const statusMap = {
        0: '待处理',
        1: '处理中',
        2: '已回复',
        3: '已解决',
        4: '已关闭'
      }
      return statusMap[status] || '未知状态'
    },
    
    // 获取状态样式类
    getStatusClassByValue(status) {
      const statusValue = status || 0
      const classMap = {
        0: 'status-pending',
        1: 'status-processing',
        2: 'status-replied',
        3: 'status-resolved',
        4: 'status-closed'
      }
      return classMap[statusValue] || ''
    },
    
    // 格式化时间
    formatTime(timeString) {
      if (!timeString) return '未知时间'
      
      const date = new Date(timeString)
      const now = new Date()
      const diff = now - date
      
      if (isNaN(date.getTime())) return '未知时间'
      
      if (diff < 60 * 1000) {
        return '刚刚'
      } else if (diff < 60 * 60 * 1000) {
        return Math.floor(diff / (60 * 1000)) + '分钟前'
      } else if (diff < 24 * 60 * 60 * 1000) {
        return Math.floor(diff / (60 * 60 * 1000)) + '小时前'
      } else {
        const year = date.getFullYear()
        const month = (date.getMonth() + 1).toString().padStart(2, '0')
        const day = date.getDate().toString().padStart(2, '0')
        const hour = date.getHours().toString().padStart(2, '0')
        const minute = date.getMinutes().toString().padStart(2, '0')
        return `${year}-${month}-${day} ${hour}:${minute}`
      }
    },
    
    // 加载反馈详情
    async loadFeedbackDetail(id) {
      try {
        // 调用真实API获取详情
        const result = await faqApi.getFeedbackDetail(id)
        
        if (result.code === 1) {
          this.feedbackDetail = result.data
        } else {
          uni.showToast({
            title: result.message || '获取反馈详情失败',
            icon: 'none'
          })
        }
      } catch (error) {
        console.error('获取反馈详情失败:', error)
        uni.showToast({
          title: '网络错误，请重试',
          icon: 'none'
        })
      }
    },
    
    // 返回上一页
    goBack() {
      uni.navigateBack()
    },
    
    // 验证评论内容
    validateComment() {
      this.isCommentValid = this.newCommentContent.trim().length > 0 && 
                          this.newCommentContent.length <= 200
    },
    
    // 提交新评论
    async submitComment() {
      if (!this.isCommentValid || this.submittingComment) return
      
      this.submittingComment = true
      
      try {
        uni.showLoading({ title: '发送中...' })
        
        // 调用API添加评论
        const result = await faqApi.addComment({
          bus_id: this.feedbackDetail.id,
          bus_type: 'ask_faq',
          content: this.newCommentContent.trim()
        })
        
        if (result.code === 1) {
          // 成功提交，重新加载详情
          await this.loadFeedbackDetail(this.feedbackDetail.id)
          
          // 清空输入框
          this.newCommentContent = ''
          
          uni.hideLoading()
          uni.showToast({
            title: '评论成功',
            icon: 'success'
          })
        } else {
          throw new Error(result.message || '评论失败')
        }
        
      } catch (error) {
        console.error('提交评论失败:', error)
        uni.hideLoading()
        uni.showToast({
          title: error.message || '评论失败，请重试',
          icon: 'none'
        })
      } finally {
        this.submittingComment = false
      }
    },
    
    // 切换回复输入框显示
    toggleReply(commentId) {
      if (this.showReplyInput === commentId) {
        this.showReplyInput = null
        this.replyContents[commentId] = ''
      } else {
        this.showReplyInput = commentId
        this.replyContents[commentId] = ''
      }
    },
    
    // 取消回复
    cancelReply(commentId) {
      this.showReplyInput = null
      this.replyContents[commentId] = ''
    },
    
    // 提交回复
    async submitReply(comment) {
      const replyContent = this.replyContents[comment.id] && this.replyContents[comment.id].trim()
      if (!replyContent) return
      
      try {
        uni.showLoading({ title: '发送中...' })
        
        // 调用API添加回复
        const result = await faqApi.addReply({
          comment_id: comment.id,
          bus_id: this.feedbackDetail.id,
          bus_type: 'ask_faq',
          content: replyContent,
          receive_user_id: comment.comment_user_id
        })
        
        if (result.code === 1) {
          // 成功提交，重新加载详情
          await this.loadFeedbackDetail(this.feedbackDetail.id)
          
          // 关闭回复输入框
          this.showReplyInput = null
          this.replyContents[comment.id] = ''
          
          uni.hideLoading()
          uni.showToast({
            title: '回复成功',
            icon: 'success'
          })
        } else {
          throw new Error(result.message || '回复失败')
        }
        
      } catch (error) {
        console.error('提交回复失败:', error)
        uni.hideLoading()
        uni.showToast({
          title: error.message || '回复失败，请重试',
          icon: 'none'
        })
      }
    }
  }
}
</script>

<style scoped>
.faq-detail-page {
  min-height: 100vh;
  background-color: #f5f5f5;
  overflow-x: hidden;
}

.detail-container {
  padding: 20rpx;
  box-sizing: border-box;
  max-width: 100%;
}

/* 问题主体样式 */
.main-question {
  background-color: white;
  border-radius: 16rpx;
  padding: 30rpx;
  margin-bottom: 20rpx;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  box-sizing: border-box;
  max-width: 100%;
}

.question-header {
  margin-bottom: 24rpx;
}

.question-title {
  font-size: 32rpx;
  font-weight: bold;
  color: #333;
  line-height: 1.4;
  margin-bottom: 16rpx;
}

.question-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.question-status {
  font-size: 22rpx;
  padding: 8rpx 16rpx;
  border-radius: 20rpx;
  font-weight: 500;
}

.question-time {
  font-size: 22rpx;
  color: #999;
}

.question-content {
  border-top: 1rpx solid #f0f0f0;
  padding-top: 24rpx;
}

.question-author {
  display: flex;
  align-items: center;
  margin-bottom: 16rpx;
}

.author-name {
  font-size: 26rpx;
  font-weight: 500;
  color: #333;
  margin-right: 12rpx;
}

.author-tag {
  font-size: 20rpx;
  color: #07C160;
  background-color: #f0f9f4;
  padding: 4rpx 12rpx;
  border-radius: 12rpx;
}

.question-text {
  font-size: 28rpx;
  color: #666;
  line-height: 1.6;
}

/* 评论区域样式 */
.comment-section {
  background-color: white;
  border-radius: 16rpx;
  margin-bottom: 20rpx;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  box-sizing: border-box;
  max-width: 100%;
}

.section-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24rpx 30rpx;
  border-bottom: 1rpx solid #f0f0f0;
}

.title-text {
  font-size: 28rpx;
  font-weight: bold;
  color: #333;
}

.comment-count {
  font-size: 22rpx;
  color: #999;
}

.comment-list {
  padding: 0;
}

.comment-item {
  border-bottom: 1rpx solid #f8f8f8;
}

.comment-item:last-child {
  border-bottom: none;
}

.comment-main {
  padding: 24rpx 30rpx;
}

.comment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16rpx;
}

.comment-author {
  display: flex;
  align-items: center;
}

.author-role {
  font-size: 20rpx;
  padding: 4rpx 10rpx;
  border-radius: 10rpx;
  margin-left: 12rpx;
}

.author-role.staff {
  background-color: #e6f3ff;
  color: #1890ff;
}

.author-role.user {
  background-color: #f0f9f4;
  color: #07C160;
}

.comment-time {
  font-size: 22rpx;
  color: #999;
}

.comment-content {
  margin-bottom: 16rpx;
}

.content-text {
  font-size: 26rpx;
  color: #333;
  line-height: 1.5;
}

.comment-actions {
  display: flex;
  justify-content: flex-end;
}

.reply-btn {
  display: flex;
  align-items: center;
  padding: 8rpx 16rpx;
  border-radius: 20rpx;
  background-color: #f8f9fa;
  color: #666;
}

.reply-text {
  font-size: 22rpx;
  margin-left: 6rpx;
}

/* 回复列表样式 */
.reply-list {
  background-color: #f8f9fa;
  border-radius: 12rpx;
  margin: 16rpx;
  padding: 20rpx;
  box-sizing: border-box;
  max-width: 100%;
}

.reply-item {
  margin-bottom: 16rpx;
  padding-bottom: 16rpx;
  border-bottom: 1rpx solid #e8e8e8;
}

.reply-item:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.reply-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8rpx;
}

.reply-author {
  display: flex;
  align-items: center;
  font-size: 22rpx;
  color: #666;
}

.reply-arrow {
  margin: 0 8rpx;
  color: #999;
}

.reply-time {
  font-size: 20rpx;
  color: #999;
}

.reply-content {
  padding-left: 10rpx;
}

/* 回复输入框样式 */
.reply-input-container {
  padding: 0 30rpx 24rpx;
}

.reply-input {
  background-color: #f8f9fa;
  border-radius: 12rpx;
  padding: 20rpx;
}

.reply-input-field {
  width: 100%;
  background-color: white;
  border: 1rpx solid #e8e8e8;
  border-radius: 8rpx;
  padding: 16rpx;
  font-size: 24rpx;
  margin-bottom: 16rpx;
}

.reply-actions {
  display: flex;
  justify-content: flex-end;
  align-items: center;
}

.cancel-btn {
  padding: 12rpx 24rpx;
  color: #666;
  font-size: 24rpx;
  margin-right: 16rpx;
}

.submit-btn {
  padding: 12rpx 24rpx;
  border-radius: 20rpx;
  font-size: 24rpx;
  border: none;
  outline: none;
  background-color: #07C160;
  color: white;
}

.submit-btn.disabled {
  background-color: #ccc !important;
  color: #999 !important;
}

/* 空评论状态 */
.empty-comments {
  text-align: center;
  padding: 60rpx 20rpx;
  background-color: white;
  border-radius: 16rpx;
  margin-bottom: 20rpx;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.empty-icon {
  font-size: 48rpx;
  display: block;
  margin-bottom: 16rpx;
}

.empty-text {
  font-size: 26rpx;
  color: #999;
}

/* 添加评论区域 */
.add-comment-section {
  background-color: white;
  border-top: 1rpx solid #e8e8e8;
  padding: 20rpx;
  margin-top: 20rpx;
  border-radius: 16rpx;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  box-sizing: border-box;
  max-width: 100%;
}

.comment-input-container {
  background-color: #f8f9fa;
  border-radius: 12rpx;
  padding: 20rpx;
}

.comment-input {
  width: 100%;
  min-height: 80rpx;
  background-color: white;
  border: 1rpx solid #e8e8e8;
  border-radius: 8rpx;
  padding: 16rpx;
  font-size: 24rpx;
  margin-bottom: 16rpx;
}

.comment-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.char-count {
  font-size: 22rpx;
  color: #999;
}

.submit-comment-btn {
  padding: 12rpx 24rpx;
  background-color: #07C160;
  color: white;
  border-radius: 20rpx;
  font-size: 24rpx;
}

.submit-comment-btn.disabled {
  background-color: #ccc;
  color: #999;
}

/* 状态样式 */
.status-pending {
  background-color: #fff7e6;
  color: #fa8c16;
}

.status-processing {
  background-color: #f6ffed;
  color: #52c41a;
}

.status-replied {
  background-color: #e6f7ff;
  color: #1890ff;
}

.status-resolved {
  background-color: #f6ffed;
  color: #52c41a;
}

.status-closed {
  background-color: #f5f5f5;
  color: #999999;
}

.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 60vh;
  font-size: 28rpx;
  color: #999;
}
</style>