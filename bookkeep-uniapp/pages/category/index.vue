<template>
  <view class="categories-page">
    <!-- 导航栏 -->
    <uni-nav-bar 
      title="分类管理" 
      background-color="#07C160" 
      color="#FFFFFF" 
      status-bar="true"
      fixed="true"
      left-icon="back"
      @clickLeft="goBack"
    ></uni-nav-bar>
    
	
	<view class="categories-container">
	
    <!-- 顶部切换按钮 -->
    <view class="type-tabs">
      <view 
        class="tab-item" 
        :class="{ active: activeTab === 'expense' }"
        @click="switchTab('expense')"
      >
        <text>支出分类</text>
      </view>
      <view 
        class="tab-item" 
        :class="{ active: activeTab === 'income' }"
        @click="switchTab('income')"
      >
        <text>收入分类</text>
      </view>
    </view>
    
    <!-- 弹出层遮罩 -->
    <view class="modal-mask" v-if="showForm" @click="closeForm"></view>
    
    <!-- 添加/编辑表单弹出层 -->
    <view class="modal-form" v-if="showForm">
      <view class="modal-header">
        <text class="modal-title">{{isEditing ? '编辑分类' : '添加分类'}}</text>
        <text class="iconfont icon-close" @click="closeForm"></text>
      </view>
      
      <view class="modal-content">
        <view class="form-item">
          <text class="label">分类名称</text>
          <input 
            class="input" 
            v-model="formData.name" 
            placeholder="请输入分类名称"
            maxlength="20"
          />
        </view>
        
        <view class="form-item">
          <text class="label">分类图标</text>
          <view class="icon-selector">
            <view 
              class="icon-option" 
              v-for="icon in availableIcons" 
              :key="icon"
              :class="{ active: formData.category_icon === icon }"
              @click="formData.category_icon = icon"
            >
              <text class="iconfont" :class="icon"></text>
            </view>
          </view>
        </view>
        
        <view class="form-item">
          <text class="label">排序</text>
          <input 
            class="input" 
            v-model="formData.sort" 
            type="number"
            placeholder="请输入排序数字"
          />
        </view>
        
        <view class="form-buttons">
          <button class="cancel-btn" @click="closeForm">取消</button>
          <button class="submit-btn" @click="submitForm">保存</button>
        </view>
      </view>
    </view>
    
    <!-- 分类列表 -->
    <view class="category-section">
      <view class="section-header">
        <text class="section-title">{{activeTab === 'expense' ? '支出分类' : '收入分类'}}</text>
        <text class="category-count">{{currentCategories.length}}个分类</text>
      </view>
      
      <view class="category-list">
        <view 
          class="category-item" 
          v-for="(item, index) in currentCategories" 
          :key="index"
        >
          <view class="category-left">
            <text class="iconfont category-icon" :class="item.category_icon || (activeTab === 'expense' ? ICONFONT_CLASSES.DINING : ICONFONT_CLASSES.SALARY_CERTIFICATION)"></text>
            <text class="category-name">{{item.name || item.name}}</text>
          </view>
          <view class="category-right">
            <text class="iconfont icon-edit" @click="editCategory(item, index)"></text>
            <text class="iconfont icon-delete" @click="deleteCategory(item, index)"></text>
          </view>
        </view>
        
        <!-- 空状态 -->
        <view class="empty-state" v-if="currentCategories.length === 0 && !showForm">
          <text class="empty-text">暂无分类数据</text>
          <text class="empty-tip">点击下方按钮添加分类</text>
        </view>
      </view>
    </view>
    
    <!-- 添加分类按钮 -->
    <view class="add-category-btn" v-if="!showForm">
      <button class="add-btn" @click="addCategory">添加分类</button>
    </view>
  </view>
  
  </view>
</template>

<script>
import categoryApi from '@/api/category';
import { ICONFONT_CLASSES } from '@/common/iconfont.js';

