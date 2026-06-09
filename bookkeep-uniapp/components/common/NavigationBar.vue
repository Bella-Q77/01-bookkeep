<template>
  <!-- 通用顶部导航栏组件 -->
  <view class="navigation-bar" :style="navBarStyle">
    <!-- 状态栏占位 -->
    <view class="status-bar" :style="statusBarStyle"></view>
    
    <!-- 导航栏内容 -->
    <view class="nav-content" :style="navContentStyle">
      <!-- 左侧按钮区域 -->
      <view class="nav-left" v-if="showBack || leftText || leftIcon">
        <!-- 返回按钮 -->
        <view v-if="showBack" class="back-btn" @click="handleBack">
          <text class="back-icon">‹</text>
          <text v-if="backText" class="back-text">{{ backText }}</text>
        </view>
        
        <!-- 自定义左侧内容 -->
        <view v-else-if="leftText || leftIcon" class="custom-left" @click="handleLeftClick">
          <text v-if="leftIcon" class="left-icon">{{ leftIcon }}</text>
          <text v-if="leftText" class="left-text">{{ leftText }}</text>
        </view>
      </view>
      
      <!-- 标题区域 -->
      <view class="nav-title" :style="titleStyle">
        <text class="title-text">{{ title }}</text>
      </view>
      
      <!-- 右侧按钮区域 -->
      <view class="nav-right" v-if="rightText || rightIcon">
        <view class="custom-right" @click="handleRightClick">
          <text v-if="rightIcon" class="right-icon">{{ rightIcon }}</text>
          <text v-if="rightText" class="right-text">{{ rightText }}</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
export default {
  name: 'NavigationBar',
  props: {
    // 标题
    title: {
      type: String,
      default: ''
    },
    // 是否显示返回按钮
    showBack: {
      type: Boolean,
      default: false
    },
    // 返回按钮文字
    backText: {
      type: String,
      default: ''
    },
    // 左侧自定义文字
    leftText: {
      type: String,
      default: ''
    },
    // 左侧自定义图标
    leftIcon: {
      type: String,
      default: ''
    },
    // 右侧自定义文字
    rightText: {
      type: String,
      default: ''
    },
    // 右侧自定义图标
    rightIcon: {
      type: String,
      default: ''
    },
    // 背景颜色
    backgroundColor: {
      type: String,
      default: '#07C160'
    },
    // 文字颜色
    color: {
      type: String,
      default: '#ffffff'
    },
    // 是否固定定位
    fixed: {
      type: Boolean,
      default: true
    },
    // 自定义高度
    height: {
      type: [String, Number],
      default: ''
    }
  },
  data() {
    return {
      // 不同平台的状态栏高度
      statusBarHeight: 0,
      // 导航栏内容高度
      navContentHeight: 44
    }
  },
  created() {
    this.initPlatformInfo();
  },
  computed: {
    // 导航栏整体样式
    navBarStyle() {
      const style = {
        backgroundColor: this.backgroundColor
      };
      
      if (this.fixed) {
        style.position = 'fixed';
        style.top = '0';
        style.left = '0';
        style.right = '0';
        style.zIndex = '10000'; // 提高z-index确保在最顶层
        // 确保导航栏不被状态栏遮挡
        style.height = (this.statusBarHeight + this.navContentHeight) + 'px';
      }
      
      return style;
    },
    
    // 状态栏样式
    statusBarStyle() {
      return {
        height: this.statusBarHeight + 'px',
        backgroundColor: this.backgroundColor // 状态栏区域使用相同的背景色
      };
    },
    
    // 导航栏内容样式
    navContentStyle() {
      const height = this.height || this.navContentHeight;
      return {
        height: height + 'px',
        lineHeight: height + 'px'
      };
    },
    
    // 标题样式
    titleStyle() {
      return {
        color: this.color
      };
    }
  },
  methods: {
    // 初始化平台信息
    initPlatformInfo() {
      const systemInfo = uni.getSystemInfoSync();
      
      // #ifdef MP-WEIXIN
      // 小程序环境 - 使用自定义导航栏
      this.statusBarHeight = systemInfo.statusBarHeight || 44;
      this.navContentHeight = 44; // 小程序导航栏标准高度
      // #endif
      
      // #ifdef H5
      // H5环境
      this.statusBarHeight = 0; // H5没有状态栏
      this.navContentHeight = 44;
      // #endif
      
      // #ifdef APP-PLUS
      // App环境
      this.statusBarHeight = systemInfo.statusBarHeight || 44;
      this.navContentHeight = 44;
      // #endif
      
      console.log('NavigationBar平台信息:', {
        statusBarHeight: this.statusBarHeight,
        navContentHeight: this.navContentHeight,
        platform: systemInfo.platform
      });
    },
    
    // 返回按钮点击事件
    handleBack() {
      this.$emit('back');
      // 默认行为：返回上一页
      uni.navigateBack();
    },
    
    // 左侧按钮点击事件
    handleLeftClick() {
      this.$emit('left-click');
    },
    
    // 右侧按钮点击事件
    handleRightClick() {
      this.$emit('right-click');
    }
  }
}
</script>

<style scoped>
.navigation-bar {
  width: 100%;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  background-color: #07C160; /* 确保背景色可见 */
  /* 确保导航栏在最顶层 */
  z-index: 10000;
}

.status-bar {
  width: 100%;
  background-color: inherit; /* 继承导航栏的背景色 */
  /* 确保状态栏区域正确显示 */
  position: relative;
  z-index: 10000;
}

.nav-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 15px;
  box-sizing: border-box;
  background-color: inherit;
  color: #ffffff; /* 确保文字颜色可见 */
  height: 44px; /* 固定导航栏内容高度 */
  /* 确保导航栏内容在状态栏下方正确显示 */
  position: relative;
  z-index: 10001;
}

.nav-left {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.nav-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.nav-title {
  flex: 2;
  text-align: center;
  font-size: 17px;
  font-weight: 600;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.title-text {
  font-size: 17px;
  font-weight: 600;
}

.back-btn {
  display: flex;
  align-items: center;
  padding: 8px 0;
  color: inherit;
}

.back-icon {
  font-size: 24px;
  font-weight: bold;
  margin-right: 4px;
}

.back-text {
  font-size: 16px;
}

.custom-left,
.custom-right {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  color: inherit;
}

.left-icon,
.right-icon {
  font-size: 20px;
  margin-right: 4px;
}

.left-text,
.right-text {
  font-size: 16px;
}

/* 点击效果 */
.back-btn:active,
.custom-left:active,
.custom-right:active {
  opacity: 0.7;
}
</style>