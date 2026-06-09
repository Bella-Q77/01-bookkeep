<template>
  <view class="books-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="账本管理" 
      background-color="#07C160" 
      color="#FFFFFF" 
      :status-bar="true"
      :fixed="true"
      left-icon="back"
      @click-left="goBack"
    ></uni-nav-bar>
    
    <!-- 账本卡片列表 -->
    <view class="books-container">
      <view class="book-card" v-for="(book, index) in books" :key="index" 
            :class="{ 'active': book.id == currentBookId }">
        <!-- 第一行：账本名称和编辑图标 -->
        <view class="card-row first-row">
          <text class="book-name">{{book.name}}</text>
          <view class="edit-menu-container">
            <view class="edit-icon" @click="goToBookDetail(book)">
              <text class="iconfont icon-more">⋮</text>
            </view>
          </view>
        </view>
        
        <!-- 第二行：创建时间 -->
        <view class="card-row second-row">
          <text class="book-date">创建时间: {{formatDate(book.create_time)}}</text>
        </view>
        
        <!-- 第三行：切换账本按钮 -->
        <view class="card-row third-row">
          <button class="action-btn use-btn" @click="useBook(book)" 
                  :class="{ 'active': book.id == currentBookId }">
            {{book.id == currentBookId ? '当前账本' : '切换为该账本'}}
          </button>
        </view>
      </view>
    </view>
    
    <!-- 添加/编辑账本表单 -->
    <view class="book-form" v-if="showForm">
      <view class="form-header">
        <text>{{isEditing ? '编辑账本' : '添加账本'}}</text>
        <text class="close-btn" @click="closeForm">×</text>
      </view>
      
      <view class="form-content">
        <view class="form-item">
          <text class="label">账本名称</text>
          <input type="text" v-model="formData.name" placeholder="例如: 旅行账本" />
        </view>

        <view class="form-item">
          <text class="label">基准币种</text>
          <picker mode="selector" :range="currencyList" @change="(e) => formData.currency = currencyList[e.detail.value]">
            <view class="picker">{{formData.currency || '请选择基准币种'}}</view>
          </picker>
        </view>

        <view class="form-item">
          <text class="label">月起始日</text>
          <picker mode="selector" :range="dayList" @change="(e) => formData.monthStartDay = dayList[e.detail.value]">
            <view class="picker">{{formData.monthStartDay || '请选择账单周期起始日'}}</view>
          </picker>
        </view>
        
        <view class="form-actions">
          <button class="cancel-btn" @click="closeForm">取消</button>
          <button class="save-btn" @click="handleSaveBook" :loading="savingLoading">
            {{isEditing ? '保存修改' : '添加账本'}}
          </button>
        </view>
      </view>
    </view>
    
    <!-- 添加账本按钮 -->
    <view class="add-book-btn">
      <button class="add-btn" @click="addBook">
     
        添加账本
      </button>
    </view>
    
    <!-- 半透明遮罩层 -->
    <view class="mask" v-if="showForm" @click="closeForm"></view>
  </view>
</template>

<script>
import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue'
import uniNavBar from '@dcloudio/uni-ui/lib/uni-nav-bar/uni-nav-bar.vue'
import booksApi from '@/api/books'

