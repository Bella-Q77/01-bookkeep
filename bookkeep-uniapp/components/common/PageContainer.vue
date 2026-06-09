<template>
  <!-- 页面内容容器组件 -->
  <view class="page-container" :style="containerStyle">
    <!-- 顶部导航栏插槽 -->
    <slot name="navigation"></slot>
    
    <!-- 页面内容 -->
    <view class="page-content" :style="contentStyle">
      <slot></slot>
    </view>
  </view>
</template>

<script>
export default {
  name: 'PageContainer',
  props: {
    // 是否使用安全区域
    safeArea: {
      type: Boolean,
      default: true
    },
    // 自定义顶部间距
    paddingTop: {
      type: [String, Number],
      default: ''
    },
    // 自定义底部间距
    paddingBottom: {
      type: [String, Number],
      default: ''
    },
    // 背景颜色
    backgroundColor: {
      type: String,
      default: '#ffffff'
    }
  },
  data() {
    return {
      // 导航栏高度
      navBarHeight: 0,
      // 状态栏高度
      statusBarHeight: 0,
      // 安全区域底部高度
      safeAreaBottom: 0
    }
  },
  created() {
    this.initPlatformInfo();
  },
  computed: {
    // 容器样式
    containerStyle() {
      return {
        backgroundColor: this.backgroundColor,
        minHeight: '100vh'
      };
    },
    
    // 内容区域样式
    contentStyle() {
      const style = {
        backgroundColor: this.backgroundColor
      };
      
      // 计算顶部间距
      if (this.paddingTop !== '') {
        style.paddingTop = this.addUnit(this.paddingTop);
      } else {
        // 默认顶部间距：导航栏高度 + 状态栏高度
        style.paddingTop = (this.navBarHeight + this.statusBarHeight) + 'px';
      }
      
      // 计算底部间距
      if (this.paddingBottom !== '') {
        style.paddingBottom = this.addUnit(this.paddingBottom);
      } else if (this.safeArea) {
        // 安全区域底部间距
        style.paddingBottom = this.safeAreaBottom + 'px';
      }
      
      console.log('PageContainer内容区域样式:', {
        paddingTop: style.paddingTop,
        paddingBottom: style.paddingBottom,
        navBarHeight: this.navBarHeight,
        statusBarHeight: this.statusBarHeight
      });
      
      return style;
    }
  },
  methods: {
    // 初始化平台信息
    initPlatformInfo() {
      const systemInfo = uni.getSystemInfoSync();
      
      // #ifdef MP-WEIXIN
      // 小程序环境
      this.statusBarHeight = systemInfo.statusBarHeight || 44;
      this.navBarHeight = 44; // 小程序导航栏标准高度
      this.safeAreaBottom = systemInfo.safeArea?.bottom ? 
        systemInfo.screenHeight - systemInfo.safeArea.bottom : 0;
      // #endif
      
      // #ifdef H5
      // H5环境
      this.statusBarHeight = 0; // H5没有状态栏
      this.navBarHeight = 44; // H5使用自定义导航栏
      this.safeAreaBottom = 0;
      // #endif
      
      // #ifdef APP-PLUS
      // App环境
      this.statusBarHeight = systemInfo.statusBarHeight || 44;
      this.navBarHeight = 44;
      this.safeAreaBottom = systemInfo.safeAreaInsets?.bottom || 0;
      // #endif
      
      console.log('PageContainer平台信息:', {
        statusBarHeight: this.statusBarHeight,
        navBarHeight: this.navBarHeight,
        safeAreaBottom: this.safeAreaBottom,
        platform: systemInfo.platform
      });
    },
    
    // 添加单位
    addUnit(value) {
      if (typeof value === 'number') {
        return value + 'px';
      }
      return value;
    }
  }
}
</script>

<style scoped>
.page-container {
  width: 100%;
  box-sizing: border-box;
}

.page-content {
  width: 100%;
  min-height: calc(100vh - var(--nav-bar-height, 0px) - var(--status-bar-height, 0px));
  box-sizing: border-box;
}
</style>