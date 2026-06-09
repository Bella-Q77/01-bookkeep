<template>
  <view class="record-container">
    <!-- 顶部导航栏 -->
    <uni-nav-bar 
      title="账单查询" 
      background-color="#07C160" 
      color="#FFFFFF" 
      status-bar="true"
      fixed="true"
      left-icon="back"
      @clickLeft="goBack"
    ></uni-nav-bar>

    

    <!-- 筛选条件区域 -->
    <view class="filter-card">
      <!-- 顶部行：账单类型选择 + 搜索框 -->
      <view class="filter-header">
        <view class="filter-selector" @click="showTypeSelect = true">
          <text class="filter-text">{{ getFilterText() }}</text>
          <uni-icons type="down" size="14" color="#666"></uni-icons>
        </view>
        
        <!-- 类型选择弹层 -->
        <view class="type-select-popup" v-if="showTypeSelect">
          <view class="popup-mask" @click="showTypeSelect = false"></view>
          <view class="popup-content">
            <view class="popup-header">
              <text>选择账单类型</text>
              <uni-icons type="closeempty" size="20" color="#999" @click="showTypeSelect = false"></uni-icons>
            </view>
            <view class="type-title">收支类型</view>
            <view class="type-list-horizontal">
              <view 
                class="type-item" 
                :class="{ active: recordType === 'all' }"
                @click.stop="selectType('all')"
              >
                <text>全部</text>
              </view>
              <view 
                class="type-item" 
                :class="{ active: recordType === 'income' }"
                @click.stop="selectType('income')"
              >
                <text>收入</text>
              </view>
              <view 
                class="type-item" 
                :class="{ active: recordType === 'expense' }"
                @click.stop="selectType('expense')"
              >
                <text>支出</text>
              </view>
            </view>

            <view class="category-list">
              <text class="category-title">选择分类</text>
              <view class="category-items">
                <view 
                  class="category-item"
                  v-for="(category, index) in categories"
                  :key="category.id || index"
                  :data-index="index"
                  :class="{ active: selectedCategoryId === category.id }"
                  @click.stop="selectCategory(category, $event)"
                >
                  <text>{{ category.name || category.category_name }}</text>
                </view>
                <!-- 调试信息 -->
                <view v-if="categories.length === 0" class="debug-info">
                  <text>暂无分类数据</text>
                </view>
              </view>
            </view>

            <view class="popup-footer">
              <text class="reset-btn" @click="resetFilter">重置</text>
              <text class="confirm-btn" @click="confirmFilter">确认</text>
            </view>
          </view>
        </view>
        
        <!-- 搜索框 -->
        <view class="search-box" :class="{'search-box-active': showSearchBox}">
          <view class="search-icon" @click="toggleSearchBox">
            <uni-icons type="search" size="18" color="#07C160"></uni-icons>
          </view>
          <input type="text" v-model="keyword" placeholder="搜索关键字" confirm-type="search" @confirm="searchRecords"
            :focus="showSearchBox" />
          <view class="search-actions" v-if="showSearchBox">
            <text class="search-btn" @click="searchRecords">搜索</text>
            <text class="cancel-btn" @click="toggleSearchBox">取消</text>
          </view>
        </view>
      </view>

      <!-- 筛选条件行：日期选择 + 收支统计 -->
      <view class="filter-body">
        <view class="filter-row">
          <!-- 自定义日期选择器 -->
          <view class="date-picker-container">
            <view class="date-picker" @click="showDatePicker = !showDatePicker">
              <text class="date-text">{{ formattedDate }}</text>
              <uni-icons type="down" size="14" color="#666"></uni-icons>
            </view>
            
            <!-- 日期选择器弹层 -->
            <view class="date-picker-popup" v-if="showDatePicker">
              <view class="popup-mask" @click="showDatePicker = false"></view>
              <view class="popup-content">
                <view class="popup-header">
                  <text class="popup-title">选择日期</text>
                  <uni-icons type="closeempty" size="20" color="#999" @click="showDatePicker = false"></uni-icons>
                </view>
                
                <!-- 按年/按月切换 -->
                <view class="mode-switch">
                  <view 
                    class="mode-btn" 
                    :class="{ active: dateMode === 'month' }"
                    @click="dateMode = 'month'"
                  >
                    <text>按月</text>
                  </view>
                  <view 
                    class="mode-btn" 
                    :class="{ active: dateMode === 'year' }"
                    @click="dateMode = 'year'"
                  >
                    <text>按年</text>
                  </view>
                </view>
                
                <!-- 年份切换 -->
                <view class="year-selector" v-if="dateMode === 'month'">
                  <view class="year-btn prev-year" @click="changeYear(-1)">
                    <uni-icons type="left" size="16" color="#07C160"></uni-icons>
                  </view>
                  <text class="current-year">{{ currentYear }}</text>
                  <view class="year-btn next-year" @click="changeYear(1)">
                    <uni-icons type="right" size="16" color="#07C160"></uni-icons>
                  </view>
                </view>
                
                <!-- 月份网格 -->
                <view class="month-grid" v-if="dateMode === 'month'">
                  <view 
                    class="month-item" 
                    v-for="month in monthList" 
                    :key="month.value"
                    :class="{ active: isCurrentMonth(month.value) }"
                    @click="selectMonth(month.value)"
                  >
                    <text>{{ month.label }}</text>
                  </view>
                </view>
                
                <!-- 年份网格 -->
                <view class="year-grid" v-if="dateMode === 'year'">
                  <view 
                    class="year-item" 
                    v-for="year in yearRange" 
                    :key="year"
                    :class="{ active: isCurrentYear(year) }"
                    @click="selectYear(year)"
                  >
                    <text>{{ year }}</text>
                  </view>
                </view>
              </view>
            </view>
          </view>
      
          <view class="stats-summary">
            <view class="stat-item">
              <text class="stat-value income-value"><text class="stat-label">收入</text> ¥{{ calculateTotalIncome() }}</text>
            </view>
            <view class="stat-divider"></view>
            <view class="stat-item">
              <text class="stat-value expense-value"><text class="stat-label">支出</text> ¥{{ calculateTotalExpense() }}</text>
            </view>
          </view>
        </view>
      </view>
    </view>

    <!-- 账单列表区域 -->
    <view class="record-list">
      <template v-if="sortedRecords.length > 0">
        <view class="record-item" v-for="(record, index) in sortedRecords" :key="index" @click="viewRecordDetail(record.id)">
          <view class="record-icon-container">
            <view class="record-icon" :class="record.type == '-1' ? 'expense-bg' : 'income-bg'">
              <uni-icons :type="getCategoryIcon(record.category)" size="24" color="#FFF"></uni-icons>
            </view>
          </view>
          <view class="record-info">
            <view class="record-main">
              <view class="record-left">
                <text class="record-category">{{ record.category_name }}</text>
                <text class="record-date">{{ formatRecordDate(record.transaction_date) }}</text>
              </view>
              <view class="record-amount-container">
                <text class="record-amount" :class="record.type == '-1' ? 'expense' : 'income'">
                  {{ record.type == '-1' ? '' : '+' }}{{ record.amount }}
                </text>
                <text class="record-remark" v-if="record.remark">{{ record.remark }}</text>
              </view>
            </view>
          </view>
        </view>
      </template>

      <view class="empty-state" v-else>
        <uni-icons type="info-filled" size="64" color="#ddd"></uni-icons>
        <text>暂无符合条件的账单记录</text>
        <view class="empty-action" @click="resetFilters">重置筛选条件</view>
      </view>
    </view>

    <!-- 空弹窗占位 -->
  </view>
