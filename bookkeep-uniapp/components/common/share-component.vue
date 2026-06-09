<template>
  <view class="share-component">
    <!-- 分享弹窗 -->
    <view class="share-popup" v-if="showSharePopup">
      <!-- 遮罩层 -->
      <view class="share-mask" @click="hideSharePopup"></view>
      
      <!-- 分享内容 -->
      <view class="share-content">
        <view class="share-title">分享至</view>
        
        <!-- 分享说明 -->
        <view class="share-tips">
          <text class="tips-text">点击右上角 "..." 按钮进行分享</text>
        </view>
        
        <!-- 分享选项 -->
        <view class="share-options">
          <view class="share-option" @click="shareToWechatFriend">
            <view class="share-icon wechat-friend"></view>
            <text class="share-text">分享给朋友</text>
          </view>
          
          <view class="share-option" @click="shareToWechatMoments">
            <view class="share-icon wechat-moments"></view>
            <text class="share-text">分享到朋友圈</text>
          </view>
          
          <view class="share-option" @click="generateShareImage">
            <view class="share-icon share-image"></view>
            <text class="share-text">生成分享图片</text>
          </view>
          
          <view class="share-option" @click="copyShareLink">
            <view class="share-icon copy-link"></view>
            <text class="share-text">复制分享信息</text>
          </view>
        </view>
        
        <!-- 取消按钮 -->
        <view class="share-cancel" @click="hideSharePopup">
          <text class="cancel-text">取消</text>
        </view>
      </view>
    </view>
    
    <!-- 分享预览图片 -->
    <view class="share-preview" v-if="showPreview">
      <canvas canvas-id="shareCanvas" class="share-canvas"></canvas>
    </view>
  </view>
</template>

<script>
export default {
  name: 'ShareComponent',
  props: {
    shareData: {
      type: Object,
      default: () => ({
        title: '科科记账',
        desc: '简单好用的记账小程序',
        path: '/pages/dashboard/index',
        imageUrl: '/static/logo.png'
      })
    }
  },
  data() {
    return {
      showSharePopup: false,
      showPreview: false
    }
  },
  methods: {
    // 显示分享弹窗
    showSharePopupMethod() {
      this.showSharePopup = true;
    },
    
    // 隐藏分享弹窗
    hideSharePopup() {
      this.showSharePopup = false;
    },
    
    // 分享给微信好友
    shareToWechatFriend() {
      this.hideSharePopup();
      
      // 小程序中不直接调用uni.share，而是触发原生分享
      // 通过模拟右上角菜单的分享行为
      try {
        // 触发分享菜单
        uni.showActionSheet({
          itemList: ['转发给朋友'],
          success: () => {
            // 提示用户使用右上角菜单分享
            uni.showModal({
              title: '分享提示',
              content: '请点击右上角 "..." 按钮，选择 "转发" 来分享给朋友',
              showCancel: false,
              confirmText: '知道了'
            });
          }
        });
      } catch (error) {
        console.error('分享失败:', error);
        this.showShareTip();
      }
    },
    
    // 分享到朋友圈
    shareToWechatMoments() {
      this.hideSharePopup();
      
      // 朋友圈分享提示
      uni.showModal({
        title: '分享到朋友圈',
        content: '请点击右上角 "..." 按钮，选择 "分享到朋友圈" 来分享',
        showCancel: false,
        confirmText: '知道了'
      });
    },
    
    // 生成分享图片
    generateShareImage(callback) {
      this.hideSharePopup();
      this.showPreview = true;
      
      // 创建canvas上下文
      const ctx = uni.createCanvasContext('shareCanvas', this);
      
      // 设置canvas大小
      const canvasWidth = 300;
      const canvasHeight = 400;
      
      // 绘制背景
      ctx.setFillStyle('#07C160');
      ctx.fillRect(0, 0, canvasWidth, canvasHeight);
      
      // 绘制标题
      ctx.setFillStyle('#FFFFFF');
      ctx.setFontSize(20);
      ctx.setTextAlign('center');
      ctx.fillText(this.shareData.title, canvasWidth / 2, 80);
      
      // 绘制描述
      ctx.setFontSize(14);
      ctx.fillText(this.shareData.desc, canvasWidth / 2, 120);
      
      // 绘制logo
      ctx.drawImage(this.shareData.imageUrl, 100, 150, 100, 100);
      
      // 绘制小程序码（如果有的话）
      if (this.shareData.qrCode) {
        ctx.drawImage(this.shareData.qrCode, 100, 280, 100, 100);
      }
      
      // 绘制完成
      ctx.draw(false, () => {
        // 将canvas转换为图片
        uni.canvasToTempFilePath({
          canvasId: 'shareCanvas',
          success: (res) => {
            this.shareData.imageUrl = res.tempFilePath;
            this.showPreview = false;
            
            if (callback) {
              callback();
            }
            
            // 保存图片到相册
            uni.saveImageToPhotosAlbum({
              filePath: res.tempFilePath,
              success: () => {
                uni.showToast({
                  title: '图片已保存',
                  icon: 'success'
                });
              },
              fail: () => {
                uni.showToast({
                  title: '保存失败',
                  icon: 'none'
                });
              }
            });
          },
          fail: () => {
            this.showPreview = false;
            uni.showToast({
              title: '生成图片失败',
              icon: 'none'
            });
          }
        }, this);
      });
    },
    
    // 复制分享链接
    copyShareLink() {
      this.hideSharePopup();
      
      // 复制小程序页面路径
      const shareUrl = `科科记账小程序：${this.shareData.title}\n${this.shareData.desc}\n点击进入小程序：${this.shareData.path}`;
      
      uni.setClipboardData({
        data: shareUrl,
        success: () => {
          uni.showToast({
            title: '链接已复制',
            icon: 'success'
          });
        },
        fail: () => {
          uni.showToast({
            title: '复制失败',
            icon: 'none'
          });
        }
      });
    },
    
    // 显示分享提示
    showShareTip() {
      uni.showModal({
        title: '分享提示',
        content: '请点击右上角 "..." 按钮，选择 "转发" 来分享给朋友',
        showCancel: false,
        confirmText: '知道了'
      });
    }
  }
}
</script>

