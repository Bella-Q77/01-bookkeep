<template>
  <view class="faq-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar title="意见反馈" left-icon="left" :status-bar="true" @click-left="goBack">
      <view slot="left" @click="goBack" style="padding: 0 20rpx;">
        <uni-icons type="left" size="24" color="#000000"></uni-icons>
      </view>
    </uni-nav-bar>
    <view class="faq-container">

    <!-- 反馈表单 -->
    <view class="form-container">
      <view class="form-item">
        <view class="form-label">
          <text class="label-text">标题</text>
          <text class="required">*</text>
        </view>
        <input 
          class="form-input" 
          v-model="formData.name" 
          placeholder="请输入反馈标题"
          maxlength="50"
          @input="validateTitle"
        />
        <view class="input-tips">{{ titleLength }}/50</view>
      </view>
      
      <view class="form-item">
        <view class="form-label">
          <text class="label-text">内容</text>
          <text class="required">*</text>
        </view>
        <textarea 
          class="form-textarea" 
          v-model="formData.content" 
          placeholder="请输入详细的问题描述或建议"
          maxlength="500"
          @input="validateContent"
        />
        <view class="input-tips">{{ contentLength }}/500</view>
      </view>
      
      <!-- 提交按钮 -->
      <view class="submit-btn-container">
        <button 
          class="submit-btn" 
          :class="{ 'disabled': !isFormValid }"
          :disabled="!isFormValid || submitting"
          @click="submitFeedback"
        >
          {{ submitting ? '提交中...' : '提交反馈' }}
        </button>
      </view>
    </view>
    
    <!-- 反馈历史 -->
    <view class="history-section" v-if="feedbackList.length > 0">
      <view class="section-title">
        <text class="title-text">历史反馈</text>
      </view>
      <view class="history-list">
        <view 
          class="history-item" 
          v-for="(item, index) in feedbackList" 
          :key="index"
          @click="viewFeedbackDetail(item)"
        >
          <view class="item-header">
            <text class="item-title">{{ item.name }}</text>
            <text class="item-status"  :class="{ 'status-pending': item.status === 0, 'status-processed': item.status === 1, 'status-replied': item.status === 2, 'status-resolved': item.status === 3, 'status-closed': item.status === 4 }">
              {{ getStatusText(item.status || item.type_id) }}
            </text>
          </view>
          <view class="item-content">
            <text class="item-content-text">{{ item.content }}</text>
          </view>
          <view class="item-footer">
            <text class="item-time">{{ formatTime(item.create_time) }}</text>
          </view>
        </view>
      </view>
    </view>
    
    <!-- 空状态 -->
    <view class="empty-state" v-else>
      <text class="empty-icon">💬</text>
      <text class="empty-text">暂无反馈记录</text>
    </view>

    </view>
  </view>
</template>

<script>
// 导入真实API接口
import faqApi from '@/api/faq.js'

export default {
  data() {
    return {
      formData: {
        name: '',
        content: ''
      },
      submitting: false,
      feedbackList: [],
      isFormValid: false
    }
  },
  computed: {
    titleLength() {
      return this.formData.name.length
    },
    contentLength() {
      return this.formData.content.length
    }
  },
  onLoad() {
    this.loadFeedbackHistory()
  },
  methods: {
    // 验证标题
    validateTitle() {
      this.checkFormValidity()
    },
    
    // 验证内容
    validateContent() {
      this.checkFormValidity()
    },
    
    // 检查表单有效性
    checkFormValidity() {
      this.isFormValid = this.formData.name.trim().length > 0 && 
                       this.formData.content.trim().length > 0 &&
                       this.formData.name.length <= 50 &&
                       this.formData.content.length <= 500
    },
    
    // 提交反馈
    async submitFeedback() {
      if (!this.isFormValid || this.submitting) return
      
      this.submitting = true
      
      try {
        uni.showLoading({ title: '提交中...' })
        
        // 调用API
        const result = await faqApi.submitFeedback(this.formData)
        
        if (result.code === 1) {
          // 成功提交，重新加载反馈列表
          await this.loadFeedbackHistory()
          
          // 清空表单
          this.formData = {
            name: '',
            content: ''
          }
          
          uni.hideLoading()
          uni.showToast({
            title: '提交成功',
            icon: 'success'
          })
        } else {
          throw new Error(result.message || '提交失败')
        }
        
      } catch (error) {
        console.error('提交反馈失败:', error)
        uni.hideLoading()
        uni.showToast({
          title: error.message || '提交失败，请重试',
          icon: 'none'
        })
      } finally {
        this.submitting = false
      }
    },
    
    // 加载反馈历史
    async loadFeedbackHistory() {
      try {
        // 调用API获取反馈列表
        const result = await faqApi.getFeedbackList({
          pageNum: 1,
          pageSize: 20
        })
        
        if (result.code == 1) {
          // 解析后端返回的数据结构
          this.feedbackList = result.data?.data || []
        } else {
          console.error('获取反馈列表失败:', result.msg)
          this.feedbackList = []
        }
        
      } catch (error) {
        console.error('加载反馈历史失败:', error)
        this.feedbackList = []
      }
    },
    
    // 查看反馈详情
    viewFeedbackDetail(item) {
      // 跳转到详情页面，传递反馈项的ID
      uni.navigateTo({
        url: `/pages/member/faqdetail?id=${item.id}`
      })
    },
    
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
    
    // 获取状态样式类（返回类名字符串）
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
        return (date.getMonth() + 1) + '月' + date.getDate() + '日'
      }
    },
    
    // 返回上一页
    goBack() {
      uni.navigateBack()
    }
  }
}
</script>

