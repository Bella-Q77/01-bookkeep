<template>
  <view class="about-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="关于我们" 
      left-icon="left" 
      :status-bar="true" 
      @click-left="goBack"
    >
      <view slot="left" @click="goBack" style="padding: 0 20rpx;">
        <uni-icons type="left" size="24" color="#000000"></uni-icons>
      </view>
    </uni-nav-bar>
    
    <view class="content-container">
      <!-- 应用图标和名称 -->
      <view class="app-header">
        <view class="app-icon">
          <text class="icon-text">💰</text>
        </view>
        <view class="app-info">
          <text class="app-name">科科记账</text>
          <text class="app-version">版本 1.0.0</text>
        </view>
      </view>
      
      <!-- 关于我们内容 -->
      <view class="about-content">
        <view class="section">
          <text class="section-title">产品介绍</text>
          <view class="section-content">
                        <text class="content-text">
              科科记账是一款简洁的记账小程序，覆盖收支记录、预算管理、财务分析、报表生成等核心需求，力求极简，专注个人记账。
            </text>
            <text class="content-text">
              主要面向个人用户与小微企业的全场景记账工具，以 "极简操作、精准核算、智能分析" 为核心，覆盖收支记录、预算管理、财务分析、报表生成等核心需求，助力用户轻松掌控财务状况，实现科学理财与合规记账。
            </text>

          </view>
        </view>
        
        <!-- 功能特色 -->
        <view class="section">
          <text class="section-title">功能特色</text>
          <view class="features-list">
            <view class="feature-item">
              <uni-icons type="checkbox-filled" size="16" color="#07C160"></uni-icons>
              <text class="feature-text">极简操作，快速记账</text>
            </view>
            <view class="feature-item">
              <uni-icons type="checkbox-filled" size="16" color="#07C160"></uni-icons>
              <text class="feature-text">智能分类，自动识别</text>
            </view>
            <view class="feature-item">
              <uni-icons type="checkbox-filled" size="16" color="#07C160"></uni-icons>
              <text class="feature-text">预算管理，合理规划</text>
            </view>
            <view class="feature-item">
              <uni-icons type="checkbox-filled" size="16" color="#07C160"></uni-icons>
              <text class="feature-text">数据统计，一目了然</text>
            </view>
            <view class="feature-item">
              <uni-icons type="checkbox-filled" size="16" color="#07C160"></uni-icons>
              <text class="feature-text">多账本管理，满足不同需求</text>
            </view>
          </view>
        </view>
        
        <!-- 联系我们 -->
        <view class="section">
          <text class="section-title">联系我们</text>
          <view class="contact-info">
            <view class="contact-item">
              <uni-icons type="help" size="16" color="#1890ff"></uni-icons>
              <text class="contact-text">Q Q：18717 20801 （微信同号）</text>
            </view>
            <view class="contact-item">
              <uni-icons type="email-filled" size="16" color="#1890ff"></uni-icons>
              <text class="contact-text">邮箱：kf@07fly.net</text>
            </view>
            <view class="contact-item" @click="goToFeedback">
              <uni-icons type="chat" size="16" color="#1890ff"></uni-icons>
              <text class="contact-text">意见反馈：点击"意见反馈"</text>
            </view>
          </view>
        </view>
        
        <!-- 版权信息 -->
        <view class="copyright">
          <text class="copyright-text">© 2025 科科记账 版权所有</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
// 使用 uni-app 的 easycom 自动注册组件，无需手动 import
export default {
  data() {
    return {
      // 可以在这里添加版本信息等数据
    }
  },
  methods: {
    // 返回上一页
    goBack() {
      uni.navigateBack()
    },
    // 跳转到意见反馈页面
    goToFeedback() {
      uni.navigateTo({
        url: '/pages/member/faq'
      })
    }
  }
}
</script>

<style scoped>
.about-page {
  min-height: 100vh;
  background-color: #f5f5f5;
  width: 100vw;
  overflow-x: hidden;
  box-sizing: border-box;
}

.content-container {
  padding: 40rpx 30rpx;
  width: 100%;
  box-sizing: border-box;
  max-width: 100vw;
}

/* 应用头部样式 */
.app-header {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  margin-bottom: 60rpx;
  padding: 40rpx 0;
  background-color: white;
  border-radius: 16rpx;
  box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.08);
}

.app-icon {
  width: 120rpx;
  height: 120rpx;
  background: linear-gradient(135deg, #07C160, #52c41a);
  border-radius: 24rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24rpx;
}

.icon-text {
  font-size: 60rpx;
  color: white;
}

.app-info {
  text-align: center;
}

.app-name {
  font-size: 36rpx;
  font-weight: bold;
  color: #333;
  display: block;
  margin-bottom: 8rpx;
}

.app-version {
  font-size: 24rpx;
  color: #999;
}

/* 关于内容样式 */
.about-content {
  background-color: white;
  border-radius: 16rpx;
  padding: 40rpx 30rpx;
  box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.08);
  width: 100%;
  box-sizing: border-box;
  overflow-x: hidden;
}

.section {
  margin-bottom: 50rpx;
}

.section:last-child {
  margin-bottom: 0;
}

.section-title {
  font-size: 32rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 24rpx;
  display: block;
  position: relative;
  padding-left: 20rpx;
}

.section-title::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 8rpx;
  height: 32rpx;
  background-color: #07C160;
  border-radius: 4rpx;
}

.section-content {
  line-height: 1.8;
}

.content-text {
  font-size: 28rpx;
  color: #666;
  line-height: 1.8;
  display: block;
  margin-bottom: 20rpx;
  text-align: justify;
}

.content-text:last-child {
  margin-bottom: 0;
}

/* 功能特色样式 */
.features-list {
  display: flex;
  flex-direction: column;
  gap: 20rpx;
}

.feature-item {
  display: flex;
  align-items: center;
  padding: 20rpx;
  background-color: #f8f9fa;
  border-radius: 12rpx;
  border-left: 6rpx solid #07C160;
}

.feature-text {
  font-size: 26rpx;
  color: #333;
  margin-left: 16rpx;
}

/* 联系信息样式 */
.contact-info {
  display: flex;
  flex-direction: column;
  gap: 20rpx;
}

.contact-item {
  display: flex;
  align-items: center;
  padding: 20rpx;
  background-color: #f0f9ff;
  border-radius: 12rpx;
  border-left: 6rpx solid #1890ff;
}

.contact-text {
  font-size: 26rpx;
  color: #1890ff;
  margin-left: 16rpx;
}

/* 版权信息样式 */
.copyright {
  margin-top: 40rpx;
  padding-top: 30rpx;
  border-top: 1rpx solid #f0f0f0;
  text-align: center;
}

.copyright-text {
  font-size: 24rpx;
  color: #999;
}

/* 响应式调整 */
@media (max-width: 750rpx) {
  .content-container {
    padding: 30rpx 20rpx;
  }
  
  .app-header {
    margin-bottom: 40rpx;
    padding: 30rpx 0;
  }
  
  .about-content {
    padding: 30rpx 20rpx;
  }
}
</style>