export default {
  data() {
    return {
      activeTab: 'expense', // 默认显示支出分类
      incomeCategories: [],
      expenseCategories: [],
      loading: false,
      currentBookId: '', // 当前账本ID
      showForm: false, // 是否显示表单
      isEditing: false, // 是否为编辑模式
      editingId: '', // 正在编辑的分类ID
      formData: {
        name: '',
        category_icon: ICONFONT_CLASSES.DINING,
        sort: 0,
        book_id: '',
        type: 'expense'
      },
      availableIcons: Object.values(ICONFONT_CLASSES)
    }
  },
  onLoad(options) {
    // 优先使用页面传递的bookID参数
    if (options.bookId) {
      this.currentBookId = options.bookId;
    } else {
      // 如果没有传递参数，则从Session中获取
      this.currentBookId = uni.getStorageSync('currentBookId');
    }
  },
  
  onShow() {
    this.fetchCategories();
  },
  methods: {
    // 获取分类列表
    async fetchCategories() {
      try {
        this.loading = true;
        
        // 验证当前账本ID
        if (!this.currentBookId) {
          uni.showToast({
            title: '请先选择账本',
            icon: 'none'
          });
          return;
        }
        
        // 获取收入分类
        const incomeRes = await categoryApi.getCategories('income', this.currentBookId);
        console.log('收入分类API响应:', incomeRes);
        if (incomeRes.code === 1 || incomeRes.code === '1' || incomeRes.code === 200) {
          this.incomeCategories = Array.isArray(incomeRes.data) ? incomeRes.data : [];
          // 过滤掉 visible=0 的分类
          this.incomeCategories = this.incomeCategories.filter(item => item.visible !== 0);
        } else {
          this.incomeCategories = [];
        }
        
        // 获取支出分类
        const expenseRes = await categoryApi.getCategories('expense', this.currentBookId);
        console.log('支出分类API响应:', expenseRes);
        if (expenseRes.code === 1 || expenseRes.code === '1' || expenseRes.code === 200) {
          this.expenseCategories = Array.isArray(expenseRes.data) ? expenseRes.data : [];
          // 过滤掉 visible=0 的分类
          this.expenseCategories = this.expenseCategories.filter(item => item.visible !== 0);
        } else {
          this.expenseCategories = [];
        }
        
        // 将分类数据存入session
        this.saveCategoriesToSession();
        
        
      } catch (error) {
        console.error('获取分类列表错误:', error);
        uni.showToast({
          title: '获取分类失败',
          icon: 'none'
        });
      } finally {
        this.loading = false;
      }
    },
    
    // 添加分类
    addCategory() {
      this.showForm = true;
      this.isEditing = false;
      this.editingId = '';
      this.resetForm();
      this.formData.type = this.activeTab === 'expense' ? '-1' : '1';
    },
    
    // 编辑分类
    editCategory(item, index) {
      console.log('编辑分类被点击，item参数:', item, 'index:', index);
      console.log('当前分类列表:', this.currentCategories);
      
      // 安全检查：确保item存在且有id
      if (!item) {
        console.error('编辑分类时item参数为undefined或null');
        // 尝试从当前分类列表中获取
        if (index >= 0 && index < this.currentCategories.length) {
          item = this.currentCategories[index];
          console.log('从列表中重新获取item:', item);
        }
      }
      
      if (!item || !item.id) {
        uni.showToast({
          title: '分类数据异常',
          icon: 'none'
        });
        console.error('编辑分类时item参数异常:', item);
        return;
      }
      
      this.showForm = true;
      this.isEditing = true;
      this.editingId = item.id;
      this.formData = {
        name: item.name || '',
        category_icon: item.category_icon || ICONFONT_CLASSES.DINING,
        sort: item.sort || 0,
        book_id: this.currentBookId,
        type: item.type || (this.activeTab === 'expense' ? '-1' : '1')
      };
    },
    
    // 返回上一页
    goBack() {
      uni.navigateBack();
    },
    
    // 关闭表单
    closeForm() {
      this.showForm = false;
      this.isEditing = false;
      this.editingId = '';
      this.resetForm();
    },
    
    // 重置表单
    resetForm() {
      this.formData = {
        name: '',
        category_icon: ICONFONT_CLASSES.DINING,
        sort: 0,
        book_id: this.currentBookId,
        type: this.activeTab === 'expense' ? '-1' : '1'
      };
    },
    
    // 提交表单
    async submitForm() {
      if (!this.formData.name.trim()) {
        uni.showToast({
          title: '请输入分类名称',
          icon: 'none'
        });
        return;
      }
      
      if (!this.formData.book_id) {
        uni.showToast({
          title: '请先选择账本',
          icon: 'none'
        });
        return;
      }
      
      try {
        let result;
        if (this.isEditing) {
          result = await categoryApi.editCategory(this.editingId, this.formData);
        } else {
          result = await categoryApi.addCategory(this.formData);
        }
        
        if (result.code === 1 || result.code === '1' || result.code === 200) {
          uni.showToast({
            title: this.isEditing ? '更新成功' : '添加成功',
            icon: 'success'
          });
          
          // 关闭表单并刷新列表
          this.closeForm();
          this.fetchCategories();
        } else {
          throw new Error(result.message || result.msg || (this.isEditing ? '更新失败' : '添加失败'));
        }
      } catch (error) {
        console.error('操作分类错误:', error);
        uni.showToast({
          title: error.message || (this.isEditing ? '更新失败' : '添加失败'),
          icon: 'none'
        });
      }
    },
    
    // 切换标签
    switchTab(tab) {
      this.activeTab = tab;
      // 切换标签时重置表单类型
      if (this.showForm) {
        this.formData.type = tab === 'expense' ? '-1' : '1';
      }
    },
    
    async deleteCategory(item, index) {
      console.log('删除分类被点击，item参数:', item, 'index:', index);
      console.log('当前分类列表:', this.currentCategories);
      
      // 安全检查：确保item存在且有id
      if (!item) {
        console.error('删除分类时item参数为undefined或null');
        // 尝试从当前分类列表中获取
        if (index >= 0 && index < this.currentCategories.length) {
          item = this.currentCategories[index];
          console.log('从列表中重新获取item:', item);
        }
      }
      
      if (!item || !item.id) {
        uni.showToast({
          title: '分类数据异常',
          icon: 'none'
        });
        console.error('删除分类时item参数异常:', item);
        return;
      }
      
      uni.showModal({
        title: '提示',
        content: `确定要删除分类"${item.name || ''}"吗？`,
        success: async (res) => {
          if (res.confirm) {
            try {
              const result = await categoryApi.delCategory(item.id);
              if (result.code === 1 || result.code === '1' || result.code === 200) {
                uni.showToast({
                  title: '删除成功',
                  icon: 'success'
                });
                // 重新加载分类列表
                this.fetchCategories();
              } else {
                throw new Error(result.message || result.msg || '删除失败');
              }
            } catch (error) {
              console.error('删除分类错误:', error);
              uni.showToast({
                title: error.message || '删除失败',
                icon: 'none'
              });
            }
          }
        }
      });
    },
    
    // 将分类数据存入session
    saveCategoriesToSession() {
      try {
        // 保存收入分类数据
        uni.setStorageSync('incomeCategories', this.incomeCategories);
        
        // 保存支出分类数据
        uni.setStorageSync('expenseCategories', this.expenseCategories);
        
        console.log('分类数据已存入session');
        console.log('收入分类数量:', this.incomeCategories.length);
        console.log('支出分类数量:', this.expenseCategories.length);
      } catch (error) {
        console.error('保存分类数据到session失败:', error);
      }
    }
  },
  computed: {
    currentCategories() {
      return this.activeTab === 'expense' ? this.expenseCategories : this.incomeCategories;
    }
  }
}
</script>