</template>

<script>
import { uniIcons } from '@dcloudio/uni-ui';
import { uniPopup } from '@dcloudio/uni-ui';
import recordApi from '@/api/record';
import categoryApi from '@/api/category';

export default {
  components: {
    uniIcons,
    uniPopup
  },
  data() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const currentDate = `${year}-${month}`;
    
    return {
      // 筛选条件
      currentDate: currentDate,
      formattedDate: `${year}年${month}月`,
      periodType: 'month', // 'month' or 'year'
      recordType: 'all', // 'all', 'expense' or 'income'
      categoryIndex: 0,
      incomeCategories: [], // 收入分类列表（通过API获取）
      expenseCategories: [], // 支出分类列表（通过API获取）
      categories: [], // 当前显示的分类列表
      allIncomeCategories: [], // 保存完整的收入分类数据（包含ID）
      allExpenseCategories: [], // 保存完整的支出分类数据（包含ID）
      selectedCategoryId: null, // 当前选中的分类ID
      keyword: '',
      showSearchBox: false,
      showTypeSelect: false, // 控制类型选择弹层显示
      selectedCategory: '全部分类', // 当前选中的分类
      
      // 账单数据
      records: [],
      loading: false,
      loadingMore: false,
      pageNum: 1,
      pageSize: 20,
      hasMore: true,
      debounceTimer: null,
      
      // 月度统计数据
      monthSummary: {
        income: '0.00',
        expense: '0.00',
        balance: '0.00'
      },
      
      // 日期选择器状态
      showDatePicker: false,
      currentYear: parseInt(currentDate.split('-')[0]),
      dateMode: 'month' // 'month' or 'year'
    };
  },
  computed: {
    sortedRecords() {
      // 直接返回按日期降序排序的记录数组
      return [...this.records].sort((a, b) => new Date(b.date) - new Date(a.date));
    },
    
    // 年份范围（当前年份前后5年）
    yearRange() {
      const currentYear = this.currentYear;
      const years = [];
      for (let i = currentYear - 5; i <= currentYear + 5; i++) {
        years.push(i);
      }
      return years;
    },
    
    // 月份列表（1-12月）
    monthList() {
      const months = [];
      for (let i = 1; i <= 12; i++) {
        months.push({
          value: i,
          label: `${i}月`
        });
      }
      return months;
    }
  },
  methods: {
    // 获取月度统计
    async fetchMonthSummary() {
      try {
        const params = {
          book_id: uni.getStorageSync('currentBookId') || '',
          period_type: this.periodType,
          period_date: this.periodType === 'year' ? this.currentDate.substring(0, 4) : this.currentDate
        };
        const summary = await recordApi.getMonthSummary(params);
        
        // 安全地设置数据，防止undefined错误
        this.monthSummary = {
          income: summary?.income || '0.00',
          expense: summary?.expense || '0.00',
          balance: summary?.balance || '0.00'
        };
      } catch (error) {
        console.error('获取月度统计失败', error);
        // 设置默认值
        this.monthSummary = {
          income: '0.00',
          expense: '0.00',
          balance: '0.00'
        };
      }
    },
    
    // 计算总收入（从月度统计获取）
    calculateTotalIncome() {
      return this.monthSummary.income;
    },
    
    // 计算总支出（从月度统计获取）
    calculateTotalExpense() {
      return this.monthSummary.expense;
    },
    
    // 获取筛选文本显示
    getFilterText() {
      const typeMap = {
        'all': '全部账单',
        'expense': '支出',
        'income': '收入'
      };
      return typeMap[this.recordType] || '全部账单';
    },
    
    // 选择账单类型
    selectType(type) {
      this.recordType = type;
      
      // 根据类型更新分类列表
      if (type === 'income') {
        this.categories = this.allIncomeCategories;
      } else if (type === 'expense') {
        this.categories = this.allExpenseCategories;
      } else {
        // 合并收入和支出分类，去除重复项
        const allCategories = [...this.allIncomeCategories, ...this.allExpenseCategories];
        // 按ID去重，如果没有ID则按名称去重
        const uniqueCategories = allCategories.filter((item, index, self) =>
          index === self.findIndex((t) => 
            (t.id && item.id && t.id === item.id) || 
            (!t.id && !item.id && (t.name === item.name || t.category_name === item.category_name))
          )
        );
        this.categories = uniqueCategories;
      }
      
      // 重置分类选择
      this.selectedCategoryId = null;
      this.selectedCategory = '全部';
      
      console.log('selectType:', type, 'categories:', this.categories);
    },
    
    selectCategory(category, event) {
      // 阻止事件冒泡
      if (event) {
        event.stopPropagation();
      }
      

      // 如果参数是undefined，尝试从事件中获取数据
      if (!category && event && event.currentTarget) {
        // 尝试从DOM元素获取相关信息
        const index = event.currentTarget.dataset.index;
        if (index !== undefined && this.categories[index]) {
          category = this.categories[index];
          console.log('从事件中获取category:', category);
        }
      }

      // 添加安全检查，防止category为undefined
      if (!category) {
        console.warn('selectCategory: category is undefined');
        console.warn('调用栈:', new Error().stack);
        return;
      }
      
      // 如果是同一个分类，则取消选择；否则选中该分类
      const categoryId = category.id;
      const categoryName = category.name || category.category_name;
      
      if (this.selectedCategoryId === categoryId) {
        this.selectedCategoryId = null;
        this.selectedCategory = '全部';
      } else {
        this.selectedCategoryId = categoryId;
        this.selectedCategory = categoryName;
      }

    },
    
    confirmFilter() {
      console.log('确认筛选', {
        recordType: this.recordType,
        selectedCategory: this.selectedCategory,
        selectedCategoryId: this.selectedCategoryId
      });
      this.showTypeSelect = false;
      this.applyFilters();
    },
    
    resetFilter() {
      this.recordType = 'all';
      this.selectedCategoryId = null;
      this.selectedCategory = '全部';
      // 重置分类列表 - 合并收入和支出分类，去除重复项
      const allCategories = [...this.allIncomeCategories, ...this.allExpenseCategories];
      const uniqueCategories = allCategories.filter((item, index, self) =>
        index === self.findIndex((t) => 
          (t.id && item.id && t.id === item.id) || 
          (!t.id && !item.id && (t.name === item.name || t.category_name === item.category_name))
        )
      );
      this.categories = uniqueCategories;
      
      console.log('resetFilter - categories:', this.categories);
    },
    
    // 从API获取记录数据
    async fetchRecords(refresh = true) {
      try {
        if (refresh) {
          this.loading = true;
          this.pageNum = 1;
        } else {
          this.loadingMore = true;
        }
        
        const params = {
          pageNum: this.pageNum,
          pageSize: this.pageSize,
          book_id: uni.getStorageSync('currentBookId') || '',
          period_type: this.periodType,
          period_date: this.periodType === 'year' ? this.currentDate.substring(0, 4) : this.currentDate,
          ...(this.recordType !== 'all' && { type: this.recordType === 'income' ? 1 : -1 }),
          ...(this.selectedCategoryId && { category_id: this.selectedCategoryId }),
          ...(this.keyword && { keyword: this.keyword })
        };
        
        const result = await recordApi.getRecords(params);
        
        // 更新分页信息
        this.hasMore = result.pagination?.hasMore || false;
        
        // 更新记录列表
        if (refresh) {
          this.records = result.records || [];
        } else {
          this.records = [...this.records, ...(result.records || [])];
        }
        
        // 递增页码
        if (this.hasMore) {
          this.pageNum++;
        }
      } catch (error) {
        this.error = error.message || '获取记录失败';
        uni.showToast({
          title: this.error,
          icon: 'none'
        });
      } finally {
        this.loading = false;
        this.loadingMore = false;
      }
    },
    
    toggleSearchBox() {
      this.showSearchBox = !this.showSearchBox;
      if (!this.showSearchBox) {
        this.keyword = '';
        this.applyFilters();
      }
    },
    
    handleDateChange(e) {
       const selected = e.detail.value;
        this.currentDate = selected;

        // 正确更新 formattedDate
        const [year, month] = selected.split('-');
        this.formattedDate = this.periodType === 'year' 
            ? `${year}年` 
            : `${year}年${month}月`;

        this.applyFilters();
    },
    
    handleCategoryChange(e) {
      this.categoryIndex = e.detail.value;
      this.applyFilters();
    },
    
    searchRecords() {
      this.applyFilters();
    },
    
    // 空方法占位
    
    applyFilters() {
      // 直接调用API获取新数据
      this.fetchRecords(true); // true表示刷新数据
      this.fetchMonthSummary(); // 同时获取月度统计
    },
    
    // 格式化记录日期为更友好的显示
    formatRecordDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      if (isNaN(date.getTime())) return '无效日期';

      const today = new Date();
      const yesterday = new Date(today);
      yesterday.setDate(today.getDate() - 1);

      const isToday = date.toDateString() === today.toDateString();
      const isYesterday = date.toDateString() === yesterday.toDateString();

      if (isToday) return '今天';
      if (isYesterday) return '昨天';

      const month = date.getMonth() + 1;
      const day = date.getDate();
      return `${month}月${day}日`;
    },
    
    getCategoryIcon(category) {
      const iconMap = {
        '餐饮': 'food',
        '交通': 'car',
        '购物': 'cart',
        '娱乐': 'star',
        '居家': 'home',
        '医疗': 'help',
        '教育': 'book',
        '工资': 'wallet',
        '奖金': 'gift',
        '投资': 'upload'
      };
      return iconMap[category] || 'info';
    },
    
    viewRecordDetail(id) {
      uni.navigateTo({
        url: `/pages/record/detail?id=${id}`
      });
    },
    
    // 加载分类数据
    async loadCategories() {
      try {
        // 从Session获取默认账本ID
        const defaultBookId = uni.getStorageSync('currentBookId') || '';
        
        // 加载收入分类
        const incomeResponse = await categoryApi.getCategories('income', defaultBookId);
        this.allIncomeCategories = incomeResponse.data || [];
        this.incomeCategories = ['全部', ...this.allIncomeCategories.map(item => item.name || item.category_name)];
        
        // 加载支出分类
        const expenseResponse = await categoryApi.getCategories('expense', defaultBookId);
        this.allExpenseCategories = expenseResponse.data || [];
        this.expenseCategories = ['全部', ...this.allExpenseCategories.map(item => item.name || item.category_name)];
        
        // 初始化全部分类（合并收入和支出分类，保持对象完整性）
        const allCategories = [...this.allIncomeCategories, ...this.allExpenseCategories];
        // 按ID去重，如果没有ID则按名称去重
        const uniqueCategories = allCategories.filter((item, index, self) =>
          index === self.findIndex((t) => 
            (t.id && item.id && t.id === item.id) || 
            (!t.id && !item.id && (t.name === item.name || t.category_name === item.category_name))
          )
        );
        this.categories = uniqueCategories;
        
        console.log('加载分类数据完成:', {
          income: this.allIncomeCategories,
          expense: this.allExpenseCategories,
          all: this.categories
        });
      } catch (error) {
        console.error('加载分类数据失败:', error);
      
      }
    },
    
    // 年份切换
    changeYear(delta) {
      this.currentYear += delta;
    },
    
    // 检查是否为当前选中月份
    isCurrentMonth(month) {
      const [currentYear, currentMonth] = this.currentDate.split('-');
      return parseInt(currentYear) === this.currentYear && parseInt(currentMonth) === month;
    },
    
    // 选择月份
    selectMonth(month) {
      const monthStr = month.toString().padStart(2, '0');
      this.currentDate = `${this.currentYear}-${monthStr}`;
      this.formattedDate = `${this.currentYear}年${month}月`;
      this.showDatePicker = false;
      this.applyFilters();
    },
    
    // 检查是否为当前选中年份
    isCurrentYear(year) {
      const [currentYear] = this.currentDate.split('-');
      return parseInt(currentYear) === year;
    },
    
    // 选择年份
    selectYear(year) {
      this.currentYear = year;
      this.currentDate = `${year}`;
      this.formattedDate = `${year}年`;
      this.showDatePicker = false;
      this.periodType = 'year'; // 设置为按年筛选
      this.applyFilters(); // 调用API接口
    },
    
    // 返回上一页
    goBack() {
      uni.navigateBack();
    },
    
    // 选择月份时设置periodType为month
    selectMonth(month) {
      const monthStr = month.toString().padStart(2, '0');
      this.currentDate = `${this.currentYear}-${monthStr}`;
      this.formattedDate = `${this.currentYear}年${month}月`;
      this.periodType = 'month'; // 设置为按月筛选
      this.showDatePicker = false;
      this.applyFilters();
    },
  },

  async onLoad() {
    // 初始化时加载分类数据
    try {
      await this.loadCategories();
      console.log('onLoad - 分类数据加载完成:', this.categories);
    } catch (error) {
      console.error('onLoad - 分类数据加载失败:', error);
    }
    
    // 获取记录数据和月度统计
    this.fetchRecords();
    this.fetchMonthSummary();
  },

  onPullDownRefresh() {
    // 下拉刷新
    this.fetchRecords(true);
    setTimeout(() => {
      uni.stopPullDownRefresh();
    }, 1000);
  },

  onReachBottom() {
    // 上拉加载更多
    if (this.hasMore && !this.loadingMore) {
      this.fetchRecords(false);
    }
  }
}
</script>

