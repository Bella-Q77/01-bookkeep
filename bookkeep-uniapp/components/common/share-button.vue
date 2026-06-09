<template>
  <view class="share-button-wrapper">
    <view class="share-btn" @click="handleShare">
      <text class="iconfont icon-fenxiang"></text>
    </view>
    
    <!-- 分享组件 -->
    <share-component ref="shareComponent" :shareData="shareData"></share-component>
  </view>
</template>

<script>
import ShareComponent from '@/components/common/share-component.vue'

export default {
  name: 'ShareButton',
  components: {
    ShareComponent
  },
  props: {
    // 分享数据配置
    shareConfig: {
      type: Object,
      default: () => ({})
    },
    // 是否显示分享按钮
    showButton: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      // 默认分享数据
      shareData: {
        title: '科科记账',
        desc: '专业的个人记账管理工具',
        path: '/pages/dashboard/index',
        imageUrl: '/static/logo.png'
      }
    }
  },
  watch: {
    // 监听分享配置变化
    shareConfig: {
      handler(newConfig) {
        if (newConfig && Object.keys(newConfig).length > 0) {
          this.updateShareData(newConfig)
        }
      },
      immediate: true,
      deep: true
    }
  },
  methods: {
    // 处理分享点击
    handleShare() {
      // 触发父组件的分享事件，允许父组件自定义分享逻辑
      this.$emit('share', {
        updateShareData: this.updateShareData,
        showShare: this.showSharePopup
      })
    },
    
    // 更新分享数据
    updateShareData(config) {
      this.shareData = {
        ...this.shareData,
        ...config
      }
    },
    
    // 显示分享弹窗
    showSharePopup() {
      if (this.$refs.shareComponent) {
        this.$refs.shareComponent.showSharePopupMethod()
      }
    }
  }
}
</script>

<style scoped>
.share-button-wrapper {
  display: inline-block;
}

.share-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 16px;
  background-color: rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
}

.share-btn:active {
  background-color: rgba(255, 255, 255, 0.3);
  transform: scale(0.95);
}

.share-btn .iconfont {
  font-size: 18px;
  color: #FFFFFF;
}

/* 适配不同背景色的分享按钮 */
.share-btn.light {
  background-color: rgba(0, 0, 0, 0.1);
}

.share-btn.light .iconfont {
  color: #333333;
}

.share-btn.dark {
  background-color: rgba(255, 255, 255, 0.2);
}

.share-btn.dark .iconfont {
  color: #FFFFFF;
}
</style>