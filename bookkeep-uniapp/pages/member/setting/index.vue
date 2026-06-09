<template>
  <view class="setting-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="个人设置" 
      left-icon="left" 
      :status-bar="true" 
      @click-left="goBack"
    >
		<view slot="left" @click="goBack" style="padding: 0 20rpx;">
        <uni-icons type="left" size="24" color="#000000"></uni-icons>
      </view>
    </uni-nav-bar>
    
    <view class="setting-container">

    <!-- 页面内容 -->
    <view class="page-content">
      <!-- 个人设置区 -->
      <view class="section">
        <view class="section-title">
          <text class="title-text">个人设置</text>
        </view>
        <view class="menu-list">
          <view class="menu-item" @click="navigateTo('/pages/member/setting/profile')">
            <view class="item-left">
              <uni-icons type="person" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
              <text class="item-text">个人资料</text>
            </view>
            <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
          </view>
          <view class="menu-item" @click="navigateTo('/pages/member/setting/password')">
            <view class="item-left">
              <uni-icons type="locked" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
              <text class="item-text">修改密码</text>
            </view>
            <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
          </view>
          <view class="menu-item" @click="navigateTo('/pages/member/setting/notification')">
            <view class="item-left">
              <uni-icons type="notification" size="18" color="#07C160" style="margin-right:10px"></uni-icons>
              <text class="item-text">消息通知</text>
            </view>
            <uni-icons type="arrowright" size="14" color="#ccc"></uni-icons>
          </view>
        </view>
      </view>
    </view>
</view>

  </view>
</template>

<script>
// 使用 uni-app 的 easycom 自动注册组件，无需手动导入

export default {
  data() {
    return {
      
    }
  },
  methods: {
        // 返回上一页
    goBack() {
      uni.navigateBack()
    },
    navigateTo(url) {
      uni.navigateTo({
        url: url
      });
    },
    handleBack() {
      uni.navigateBack({
        delta: 1
      });
    }
  }
}
</script>

<style scoped>
.setting-page {
 min-height: 100vh;
  background-color: #f5f5f5;
  width: 100vw;
  overflow-x: hidden;
  box-sizing: border-box;
}

.setting-container {
  padding: 15px;
}


.section {
  margin-bottom: 15px;
  background-color: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.section .section-title {
  padding: 15px;
  border-bottom: 1px solid #f5f5f5;
}

.section .section-title .title-text {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.section .menu-list .menu-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #f5f5f5;
}

.section .menu-list .menu-item:last-child {
  border-bottom: none;
}

.section .menu-list .menu-item:active {
  background-color: #f9f9f9;
}

.section .menu-list .menu-item .item-left {
  display: flex;
  align-items: center;
}

.section .menu-list .menu-item .item-left .iconfont {
  font-size: 18px;
  color: #07C160;
  margin-right: 10px;
}

.section .menu-list .menu-item .item-left .item-text {
  font-size: 15px;
  color: #333;
}

.section .menu-list .menu-item .iconfont.icon-right {
  color: #ccc;
  font-size: 14px;
}

/* 导航栏标题居中 */
.setting-page ::v-deep .uni-navbar__header-title {
  text-align: center !important;
  justify-content: center !important;
  display: flex !important;
  align-items: center !important;
}

.setting-page ::v-deep .uni-navbar__content {
  justify-content: center !important;
}
</style>