<style>
.record-container {
  padding: 0;
  background-color: #f5f5f5;
  min-height: 100vh;
}

.record-container .filter-card {
  border-radius: 0;
  padding: 15px 0 0 0;
  position: sticky;
  top: 44px; /* 在导航栏下方 */
  z-index: 99;
  background-color: #f5f5f5;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

.record-container .filter-card .type-select-popup {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;
}

.record-container .filter-card .type-select-popup .popup-mask {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
}

.record-container .filter-card .type-select-popup .popup-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #fff;
  border-radius: 16px 16px 0 0;
  padding: 20px;
  animation: slide-up 0.3s ease;
}

.record-container .filter-card .type-select-popup .popup-content .popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 15px;
  border-bottom: 1px solid #f5f5f5;
  font-size: 18px;
  font-weight: bold;
}

.record-container .filter-card .type-select-popup .popup-content .type-title {
  font-size: 13px;
  color: #999;
  margin-top: 5px;
}

.record-container .filter-card .type-select-popup .popup-content .type-list-horizontal {
  display: flex;
  justify-content: space-between;
  margin-top: 5px;
  gap: 8px;
}

.record-container .filter-card .type-select-popup .popup-content .type-list-horizontal .type-item {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 8px 0;
  border-radius: 4px;
  background-color: #f5f5f5;
  font-size: 14px;
}

