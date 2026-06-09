<template>
  <view class="budget-page">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="预算管理" 
      background-color="#07C160" 
      color="#FFFFFF" 
      status-bar="true"
      fixed="true"
      left-icon="back"
      @clickLeft="goBack"
    ></uni-nav-bar>
    <view class="budget-container">
    <!-- 预算类型切换 -->
    <view class="budget-type">
      <view 
        class="type-item" 
        :class="{ active: budgetType === 'month' }"
        @click="switchBudgetType('month')"
      >
        月度预算
      </view>
      <view 
        class="type-item" 
        :class="{ active: budgetType === 'year' }"
        @click="switchBudgetType('year')"
      >
        年度预算
      </view>
    </view>

    <!-- 总预算设置 -->
    <view class="total-budget">
      <text class="title">{{budgetType === 'month' ? '月度' : '年度'}}总预算</text>
      <view class="input-container">
        <text class="symbol">¥</text>
        <input 
          type="number" 
          :value="budgetAmount" 
          placeholder="0.00" 
          @input="updateTotalBudget"
        />
      </view>
      <!-- 保存按钮 -->
      <button class="save-btn" @click="saveBudget" :disabled="savingLoading">
        {{ savingLoading ? '保存中...' : '保存预算' }}
      </button>
    </view>

    <!-- 分类预算列表 -->
    <view class="category-list">
      <view class="list-header">
        <text class="title">分类预算</text>
        <view class="add-btn" @click="showAddForm = true">
          <text>+ 添加</text>
        </view>
      </view>

      <view class="list-content">
        <view 
          class="category-item" 
          v-for="(item, index) in categories" 
          :key="index"
          @click="showActionMenu(item, index)"
        >
          <view class="category-info">
            <text class="icon iconfont" :class="item.category_icon"></text>
            <text class="name">{{item.category_name}}</text>
          </view>
          <view class="item-actions">
            <text class="amount">¥ {{item.amount}}</text>
            <text class="more-icon">⋮</text>
          </view>
        </view>
      </view>
    </view>
</view>


    <!-- 编辑分类预算表单 -->
    <view class="edit-form" v-if="showEditForm">
      <view class="form-header">
        <text class="form-title">编辑分类预算</text>
        <text class="close-btn" @click="showEditForm = false">×</text>
      </view>

      <view class="form-content">
        <view class="category-info">
          <text class="icon iconfont" :class="editingCategory.category_icon"></text>
          <text class="name">{{editingCategory.category_name}}</text>
        </view>

        <view class="amount-input">
          <text class="symbol">¥</text>
          <input 
            type="number" 
            v-model="editingCategory.amount" 
            placeholder="0.00" 
            @input="formatEditAmount"
          />
        </view>

        <button class="confirm-btn" @click="updateCategory" :disabled="updatingLoading">
          {{ updatingLoading ? '更新中...' : '确认更新' }}
        </button>
      </view>
    </view>

    <!-- 添加分类预算表单 -->
    <view class="add-form" v-if="showAddForm">
      <view class="form-header">
        <text class="form-title">添加分类预算</text>
        <text class="close-btn" @click="showAddForm = false">×</text>
      </view>

      <view class="form-content">
        <picker 
          mode="selector" 
          :range="allCategories" 
          range-key="name" 
          @change="selectCategory"
        >
          <view class="picker">
            {{selectedCategory.name || '请选择分类'}}
          </view>
        </picker>

        <view class="amount-input">
          <text class="symbol">¥</text>
          <input 
            type="number" 
            v-model="newAmount" 
            placeholder="0.00" 
            @input="formatNewAmount"
          />
        </view>

        <button class="confirm-btn" @click="addCategory" :disabled="addingLoading">
          {{ addingLoading ? '添加中...' : '确认添加' }}
        </button>
      </view>
    </view>

    <!-- 操作菜单 -->
    <view class="action-menu" v-if="actionMenuVisible">
      <view class="menu-content">
        <view class="menu-item" @click="handleEdit">编辑</view>
        <view class="menu-item delete" @click="handleDelete">删除</view>
        <view class="menu-item cancel" @click="hideActionMenu">取消</view>
      </view>
    </view>

    <!-- 半透明遮罩层 -->
    <view 
      class="mask" 
      v-if="showAddForm || showEditForm || actionMenuVisible" 
      @click="hideForms"
    ></view>
  </view>
  
  
</template>

<script>
import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue'
import budgetApi from '@/api/budget'