export default {
  components: {
    'uni-icons': uniIcons,
    'uni-nav-bar': uniNavBar
  },
  data() {
    return {
      books: [],
      loading: false,
      showForm: false,
      isEditing: false,
      formData: {
        id: '',
        name: '',
        currency: 'CNY',
        monthStartDay: 1
      },
      currencyList: ['CNY', 'USD', 'EUR', 'JPY', 'HKD', 'GBP'],
      dayList: Array.from({length: 28}, (_, i) => i + 1), // 1-28日
      savingLoading: false,
      currentBookId: '',
      activeMenuIndex: -1 // 当前激活的菜单索引
    }
  },
  onLoad(options) {
    this.currentBookId = options.currentBookId || ''
    this.loadBooks()
  },
  
  onShow() {
    // 当页面显示时重新加载数据，确保数据同步
    this.loadBooks()
  },
  
  onLoad(options) {
    // 优先从URL参数获取currentBookId，如果没有则从Session中获取
    this.currentBookId = options.currentBookId || uni.getStorageSync('currentBookId') || ''
    this.loadBooks()
    
    // 监听账本更新事件
    uni.$on('bookUpdated', this.handleBookUpdated)
  },
  
  onUnload() {
    // 页面卸载时移除事件监听
    uni.$off('bookUpdated', this.handleBookUpdated)
  },
  methods: {
    goBack() {
      uni.navigateBack()
    },
    
    async loadBooks() {
      this.loading = true
      try {
        const res = await booksApi.getBooksList()
		
		  console.log('加载账本');
		  console.log(res)
        this.books = res.data || []
      } catch (error) {
        uni.showToast({
          title: '加载账本失败',
          icon: 'none'
        })
      } finally {
        this.loading = false
      }
    },
    
    // 使用账本
    async useBook(book) {
      try {
        // 先调用API切换账本
        await booksApi.changeBook(book.id)
        
        // API调用成功后再执行后续操作
        uni.$emit('bookSelected', book)
        this.currentBookId = book.id
        uni.setStorageSync('currentBookId', book.id)
        
        // 跳转到dashboard页面，并传递账本ID
        uni.switchTab({
          url: `/pages/dashboard/index?bookId=${book.id}`,
          success: () => {
            uni.showToast({
              title: `已切换到账本: ${book.name}`,
              icon: 'success'
            })
          }
        })
      } catch (error) {
        uni.showToast({
          title: '切换账本失败',
          icon: 'none'
        })
        console.error('切换账本失败:', error)
      }
    },
    
    // 添加账本
    addBook() {
      this.isEditing = false
      this.formData = {
        id: '',
        name: '',
        currency: 'CNY',
        monthStartDay: 1
      }
      this.showForm = true
    },
    // 关闭表单
    closeForm() {
      this.showForm = false
      this.formData = {
        id: '',
        name: '',
        currency: 'CNY',
        monthStartDay: 1
      }
    },
    
    // 跳转到账本详细页面
    goToBookDetail(book) {
      uni.navigateTo({
        url: `/pages/books/detail?id=${book.id}`
      })
    },
    
    // 处理账本更新事件
    handleBookUpdated(updatedBook) {
      // 更新本地账本列表中的对应账本信息
      const index = this.books.findIndex(book => book.id === updatedBook.id)
      if (index !== -1) {
        this.books.splice(index, 1, updatedBook)
      }
    },
    
    // 保存账本
    async handleSaveBook() {
      if (!this.formData.name) {
        uni.showToast({
          title: '请输入账本名称',
          icon: 'none'
        })
        return
      }
      
      this.savingLoading = true
      try {
		  // 添加账本
		  const res = await booksApi.createBook({
			name: this.formData.name,
			currency: this.formData.currency,
			monthStartDay: this.formData.monthStartDay
		  })
		  this.books.push(res.data)
        
        this.closeForm()
        uni.showToast({
          title: this.isEditing ? '修改成功' : '添加成功',
          icon: 'success'
        })
      } catch (error) {
        uni.showToast({
          title: this.isEditing ? '修改失败' : '添加失败',
          icon: 'none'
        })
      } finally {
        this.savingLoading = false
      }
    },
    
    // 格式化日期
    formatDate(date) {
      if (!date) return ''
      return new Date(date).toLocaleDateString()
    }
  }
}
</script>