.record-container .filter-card .type-select-popup .popup-content .type-list-horizontal .type-item.active {
  background-color: #07C160;
  color: #fff;
}

.record-container .filter-card .type-select-popup .popup-content .type-list-horizontal .type-item.active .uni-icons {
  color: #fff !important;
}

.record-container .filter-card .type-select-popup .popup-content .category-list {
  margin-top: 20px;
}

.record-container .filter-card .type-select-popup .popup-content .category-list .category-title {
  font-size: 13px;
  color: #999;
  margin-bottom: 20rpx;
}

.record-container .filter-card .type-select-popup .popup-content .category-list .category-items {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 10px;
}

.record-container .filter-card .type-select-popup .popup-content .category-list .category-items .category-item {
  padding: 8px 12px;
  border-radius: 4px;
  background-color: #f5f5f5;
  font-size: 14px;
}

.record-container .filter-card .type-select-popup .popup-content .category-list .category-items .category-item.active {
  background-color: #07C160;
  color: #fff;
}

.debug-info {
  padding: 10px;
  background-color: #fff3cd;
  color: #856404;
  border-radius: 4px;
  margin: 10px 0;
  text-align: center;
  font-size: 14px;
}

.record-container .filter-card .type-select-popup .popup-content .popup-footer {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
  padding-top: 15px;
  border-top: 1px solid #f5f5f5;
}