export default {
  components: {
    'uni-icons': uniIcons
  },
  data() {
    return {
      budgetType: 'month', // month or year
      budgetAmount: '0',
      showAddForm: false,
      showEditForm: false,
      selectedCategory: {},
      editingCategory: {},
      editingIndex: -1,
      newAmount: '',
      categories: [],
      allCategories: [],
      loading: false,
      savingLoading: false,
      addingLoading: false,
      updatingLoading: false,
      currentBookId: '', // 当前账本ID
      actionMenuVisible: false,
      selectedItem: null,
      selectedIndex: -1
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
    this.loadBudgetData()
  },
  methods: {
    goBack() {
      uni.navigateBack()
    },
    
    // 切换预算类型
    switchBudgetType(type) {
      if (this.budgetType !== type) {
        this.budgetType = type;
        this.loadBudgetData();
      }
    },
    
    async loadBudgetData() {
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
        
        const res = await budgetApi.getBudgetData(this.currentBookId, this.budgetType);
        if (res.code === 1 || res.code === '1' || res.code === 200) {
          const budgetData = res.data || {};
          
          // 根据API返回的数据结构进行映射
          this.budgetAmount = budgetData.budget_amount || '0';
       
          
          // 处理分类预算列表
          this.categories = Array.isArray(budgetData.budget_category_list) 
            ? budgetData.budget_category_list.map(item => ({
                id: item.id,
                category_name: item.category_name,
                name: item.category_name,
                category_icon: item.category_icon,
                amount: item.budget_amount,
                budget_amount: item.budget_amount,
                budget_used_amount: item.budget_used_amount,
                budget_remain_amount: item.budget_remain_amount
              }))
            : [];
          
          // 如果没有分类数据，使用空数组
          this.allCategories = budgetData.book_category_list || [];
          
        } else {
          // 如果API返回错误，使用默认数据
          this.monthlyBudget = '0';
          this.yearlyBudget = '0';
          this.categories = [];
          this.allCategories = [];
        }
        
      } catch (error) {
        console.error('获取预算数据错误:', error);
        uni.showToast({
          title: '获取预算数据失败',
          icon: 'none'
        });
      } finally {
        this.loading = false;
      }
    },
    
    updateTotalBudget(e) {
      const value = e.detail.value.replace(/[^0-9.]/g, '')
	  this.budgetAmount=value;
    },
    formatNewAmount() {
      this.newAmount = this.newAmount.replace(/[^0-9.]/g, '')
    },
    
    formatEditAmount() {
      this.editingCategory.amount = this.editingCategory.amount.replace(/[^0-9.]/g, '')
    },
    selectCategory(e) {
      this.selectedCategory = this.allCategories[e.detail.value]
    },
    // 编辑分类预算
    editCategory(item, index) {
      this.editingIndex = index;
      this.editingCategory = { ...item };
      this.showEditForm = true;
    },
    
    // 删除分类预算
    async deleteCategory(categoryId, index) {
      uni.showModal({
        title: '确认删除',
        content: '确定要删除这个分类预算吗？',
        success: async (res) => {
          if (res.confirm) {
            try {
              const result = await budgetApi.deleteBudgetCategory(categoryId);
              if (result.code === 1 || result.code === '1' || result.code === 200) {
                this.categories.splice(index, 1);
                uni.showToast({
                  title: '删除成功',
                  icon: 'success'
                });
              } else {
                throw new Error(result.message || result.msg || '删除失败');
              }
            } catch (error) {
              uni.showToast({
                title: '删除失败',
                icon: 'none'
              });
            }
          }
        }
      });
    },
    
    // 更新分类预算
    async updateCategory() {
      if (!this.editingCategory.amount) {
        uni.showToast({
          title: '请输入预算金额',
          icon: 'none'
        });
        return;
      }
      try {
        const result = await budgetApi.updateBudgetCategory({
          ...this.editingCategory,
          book_id: this.currentBookId
        });
        
        if (result.code === 1 || result.code === '1' || result.code === 200) {
          this.categories[this.editingIndex] = { ...this.editingCategory };
          this.showEditForm = false;
          this.editingCategory = {};
          this.editingIndex = -1;
          
          uni.showToast({
            title: '更新成功',
            icon: 'success'
          });
        } else {
          throw new Error(result.message || result.msg || '更新失败');
        }
      } catch (error) {
        uni.showToast({
          title: '更新失败',
          icon: 'none'
        });
      }
    },
    
    async addCategory() {
      if (!this.selectedCategory.name || !this.newAmount) {
        uni.showToast({
          title: '请填写完整信息',
          icon: 'none'
        })
        return
      }

      this.addingLoading = true
      try {
        const newCategory = {
		  category_name:this.selectedCategory.name,
		  category_id:this.selectedCategory.id,
          amount: this.newAmount,
          period_type:this.budgetType
        }
        // 调用API添加分类
        const result = await budgetApi.addBudgetCategory({
          ...newCategory,
          book_id: this.currentBookId
        });
        
        if (result.code === 1 || result.code === '1' || result.code === 200) {
          this.categories.push(newCategory);
          this.showAddForm = false;
          this.selectedCategory = {};
          this.newAmount = '';
          
          uni.showToast({
            title: '分类添加成功',
            icon: 'success'
          });
        } else {
          // 显示具体的错误信息
          const errorMsg = result.msg || result.message || '添加分类失败';
          uni.showToast({
            title: errorMsg,
            icon: 'none'
          });
        }
      } catch (error) {
        console.error('添加分类错误:', error);
        uni.showToast({
          title: error.message,
          icon: 'none'
        })
      } finally {
        this.addingLoading = false
      }
    },
    // 显示操作菜单
    showActionMenu(item, index) {
      console.log('显示操作菜单', item, index);
      this.selectedItem = item;
      this.selectedIndex = index;
      this.actionMenuVisible = true;
      console.log('actionMenuVisible设置为:', this.actionMenuVisible);
    },
    
    // 隐藏操作菜单
    hideActionMenu() {
      this.actionMenuVisible = false;
      this.selectedItem = null;
      this.selectedIndex = -1;
    },
    
    // 隐藏所有表单
    hideForms() {
      this.showAddForm = false;
      this.showEditForm = false;
      this.actionMenuVisible = false;
      this.selectedItem = null;
      this.selectedIndex = -1;
    },
    
    // 处理编辑操作
    handleEdit() {
      this.actionMenuVisible = false;
      this.editCategory(this.selectedItem, this.selectedIndex);
    },
    
    // 处理删除操作
    handleDelete() {
      const itemId = this.selectedItem.id;
      const itemIndex = this.selectedIndex;
      this.hideActionMenu();
      this.deleteCategory(itemId, itemIndex);
    },
    
    async saveBudget() {
      this.savingLoading = true
      try {
        const budgetData = {
		  period_type:this.budgetType,
          amount: this.budgetAmount,
          categories: this.categories
        }
        
        // 调用API保存预算
        const result = await budgetApi.saveBudgetData({
          ...budgetData,
          book_id: this.currentBookId
        });
        
        if (result.code === 1 || result.code === '1' || result.code === 200) {
          uni.showToast({
            title: '预算保存成功',
            icon: 'success'
          });
        } else {
          throw new Error(result.message || result.msg || '保存预算失败');
        }
      } catch (error) {
        uni.showToast({
          title: '保存预算失败',
          icon: 'none'
        })
      } finally {
        this.savingLoading = false
      }
    }
  }
}
</script>
<style>
/* 引入iconfont图标库 */
@import '@/static/iconfont/iconfont.css';
</style>
<style scoped>
.budget-page {
  padding: 0 0 20px;
  position: relative;
  background-color: #f7f7f7;
  width: 100vw;
  overflow-x: hidden;
  box-sizing: border-box;
}