<style scoped>
.share-component {
  position: relative;
}

.share-popup {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
}

.share-mask {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
}

.share-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #FFFFFF;
  border-radius: 20px 20px 0 0;
  padding: 20px;
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    transform: translateY(100%);
  }
  to {
    transform: translateY(0);
  }
}

.share-title {
  text-align: center;
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 15px;
}

.share-tips {
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 20px;
}

.tips-text {
  font-size: 14px;
  color: #666;
  text-align: center;
  line-height: 1.4;
}

.share-options {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
  margin-bottom: 20px;
}

.share-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 80px;
  margin-bottom: 15px;
}

.share-icon {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  margin-bottom: 8px;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
}

.share-icon.wechat-friend {
  background-color: #07C160;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M8.5 6C5.46 6 3 8.24 3 11c0 1.64.88 3.09 2.24 4-.1.39-.22.78-.36 1.14-.24.58-.52 1.14-.84 1.68.52-.05 1.04-.14 1.54-.26.5-.12.98-.28 1.44-.47.44.1.9.16 1.38.19.1-.34.24-.66.4-.96A5.01 5.01 0 0 1 8.5 13c-2.48 0-4.5 1.79-4.5 4s2.02 4 4.5 4c.38 0 .74-.05 1.1-.14.29-.07.56-.17.83-.29.27.12.54.22.83.29.36.09.72.14 1.1.14 2.48 0 4.5-1.79 4.5-4 0-1.04-.39-1.99-1.04-2.71.16.3.3.62.4.96.48-.03.94-.09 1.38-.19.46.19.94.35 1.44.47.5.12 1.02.21 1.54.26-.32-.54-.6-1.1-.84-1.68-.14-.36-.26-.75-.36-1.14C20.12 14.09 21 12.64 21 11c0-2.76-2.46-5-5.5-5-.66 0-1.29.12-1.87.33A5.03 5.03 0 0 0 8.5 6zm0 2c1.38 0 2.5 1.12 2.5 2.5S9.88 13 8.5 13 6 11.88 6 10.5 7.12 8 8.5 8zm7 0c1.38 0 2.5 1.12 2.5 2.5s-1.12 2.5-2.5 2.5-2.5-1.12-2.5-2.5S14.12 8 15.5 8z"/></svg>');
}

.share-icon.wechat-moments {
  background-color: #FFB800;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>');
}

.share-icon.share-image {
  background-color: #FF6B6B;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>');
}

.share-icon.copy-link {
  background-color: #4A90E2;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>');
}

.share-text {
  font-size: 12px;
  color: #666;
}

.share-cancel {
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-top: 1px solid #F0F0F0;
}

.cancel-text {
  font-size: 16px;
  color: #666;
}

.share-preview {
  position: fixed;
  top: -9999px;
  left: -9999px;
  z-index: -1;
}

.share-canvas {
  width: 300px;
  height: 400px;
}
</style>