.record-container .filter-card .type-select-popup .popup-content .popup-footer .reset-btn {
  color: #999;
  padding: 8px 20px;
}

.record-container .filter-card .type-select-popup .popup-content .popup-footer .confirm-btn {
  background-color: #07C160;
  color: #fff;
  padding: 8px 20px;
  border-radius: 4px;
}

@keyframes slide-up {
  from {
    transform: translateY(100%);
  }
  to {
    transform: translateY(0);
  }
}

.record-container .filter-card .filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 15px;
}

.record-container .filter-card .filter-header .filter-selector {
  background-color: #fff;
  border-radius: 6px;
  padding: 0px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-width: 80px;
  max-width: 120px;
  border: 1px solid #eee;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  height: 32px;
}

.record-container .filter-card .filter-header .filter-selector .filter-text {
  font-size: 14px;
  color: #333;
  margin-right: 5px;
}

.record-container .filter-card .filter-header .search-box {
  flex: 2;
  margin: 0 10px;
  display: flex;
  align-items: center;
  background-color: #fff;
  border-radius: 6px;
  padding: 0 12px;
  min-width: 180px;
  border: 1px solid #eee;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  transition: all 0.2s ease;
  height: 32px;
}

.record-container .filter-card .filter-header .search-box:active,
.record-container .filter-card .filter-header .search-box.search-box-active {
  border-color: #07C160;
}