/* 调整uni-nav-bar样式，保持顶部居中、两边通栏靠齐 */
.budget-page .uni-nav-bar {
  margin: 0;
  padding: 0;
  width: 100%;
}

.budget-page .budget-container{
	  padding: 30rpx;
	  box-sizing: border-box;

}

.budget-page .budget-type {
  display: flex;
  background-color: white;
  border-radius: 12px;
  margin: 16px 0 16px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.budget-page .budget-type .type-item {
  flex: 1;
  text-align: center;
  padding: 8px 12px;
  color: #666;
  font-size: 14px;
}

.budget-page .budget-type .type-item.active {
  background-color: #07C160;
  color: white;
}

.budget-page .total-budget {
  background-color: white;
  border-radius: 12px;
  padding: 20px;
  margin: 16px 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.budget-page .total-budget .title {
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 15px;
  color: #333;
}

.budget-page .total-budget .input-container {
  display: flex;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #f0f0f0;
}

.budget-page .total-budget .input-container .symbol {
  font-size: 24px;
  color: #07C160;
  margin-right: 10px;
}

.budget-page .total-budget .input-container input {
  flex: 1;
  font-size: 24px;
  font-weight: bold;
  color: #333;
}

.budget-page .category-list {
  background-color: white;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.budget-page .category-list .list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid #f0f0f0;
}

.budget-page .category-list .list-header .title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.budget-page .category-list .list-header .add-btn {
  color: #07C160;
  font-size: 14px;
  font-weight: bold;
  padding: 2px 10px;
  border: 1px solid #07C160;
  border-radius: 16px;
  background-color: transparent;
}

.budget-page .category-list .list-header .add-btn:active {
  background-color: rgba(7, 193, 96, 0.1);
}

.budget-page .category-list .list-content .category-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 0;
  border-bottom: 1px solid #f5f5f5;
}

.budget-page .category-list .list-content .category-item:last-child {
  border-bottom: none;
}

.budget-page .category-list .list-content .category-item .category-info {
  display: flex;
  align-items: center;
}

.budget-page .category-list .list-content .category-item .category-info .icon {
  font-size: 20px;
  margin-right: 12px;
  color: #07C160;
}

.budget-page .category-list .list-content .category-item .category-info .name {
  font-size: 15px;
  color: #333;
}

.budget-page .category-list .list-content .category-item .amount {
  font-weight: bold;
  color: #333;
}

.budget-page .edit-form,
.budget-page .add-form {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: white;
  border-radius: 15px 15px 0 0;
  padding: 20px;
  z-index: 100;
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

.budget-page .edit-form .form-header,
.budget-page .add-form .form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 1px solid #f0f0f0;
}

.budget-page .edit-form .form-header .form-title,
.budget-page .add-form .form-header .form-title {
  font-size: 18px;
  font-weight: bold;
  color: #333;
}

.budget-page .edit-form .form-header .close-btn,
.budget-page .add-form .form-header .close-btn {
  font-size: 24px;
  color: #999;
  width: 30px;
  height: 30px;
  text-align: center;
  line-height: 30px;
}

.budget-page .edit-form .form-content,
.budget-page .add-form .form-content {
  padding: 10px 0;
}

.budget-page .edit-form .category-info,
.budget-page .add-form .category-info {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding: 12px;
  background-color: #f8f8f8;
  border-radius: 8px;
}

.budget-page .edit-form .category-info .icon,
.budget-page .add-form .category-info .icon {
  font-size: 20px;
  margin-right: 12px;
  color: #07C160;
}

.budget-page .edit-form .category-info .name,
.budget-page .add-form .category-info .name {
  font-size: 16px;
  color: #333;
}

.budget-page .edit-form .amount-input,
.budget-page .add-form .amount-input {
  display: flex;
  align-items: center;
  margin-bottom: 25px;
  padding: 12px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
}

.budget-page .edit-form .amount-input .symbol,
.budget-page .add-form .amount-input .symbol {
  font-size: 20px;
  color: #07C160;
  margin-right: 10px;
  font-weight: bold;
}

.budget-page .edit-form .amount-input input,
.budget-page .add-form .amount-input input {
  flex: 1;
  font-size: 16px;
  border: none;
  outline: none;
}

.budget-page .edit-form .confirm-btn,
.budget-page .add-form .confirm-btn {
  background-color: #07C160;
  color: white;
  border-radius: 20px;
  height: 48px;
  line-height: 48px;
  font-size: 16px;
  width: 100%;
  border: none;
}

.budget-page .add-form .picker {
  padding: 12px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  margin-bottom: 20px;
  background-color: #f8f8f8;
}

.budget-page .save-btn {
  margin: 16px 0 0;
  background-color: #07C160;
  color: white;
  border-radius: 20px;
  font-size: 16px;
  box-shadow: 0 2px 8px rgba(7, 193, 96, 0.2);
}

.budget-page .mask {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 99;
}

/* 操作菜单样式 */
.budget-page .action-menu {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background-color: transparent;
}

.budget-page .action-menu .menu-content {
  background-color: white;
  border-radius: 15px 15px 0 0;
  padding: 10px 0;
}

.budget-page .action-menu .menu-item {
  padding: 15px 20px;
  text-align: center;
  font-size: 16px;
  border-bottom: 1px solid #f0f0f0;
}

.budget-page .action-menu .menu-item:last-child {
  border-bottom: none;
}

.budget-page .action-menu .menu-item.delete {
  color: #ff4444;
}

.budget-page .action-menu .menu-item.cancel {
  color: #666;
  font-weight: bold;
}

/* 分类项操作区域样式 */
.budget-page .category-list .list-content .category-item .item-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.budget-page .category-list .list-content .category-item .item-actions .more-icon {
  font-size: 18px;
  color: #999;
  cursor: pointer;
}
</style>