<style lang="scss" scoped>
.books-page {
  background-color: #f5f5f5;
  min-height: 100vh;
  width: 100vw;
  overflow-x: hidden;
  box-sizing: border-box;
  
  .books-container {
    padding: 30rpx;
    box-sizing: border-box;
    width: 100%;
    
    .book-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 24rpx;
      padding: 20rpx 40rpx;
      margin-bottom: 30rpx;
      box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      width: 100%;
      box-sizing: border-box;
      
      &.active {
        background: linear-gradient(135deg, #07C160 0%, #05a854 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(7, 193, 96, 0.3);
      }
      
      &:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      }
      
      .card-row {
        margin-bottom: 12px;
        
        &.first-row {
          display: flex;
          justify-content: space-between;
          align-items: center;
          width: 100%;
          
          .book-name {
            font-size: 36rpx;
            font-weight: bold;
            color: white;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 80%;
          }
          
          .edit-menu-container {
            position: relative;
            
            .edit-icon {
              width: 32px;
              height: 32px;
              border-radius: 50%;
              display: flex;
              align-items: center;
              justify-content: center;
              cursor: pointer;
              transition: all 0.3s ease;
              color: white;
              font-size: 18px;
              
              &:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: scale(1.1);
              }
            }
            
            .action-menu {
              position: absolute;
              top: 100%;
              right: 0;
              background: white;
              border-radius: 8px;
              box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
              z-index: 10;
              min-width: 100px;
              overflow: hidden;
              animation: slideDown 0.2s ease-out;
              
              .menu-item {
                display: flex;
                align-items: center;
                padding: 10px 12px;
                font-size: 14px;
                color: #333;
                cursor: pointer;
                transition: all 0.2s ease;
                border-bottom: 1px solid #f0f0f0;
                
                &:last-child {
                  border-bottom: none;
                }
                
                &:hover {
                  background: #f5f5f5;
                }
                
                &.delete {
                  color: #ff3b30;
                  
                  &:hover {
                    background: #ffeaea;
                  }
                }
                
                .iconfont {
                  margin-right: 8px;
                  font-size: 14px;
                }
              }
            }
          }
        }
        
        &.second-row {
          .book-date {
            font-size: 28rpx;
            color: rgba(255, 255, 255, 0.9);
          }
        }
        
        &.third-row {
          display: flex;
          justify-content: flex-end;
          align-items: center;
          
          .action-btn {
            border: none;
            border-radius: 12rpx;
            font-size: 28rpx;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            min-width: 240rpx;
            padding: 2rpx 2rpx;
            
            &.use-btn {
              background: rgba(255, 255, 255, 0.2);
              color: white;
              
              &.active {
                background: rgba(7, 193, 96, 0.8);
                font-weight: bold;
              }
              
              &:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
              }
            }
          }
        }
      }
    }
  }
  
  .add-book-btn {
    position: fixed;
    bottom: 60rpx;
    right: 40rpx;
    z-index: 10;
    
    .add-btn {
      background: linear-gradient(135deg, #07C160 0%, #05a854 100%);
      color: white;
      border: none;
      border-radius: 80rpx;
      padding: 30rpx 30rpx;
      box-shadow: 0 8rpx 24rpx rgba(7, 193, 96, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28rpx;
	  width: 136rpx;
	  height: 136rpx;
	  line-height: 40rpx;
      
      .iconfont {
        margin-right: 6px;
        font-size: 16px;
      }
    }
  }
  
  .book-form {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: white;
    border-radius: 30rpx 30rpx 0 0;
    padding: 40rpx;
    z-index: 100;
    max-height: 80vh;
    overflow-y: auto;
    width: 100vw;
    box-sizing: border-box;
    
    .form-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 40rpx;
      padding-bottom: 30rpx;
      border-bottom: 2rpx solid #eee;
      width: 100%;
      
      text {
        font-size: 36rpx;
        font-weight: bold;
      }
      
      .close-btn {
        font-size: 48rpx;
        color: #999;
        cursor: pointer;
      }
    }
    
    .form-content {
      width: 100%;
      box-sizing: border-box;
      
      .form-item {
        margin-bottom: 40rpx;
        width: 100%;
        
        .label {
          display: block;
          margin-bottom: 16rpx;
          color: #666;
          font-size: 28rpx;
        }
        
        input, .picker {
          width: 100%;
          height: 88rpx;
          padding: 0 24rpx;
          border: 2rpx solid #ddd;
          border-radius: 16rpx;
          font-size: 28rpx;
          box-sizing: border-box;
          line-height: 88rpx;
          background-color: #fff;
          transition: all 0.3s ease;
          
          &:focus {
            border-color: #07C160;
            outline: none;
            box-shadow: 0 0 0 2rpx rgba(7, 193, 96, 0.1);
          }
        }
        
        input {
          line-height: normal;
          padding: 24rpx;
        }
        
        .picker {
          display: flex;
          align-items: center;
          justify-content: space-between;
          cursor: pointer;
          
          &:after {
            content: "▼";
            font-size: 20rpx;
            color: #999;
          }
        }
      }
      
      .form-actions {
        display: flex;
        gap: 20rpx;
        margin-top: 40rpx;
        width: 100%;
        
        .cancel-btn {
          flex: 1;
          background: #f5f5f5;
          color: #666;
          border: none;
          border-radius: 40rpx;
    
          font-size: 28rpx;
          height: 88rpx;
          line-height: 88rpx;
        }
        
        .save-btn {
          flex: 1;
          background: #07C160;
          color: white;
          border: none;
          border-radius: 40rpx;
      
          font-size: 28rpx;
          height: 88rpx;
          line-height: 88rpx;
        }
      }
    }
  }
  
  .mask {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 99;
  }
}

// 模拟图标字体
.iconfont {
  font-family: "iconfont";
  
  &.icon-book:before { content: "📒"; }
  &.icon-add:before { content: "+"; }
  &.icon-edit:before { content: "✏️"; }
  &.icon-delete:before { content: "🗑️"; }
  &.icon-more:before { content: "⋮"; }
}

// 动画效果
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20rpx);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* 确保uni-nav-bar在小程序中正确显示 */
::v-deep .uni-nav-bar {
  width: 100vw !important;
  box-sizing: border-box !important;
}

::v-deep .uni-nav-bar__content {
  width: 100% !important;
  max-width: 100vw !important;
  box-sizing: border-box !important;
}
</style>