.record-container .filter-card .filter-header .search-box .search-icon {
  margin-right: 8px;
}

.record-container .filter-card .filter-header .search-box input {
  flex: 1;
  font-size: 14px;
  color: #333;
}

.record-container .filter-card .filter-header .search-box .search-actions {
  display: flex;
  align-items: center;
}

.record-container .filter-card .filter-header .search-box .search-actions .search-btn {
  font-size: 14px;
  color: #07C160;
  margin-left: 10px;
}

.record-container .filter-card .filter-header .search-box .search-actions .cancel-btn {
  font-size: 14px;
  color: #999;
  margin-left: 10px;
}

.record-container .filter-card .filter-body .filter-row {
  display: flex;
  align-items: center;
  background-color: #f5f5f5;
  border-radius: 8px;
  padding: 8px 15px 0 15px;
}

.record-container .filter-card .filter-body .date-picker-container {
  flex: none;
}

.record-container .filter-card .filter-body .date-picker-container .date-picker {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 6px 12px;
  border-radius: 6px;
}

.record-container .filter-card .filter-body .date-picker-container .date-picker .date-text {
  font-size: 14px;
  color: #333;
}

.record-container .filter-card .filter-body .stats-summary {
  flex: 1;
  display: flex;
  align-items: center;
  margin-left: 10px;
  padding: 6px 0;
}

.record-container .filter-card .filter-body .stats-summary .stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
}

.record-container .filter-card .filter-body .stats-summary .stat-item .stat-label {
  font-size: 12px;
  color: #666;
  margin-bottom: 4px;
}

.record-container .filter-card .filter-body .stats-summary .stat-item .stat-value {
  font-size: 14px;
  font-weight: 500;
}