<style>
/* 引入iconfont图标库 */
@import '@/static/iconfont/iconfont.css';
</style>

<style scoped>
.categories-page {
  min-height: 100vh;
  background-color: #f5f5f5;
}

.categories-container {

}

.categories-page .type-tabs {
  margin: 20px;
}

/* 弹出层样式 */
.categories-page .modal-mask {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
}

.categories-page .modal-form {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 85%;
  max-width: 400px;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  z-index: 1000;
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translate(-50%, -60%);
  }
  to {
    opacity: 1;
    transform: translate(-50%, -50%);
  }
}

.categories-page .modal-form .modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #f5f5f5;
}

.categories-page .modal-form .modal-header .modal-title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.categories-page .modal-form .modal-header .icon-close {
  font-size: 18px;
  color: #999;
  padding: 5px;
}

.categories-page .modal-form .modal-content {
  padding: 20px;
  max-height: 70vh;
  overflow-y: auto;
}

.categories-page .modal-form .form-item {
  margin-bottom: 20px;
}

.categories-page .modal-form .form-item .label {
  display: block;
  font-size: 14px;
  color: #333;
  margin-bottom: 8px;
  font-weight: 500;
}

.categories-page .modal-form .form-item .input {
  width: 100%;
  height: 40px;
  border: 1px solid #e5e5e5;
  border-radius: 6px;
  padding: 0 12px;
  font-size: 14px;
  box-sizing: border-box;
}