<style scoped>
.faq-page {
  min-height: 100vh;
  background-color: #f5f5f5;
  width: 100%;
  overflow-x: hidden;
  box-sizing: border-box;
}

.faq-container{
  padding: 10px;
  width: 100%;
  max-width: 100vw;
  box-sizing: border-box;
}

.form-container {
  background-color: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  width: 100%;
  box-sizing: border-box;
  max-width: 100vw;
}

.form-item {
  margin-bottom: 25px;
}

.form-label {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.label-text {
  font-size: 14px;
  color: #333;
  font-weight: 500;
}

.required {
  color: #ff4d4f;
  margin-left: 4px;
}

.form-input {
  border: 1px solid #e8e8e8;
  border-radius: 6px;
  padding: 12px;
  font-size: 14px;
  background-color: #fafafa;
  width: 100%;
  box-sizing: border-box;
  height: 40px;
  max-width: 100%;
}

.form-textarea {
  border: 1px solid #e8e8e8;
  border-radius: 6px;
  padding: 12px;
  font-size: 14px;
  background-color: #fafafa;
  min-height: 120px;
  line-height: 1.5;
  width: 100%;
  box-sizing: border-box;
  max-width: 100%;
  resize: none;
}

.input-tips {
  text-align: right;
  font-size: 12px;
  color: #999;
  margin-top: 4px;
}

.submit-btn-container {
  margin-top: 30px;
}

.submit-btn {
  background-color: #07C160;
  color: white;
  border: none;
  border-radius: 20px;
  padding: 0px;
  font-size: 16px;
  width: 100%;
  box-sizing: border-box;
}

.submit-btn.disabled {
  background-color: #ccc;
  color: #999;
}

.history-section {
  margin-top:20px;
  padding: 10px;
  background-color: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  box-sizing: border-box;
}

.section-title {
  padding: 15px;
  border-bottom: 1px solid #f5f5f5;
}

.title-text {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.history-list {
  padding: 0;
}

.history-item {
  padding: 15px;
  border-bottom: 1px solid #f5f5f5;
  min-height: 80px;
}

.history-item:last-child {
  border-bottom: none;
}

.history-item:active {
  background-color: #f9f9f9;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.item-title {
  font-size: 15px;
  font-weight: 500;
  color: #333;
  flex: 1;
  margin-right: 10px;
}

.item-status {
  font-size: 12px;
  padding: 2px 6px;
  border-radius: 3px;
}

.status-pending {
  background-color: #fff7e6;
  color: #fa8c16;
}

.status-processed {
  background-color: #f6ffed;
  color: #52c41a;
}

.status-replied {
  background-color: #e6f7ff;
  color: #1890ff;
}

.item-content {
  margin-bottom: 8px;
}

.item-content-text {
  font-size: 14px;
  color: #666;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.item-footer {
  text-align: right;
}

.item-time {
  font-size: 12px;
  color: #999;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
}

.empty-icon {
  font-size: 48px;
  display: block;
}

.empty-text {
  display: block;
  margin-top: 10px;
  font-size: 14px;
}
</style>