.record-container .filter-card .filter-body .stats-summary .stat-item .income-value {
  color: #07C160;
}

.record-container .filter-card .filter-body .stats-summary .stat-item .expense-value {
  color: #FF4D4F;
}

.record-container .filter-card .filter-body .stats-summary .stat-divider {
  width: 1px;
  height: 24px;
  background-color: #e0e0e0;
  margin: 0 8px;
}

/* 日期选择器弹层样式 */
.record-container .date-picker-popup {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;
}

.record-container .date-picker-popup .popup-mask {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
}

.record-container .date-picker-popup .popup-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #fff;
  border-radius: 16px 16px 0 0;
  padding: 20px;
  animation: slide-up 0.3s ease;
  max-height: 70vh;
  overflow-y: auto;
}

.record-container .date-picker-popup .popup-content .popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 15px;
  border-bottom: 1px solid #f5f5f5;
  margin-bottom: 20px;
}

.record-container .date-picker-popup .popup-content .popup-header .popup-title {
  font-size: 18px;
  font-weight: 600;
  color: #333;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* 年份选择器样式 */
.record-container .date-picker-popup .popup-content .year-selector {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 0 20px;
}

.record-container .date-picker-popup .popup-content .year-selector .year-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background-color: #f8f9fa;
  transition: all 0.2s ease;
}

.record-container .date-picker-popup .popup-content .year-selector .year-btn:active {
  background-color: #e9ecef;
}

.record-container .date-picker-popup .popup-content .year-selector .current-year {
  font-size: 20px;
  font-weight: 600;
  color: #333;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* 月份网格样式 */
.record-container .date-picker-popup .popup-content .month-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  padding: 0 10px;
}

.record-container .date-picker-popup .popup-content .month-grid .month-item {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 60px;
  border-radius: 8px;
  background-color: #f8f9fa;
  font-size: 16px;
  color: #666;
  font-weight: 500;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  transition: all 0.2s ease;
}

.record-container .date-picker-popup .popup-content .month-grid .month-item:active {
  background-color: #e9ecef;
}

.record-container .date-picker-popup .popup-content .month-grid .month-item.active {
  background-color: #07C160;
  color: #fff;
}

/* 按年/按月切换按钮样式 */
.record-container .date-picker-popup .popup-content .mode-switch {
  display: flex;
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 4px;
  margin-bottom: 20px;
}

.record-container .date-picker-popup .popup-content .mode-switch .mode-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 14px;
  color: #666;
  font-weight: 500;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  transition: all 0.2s ease;
}

.record-container .date-picker-popup .popup-content .mode-switch .mode-btn.active {
  background-color: #07C160;
  color: #fff;
}

/* 年份网格样式 */
.record-container .date-picker-popup .popup-content .year-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  padding: 0 10px;
}

.record-container .date-picker-popup .popup-content .year-grid .year-item {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 60px;
  border-radius: 8px;
  background-color: #f8f9fa;
  font-size: 16px;
  color: #666;
  font-weight: 500;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  transition: all 0.2s ease;
}

.record-container .date-picker-popup .popup-content .year-grid .year-item:active {
  background-color: #e9ecef;
}

.record-container .date-picker-popup .popup-content .year-grid .year-item.active {
  background-color: #07C160;
  color: #fff;
}

.record-container .filter-card .search-box {
  margin-top: 10px;
  display: flex;
  align-items: center;
  background-color: #f5f5f5;
  border-radius: 5px;
  padding: 10px;
}

.record-container .filter-card .search-box .search-icon {
  margin-right: 10px;
}

.record-container .filter-card .search-box input {
  flex: 1;
  font-size: 14px;
  color: #333;
}

.record-container .filter-card .search-box .search-actions {
  display: flex;
  align-items: center;
}

.record-container .filter-card .search-box .search-actions .search-btn {
  font-size: 14px;
  color: #07C160;
  margin-left: 10px;
}

.record-container .filter-card .search-box .search-actions .cancel-btn {
  font-size: 14px;
  color: #999;
  margin-left: 10px;
}

.record-container .filter-card .search-box-active {
  background-color: #fff;
}

.record-container .record-list {
  background-color: white;
  border-radius: 16rpx;
  padding: 30rpx;
  box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.05);
  margin: 40rpx;
}

.record-container .record-list .list-header {
  display: flex;
  justify-content: space-between;
  font-weight: bold;
  font-size: 32rpx;
  margin-bottom: 20rpx;
  padding-bottom: 15rpx;
  border-bottom: 1rpx solid #f5f5f5;
}