.categories-page .modal-form .form-item .input:focus {
  border-color: #07C160;
}

.categories-page .modal-form .icon-selector {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}

.categories-page .modal-form .icon-option {
  height: 50px;
  border: 1px solid #e5e5e5;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: #666;
  transition: all 0.3s;
}

.categories-page .modal-form .icon-option.active {
  background-color: #f0f9f4;
  border-color: #07C160;
  color: #07C160;
}

.categories-page .modal-form .form-buttons {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.categories-page .modal-form .form-buttons .cancel-btn {
  flex: 1;
  background-color: #f5f5f5;
  color: #666;
  border: none;
  border-radius: 6px;
  height: 40px;
  font-size: 14px;
}

.categories-page .modal-form .form-buttons .submit-btn {
  flex: 1;
  background-color: #07C160;
  color: white;
  border: none;
  border-radius: 6px;
  height: 40px;
  font-size: 14px;
}

/* 顶部切换标签样式 */
.categories-page .type-tabs {
  display: flex;
  background-color: white;
  border-radius: 10px;
  margin-bottom: 20px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.categories-page .type-tabs .tab-item {
  flex: 1;
  text-align: center;
  padding: 15px;
  font-size: 15px;
  color: #666;
  transition: all 0.3s;
  position: relative;
}

.categories-page .type-tabs .tab-item.active {
  color: #07C160;
  font-weight: 500;
}

.categories-page .type-tabs .tab-item.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 40px;
  height: 3px;
  background-color: #07C160;
  border-radius: 2px;
}

.categories-page .category-section {
  background-color: white;
  border-radius: 10px;
  padding: 15px;
  margin: 0 20px 20px 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* 分类列表头部样式 */
.categories-page .category-section .section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #f5f5f5;
}

.categories-page .category-section .section-header .section-title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.categories-page .category-section .section-header .category-count {
  font-size: 13px;
  color: #999;
}

.categories-page .category-section .section-title {
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 15px;
  color: #333;
}

.categories-page .category-section .category-list .category-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
}

.categories-page .category-section .category-list .category-item:last-child {
  border-bottom: none;
}

.categories-page .category-section .category-list .category-item .category-left {
  display: flex;
  align-items: center;
  flex: 1;
}

.categories-page .category-section .category-list .category-item .category-left .category-icon {
  width: 40px;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f0f9f4;
  border-radius: 20px;
  margin-right: 10px;
  font-size: 20px;
  color: #07C160;
}

.categories-page .category-section .category-list .category-item .category-left .category-name {
  font-size: 15px;
  color: #333;
}

.categories-page .category-section .category-list .category-item .category-right {
  display: flex;
  align-items: center;
}

.categories-page .category-section .category-list .category-item .category-right .iconfont {
  margin-left: 15px;
  font-size: 18px;
  color: #999;
  padding: 8px;
}

.categories-page .category-section .category-list .category-item .category-right .iconfont:active {
  background-color: #f5f5f5;
  border-radius: 4px;
}

/* 空状态样式 */
.categories-page .category-section .category-list .empty-state {
  text-align: center;
  padding: 40px 20px;
}

.categories-page .category-section .category-list .empty-state .empty-text {
  display: block;
  font-size: 15px;
  color: #999;
  margin-bottom: 8px;
}

.categories-page .category-section .category-list .empty-state .empty-tip {
  display: block;
  font-size: 13px;
  color: #ccc;
}

.categories-page .add-category-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 100;
}

.categories-page .add-category-btn .add-btn {
  background-color: #07C160;
  color: white;
  border: none;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  font-size: 14px;
  box-shadow: 0 4px 12px rgba(7, 193, 96, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  line-height: 20px;
}

.categories-page .add-category-btn .add-btn:active {
  transform: scale(0.95);
  box-shadow: 0 2px 8px rgba(7, 193, 96, 0.4);
}

/* iconfont图标样式 */
.iconfont {
  font-family: "iconfont";
}

.icon-edit:before { content: "\e601"; }
.icon-delete:before { content: "\e67d"; }
</style>