.record-container .record-list .list-header .count {
  color: #666;
  font-size: 24rpx;
  font-weight: normal;
}

.record-container .record-list .record-item {
  display: flex;
  padding: 24rpx 0;
  border-bottom: 1rpx solid #f5f5f5;
}

.record-container .record-list .record-item .record-icon-container {
  width: 80rpx;
  height: 100%;
  display: flex;
  align-items: flex-start;
  margin-right: 20rpx;
}

.record-container .record-list .record-item .record-icon-container .record-icon {
  width: 60rpx;
  height: 60rpx;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.record-container .record-list .record-item .income-bg {
  background-color: #07C160;
}

.record-container .record-list .record-item .expense-bg {
  background-color: #ff4d4f;
}

.record-container .record-list .record-item .record-info {
  flex: 1;
}

.record-container .record-list .record-item .record-info .record-main {
  display: flex;
  justify-content: space-between;
}

.record-container .record-list .record-item .record-info .record-main .record-left {
  display: flex;
  flex-direction: column;
}

.record-container .record-list .record-item .record-info .record-main .record-left .record-category {
  font-size: 28rpx;
  color: #333;
  font-weight: 500;
  margin-bottom: 4rpx;
}

.record-container .record-list .record-item .record-info .record-main .record-left .record-date {
  color: #999;
  font-size: 24rpx;
}

.record-container .record-list .record-item .record-info .record-main .record-amount-container {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.record-container .record-list .record-item .record-info .record-main .record-amount-container .record-amount {
  font-weight: bold;
  font-size: 30rpx;
}

.record-container .record-list .record-item .record-info .record-main .record-amount-container .record-amount.income {
  color: #07C160;
}

.record-container .record-list .record-item .record-info .record-main .record-amount-container .record-amount.expense {
  color: #FF4D4F;
}

.record-container .record-list .record-item .record-info .record-main .record-amount-container .record-remark {
  font-size: 24rpx;
  color: #999;
  margin-top: 4rpx;
}

.record-container .record-list .record-item .empty-state {
  text-align: center;
  padding: 80rpx 0;
  color: #999;
  font-size: 32rpx;
}

.record-container .record-list .record-item .empty-state::before {
  content: "📊";
  font-size: 60rpx;
  display: block;
  margin-bottom: 20rpx;
}

.record-container .record-list .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.record-container .record-list .empty-state text {
  font-size: 16px;
  color: #999;
  margin-top: 10px;
}

.record-container .record-list .empty-state .empty-action {
  font-size: 14px;
  color: #07C160;
  margin-top: 10px;
}

.record-container .filter-popup {
  background-color: #fff;
  border-radius: 10px 10px 0 0;
  padding: 15px;
}

.record-container .filter-popup .popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.record-container .filter-popup .popup-header .popup-title {
  font-size: 18px;
  color: #333;
}

.record-container .filter-popup .popup-header .popup-close {
  width: 20px;
  height: 20px;
}

.record-container .filter-popup .popup-content {
  margin-top: 10px;
}

.record-container .filter-popup .popup-content .filter-section {
  margin-bottom: 10px;
}

.record-container .filter-popup .popup-content .filter-section .section-title {
  font-size: 16px;
  color: #333;
  margin-bottom: 5px;
}

.record-container .filter-popup .popup-content .filter-section .tab-container {
  display: flex;
}

.record-container .filter-popup .popup-content .filter-section .tab-container .tab-item {
  background-color: #f5f5f5;
  border-radius: 5px;
  padding: 5px 10px;
  margin-right: 10px;
}

.record-container .filter-popup .popup-content .filter-section .tab-container .tab-item.active-tab {
  background-color: #07C160;
  color: #fff;
}

.record-container .filter-popup .popup-content .filter-section .date-mode-selector {
  display: flex;
}

.record-container .filter-popup .popup-content .filter-section .date-mode-selector .mode-item {
  background-color: #f5f5f5;
  border-radius: 5px;
  padding: 5px 10px;
  margin-right: 10px;
}

.record-container .filter-popup .popup-content .filter-section .date-mode-selector .mode-item.active {
  background-color: #07C160;
  color: #fff;
}

.record-container .filter-popup .popup-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.record-container .filter-popup .popup-footer .reset-btn {
  font-size: 14px;
  color: #999;
}

.record-container .filter-popup .popup-footer .confirm-btn {
  font-size: 14px;
  color: #07C160;
}
</style>
