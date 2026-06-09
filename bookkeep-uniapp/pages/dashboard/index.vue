
<template>
  <view class="page-container">
    <!-- 顶部导航栏 -->
    <uni-nav-bar
      title="科科记账"
      background-color="#07C160"
      color="#FFFFFF"
      :fixed="true"
      :status-bar="true"
    >
      <view slot="right" class="nav-right">
        <view class="share-btn" @click="showShare">
          <text class="iconfont icon-fenxiang"></text>
        </view>
      </view>
    </uni-nav-bar>
    
    <view class="dashboard" @scrolltolower="onReachBottom">
      <!-- 账本选择和年月选择器 -->
      <view class="selection-row">
        <!-- 账本选择器 -->
        <view class="ledger-selector">
          <view class="selector-content" @click="navigateToBooks">
            <text class="selector-text">{{currentLedger.name || '选择账本'}}</text>
            <text class="dropdown-icon">▼</text>
          </view>
        </view>
        
        <!-- 年月选择器 -->
        <view class="month-selector">
          <view class="date-picker" @click="showDatePicker = !showDatePicker">
            <text class="selector-text">{{currentMonth}}</text>
            <text class="dropdown-icon">▼</text>
          </view>
          
          <!-- 日期选择器弹层 -->
          <view class="date-picker-popup" v-if="showDatePicker">
            <view class="popup-mask" @click="showDatePicker = false"></view>
            <view class="popup-content">
              <view class="popup-header">
                <text class="popup-title">选择日期</text>
                <text class="close-btn" @click="showDatePicker = false">×</text>
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
                  <text>‹</text>
                </view>
                <text class="current-year">{{ currentYear }}</text>
                <view class="year-btn next-year" @click="changeYear(1)">
                  <text>›</text>
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
      </view>
    
    <!-- 顶部统计信息 -->
    <view class="header">
      <!-- 第一行：支出 -->
      <view class="row first-row">
        <view class="stat-item">
          <text class="label">{{ periodType === 'year' ? '年' : '月' }}支出</text>
          <text class="amount expense">{{expense}}</text>
        </view>
      </view>
      
      <!-- 第二行：收入和结余 -->
      <view class="row second-row">
        <view class="stat-item">
          <text class="label">{{ periodType === 'year' ? '年' : '月' }}收入</text>
          <text class="amount income">{{income}}</text>
        </view>
        <view class="balance-info">
          <text class="balance-label">{{ periodType === 'year' ? '年' : '月' }}结余</text>
          <text class="balance-amount" :class="{'income': parseFloat(balance) >= 0, 'expense': parseFloat(balance) < 0}">
            {{balance}}
          </text>
        </view>
      </view>
    </view>
    
    <!-- 悬浮的记一笔按钮 -->
    <view class="float-add-btn" @click="navigateTo('record')">
      <text class="icon">+</text>
      <text class="label">记一笔</text>
    </view>
    
    <!-- 预算卡片 -->
    <view class="budget-card" @click="navigateToBudget">
      <view class="budget-header">
        <text class="budget-title">{{ periodType === 'year' ? '年预算' : '本月预算' }}</text>
        <text class="budget-remaining income"><text class="remaining-text">剩余</text>  {{remainingBudget}}</text>
      </view>
      <view class="budget-progress">
        <view class="progress-bar">
          <view class="progress-fill" :style="{width: budgetProgress + '%'}"></view>
        </view>
      </view>
      <view class="budget-footer">
        <view class="budget-info">
          <text class="budget-total">总预算 {{totalBudget}}</text>
          <text class="budget-daily">剩余日均 {{dailyRemaining}}</text>
        </view>
        <text class="budget-percentage">{{budgetProgress}}%</text>
      </view>
    </view>
    
    <!-- 最近记录 -->
    <view class="recent-records">
      <view class="record-list">
        <view v-for="(group, date) in groupedRecords" :key="date" class="date-card">
          <view class="card-header">
            <text class="date-title">{{date}}</text>
            <view class="date-total">
              <text class="income-total">收 {{calculateDayTotal(group, 'income')}}</text>
              <text class="expense-total">支 {{calculateDayTotal(group, 'expense')}}</text>
            </view>
          </view>
          
          <view class="card-body">
            <view class="record-item" v-for="(item, index) in group" :key="index" @click="navigateToRecordDetail(item.id)">
              <view class="icon-column">
                <text class="iconfont category-icon" :class="item.category_icon || (item.type == '1' ? 'icon-gongziqiarenzheng' : 'icon-canyin')"></text>
              </view>
              <view class="middle-content">
                <text class="category">{{item.category_name}}</text>
                <text class="time">{{item.transaction_date ? item.transaction_date.split(' ')[1] : ''}}</text>
              </view>
              <view class="right-content">
                <text class="amount" :class="{'income': item.type == '1', 'expense': item.type == '-1'}">
                  {{item.type == '1' ? '+' : ''}}{{item.amount}}
                </text>
                <text class="remark">{{item.remark || ''}}</text>
              </view>
            </view>
          </view>
        </view>
      </view>
    </view>
    
    <!-- 加载更多提示 -->
    <view class="loading-more" v-if="loadingMore">
      <text>加载中...</text>
    </view>
    
    <!-- 没有更多数据提示 -->
    <view class="no-more" v-if="!pagination.hasMore && records.length > 0">
      <text>没有更多数据了</text>
    </view>
  </view>
  
  <!-- 分享组件 -->
  <share-component ref="shareComponent" :shareData="shareData"></share-component>
</view>
</template>

<script>
import booksApi from '@/api/books';
import recordApi from '@/api/record';
import budgetApi from '@/api/budget';
import ShareComponent from '@/components/common/share-component.vue';

export default {
  components: {
    ShareComponent
  },
  data() {
    return {
      navBarHeight: 44, // 默认导航栏高度
      ledgers: [],
      currentLedger: {},
      currentMonth: this.formatMonth(new Date()),
      balance: '0.00',
      income: '0.00',
      expense: '0.00',
      records: [],
      loading: false,
      error: null,
      pagination: {
        pageNum: 1,
        pageSize: 20,
        hasMore: true
      },
      loadingMore: false,
      // 预算相关数据
      monthlyBudget: 5000.00, // 本月总预算
      budgetUsed: 0.00, // 已使用预算
      
      // 日期选择器状态
      showDatePicker: false,
      currentYear: new Date().getFullYear(),
      dateMode: 'month', // 'month' or 'year'
      periodType: 'month', // 'month' or 'year'
      
      // 分享相关数据
      shareData: {
        title: '科科记账',
        desc: '简单好用的记账小程序，让我帮你管理财务',
        path: '/pages/dashboard/index',
        imageUrl: '/static/logo.png'
      }
    }
  },
  created() {
    console.log('Dashboard页面创建，开始获取账本列表...');
    this.fetchLedgers();
  },
  onShow() {
    console.log('Dashboard页面显示，刷新数据...');
    // 避免重复调用账本列表，只在没有当前账本时才获取账本列表
    if (this.currentLedger && this.currentLedger.id) {
      // 已经有当前账本，只刷新记录、统计和预算数据
      // 从其他页面返回时，强制刷新记录列表以获取最新数据
      this.fetchRecords(true);
      this.fetchMonthSummary();
      this.fetchBudgetData();
    } else if (!this.loading) {
      // 没有当前账本且不在加载中，才获取账本列表
      this.fetchLedgers();
    }
  },
  
  // 小程序分享给好友
  onShareAppMessage(res) {
    console.log('触发分享给好友', res);
    const currentLedgerId = this.currentLedger?.id || '';
    
    // 确保数据有效
    const title = `科科记账 - 面向个人用户与小微企业的全场景记账工具`;
    const desc = `科科记账是一款简洁的记账小程序，覆盖收支记录、预算管理、财务分析、报表生成等核心需求，力求极简，专注个人记账`;
    const path = currentLedgerId ? `/pages/dashboard/index?bookId=${currentLedgerId}` : '/pages/dashboard/index';

    return {
      title: title,
      desc: desc,
      path: path,
      imageUrl: '/static/logo.png'
    };
  },
  
  // 小程序分享到朋友圈
  onShareTimeline() {
    console.log('触发分享到朋友圈');
    const currentLedgerId = this.currentLedger?.id || '';
    
    // 确保数据有效且不为空
    const title = `科科记账一款简洁的记账小程序，覆盖收支记录、预算管理、财务分析、报表生成等核心需求，力求极简，专注个人记账`;
    
    return {
      title: title,
      imageUrl: '/static/logo.png',
      query: currentLedgerId ? { bookId: currentLedgerId } : undefined
    };
  },
  methods: {
    // 获取账本列表
    async fetchLedgers() {
      try {
        this.loading = true;
        const res = await booksApi.getBooksList();
		
        console.log('账本列表API返回数据:', res);
        
        // 根据 myRequest 的实际响应结构判断成功
        if (res.code == 1 || res.code == '1' || res.code == 200) {
          // 根据实际返回数据结构处理：{code: 1, data: [{...}, {...}]}
          // 现在res.data直接是账本数组
          this.ledgers = Array.isArray(res.data) ? res.data : [];

          // 通用账本ID设置：处理多种可能的数据结构
          // 优先查找有 is_default=1 的账本，然后找第一个账本
          let defaultLedger = this.ledgers.find(item => 
            item.is_default === 1 || item.default === 1 || item.isDefault === 1
          );
          let firstLedger = this.ledgers[0];
          
          // 优先选择默认账本，否则选择第一个账本
          this.currentLedger = defaultLedger || firstLedger || {};
          
          console.log('账本列表:', this.ledgers);
          console.log('当前账本:', this.currentLedger);
          
          // 获取账本后立即获取数据
          if (this.currentLedger && this.currentLedger.id) {
            // 设置当前账本ID到缓存中
            console.log('设置当前账本ID到缓存中:', this.currentLedger.id);
            uni.setStorageSync('currentBookId', this.currentLedger.id);

            this.fetchRecords();
            this.fetchMonthSummary();
            this.fetchBudgetData();
          } else {
            console.warn('未找到有效的账本ID，使用默认空账本');
            // 设置默认空数据
            this.records = [];
            this.monthSummary = {
              income: '0.00',
              expense: '0.00',
              balance: '0.00'
            };
          }
        } else {
          throw new Error(res.message || res.msg || '获取账本列表失败');
        }
      } catch (error) {
        console.error('获取账本列表错误:', error);
        this.error = error.message || '获取账本失败';
        uni.showToast({
          title: this.error,
          icon: 'none'
        });
      } finally {
        this.loading = false;
      }
    },
    
    // 获取记录列表
    async fetchRecords(refresh = true) {
      try {
        if (refresh) {
          this.loading = true;
          this.pagination.pageNum = 1;
        } else {
          this.loadingMore = true;
        }
        const params = {
          book_id: this.currentLedger?.id,
          period_type: this.periodType,
          period_date: this.currentMonth,
          pageNum: this.pagination.pageNum,
          pageSize: this.pagination.pageSize,
          sortField: 'transaction_date', // 按交易日期排序
          sortOrder: 'desc' // 倒序排序（最新的在前）
        };
        
        const result = await recordApi.getRecords(params);
        console.log('index返回数据：');
		console.log(result)
		
        // 更新分页信息
        this.pagination = {
          ...this.pagination,
          ...result.pagination
        };
        
        // 更新记录列表
        if (refresh) {
          this.records = result.records;
        } else {
          this.records = [...this.records, ...result.records];
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
    
    // 加载更多数据
    async loadMoreRecords() {
      if (this.loadingMore || !this.pagination.hasMore) return;
      
      this.pagination.pageNum += 1;
      await this.fetchRecords(false);
    },
    
    // 滚动到底部触发加载更多
    onReachBottom() {
      this.loadMoreRecords();
    },
    
    // 获取月度统计
    async fetchMonthSummary() {
      try {
        const params = {
          book_id: this.currentLedger?.id,
		  period_type: this.periodType,
          period_date: this.currentMonth
        };
        const summary = await recordApi.getMonthSummary(params);
        
        // 安全地设置数据，防止undefined错误
        this.income = summary?.income || '0.00';
        this.expense = summary?.expense || '0.00';
        this.balance = summary?.balance || '0.00';
      } catch (error) {
        console.error('获取月度统计失败', error);
        // 设置默认值
        this.income = '0.00';
        this.expense = '0.00';
        this.balance = '0.00';
      }
    },
    
    // 获取预算数据
    async fetchBudgetData() {
      try {
        if (!this.currentLedger?.id) {
          console.log('没有有效的账本ID，跳过获取预算数据');
          return;
        }
        
        const res = await budgetApi.getBudgetData(this.currentLedger.id, this.periodType,this.currentMonth);
   
        if (res.code === 1 || res.code === 200) {
          const budgetData = res.data || {};
          // 更新预算数据 - 使用API返回的实际字段名
          this.monthlyBudget = parseFloat(budgetData.budget_amount || 3000);
          this.budgetUsed = parseFloat(budgetData.budget_used_amount || 0);
		  
        } else {
			
          console.warn('获取预算数据失败:', res.message || res.msg);
          // 使用默认值
          this.monthlyBudget = 3000;
          this.budgetUsed = 0;
		  
        }
      } catch (error) {
		  
        console.error('获取预算数据失败:', error);
        // 出错时使用默认值
        this.monthlyBudget = 3000;
        this.budgetUsed = 0;
      }
	  
    },
    
    calculateDayTotal(records, type) {
      const typeValue = type == 'income' ? '1' : '-1';
      return records
        .filter(item => item.type == typeValue)
        .reduce((sum, item) => sum + parseFloat(item.amount || 0), 0)
        .toFixed(2);
    },
    
    formatMonth(date) {
      const year = date.getFullYear();
      const month = (date.getMonth() + 1).toString().padStart(2, '0');
      return `${year}-${month}`;
    },
    changeMonth(e) {
      this.currentMonth = e.detail.value;
      console.log('切换到月份:', this.currentMonth);
      // 重新加载数据
      this.fetchRecords(true);
      this.fetchMonthSummary();
      this.fetchBudgetData();
    },
    
    changeLedger(e) {
      this.currentLedger = this.ledgers[e.detail.value];
      console.log('切换到账本:', this.currentLedger.name);
	
	  uni.setStorageSync('currentBookId',this.currentLedger.id)
	  
      // 切换账本后重新加载数据
      this.fetchRecords(true);
      this.fetchMonthSummary();
      this.fetchBudgetData();
    },
    
    // 跳转到账本列表页面
    navigateToBooks() {
      // 监听账本选择事件
      uni.$on('bookSelected', this.handleBookSelected);
      
      uni.navigateTo({
        url: `/pages/books/index?currentBookId=${this.currentLedger?.id || ''}`
      });
    },
    
    // 处理账本选择
    handleBookSelected(book) {
      this.currentLedger = book;
	  uni.setStorageSync('currentBookId',this.currentLedger.id)
	  
      this.fetchRecords(true);
      this.fetchMonthSummary();
      this.fetchBudgetData();
      // 移除事件监听
      uni.$off('bookSelected', this.handleBookSelected);
    },
    navigateTo(page) {
      if (page == 'record') {
        // 直接跳转到添加记录页面
        uni.navigateTo({
          url: `/pages/record/add`
        })
      }else {
        // 其他页面的导航逻辑
        const tabBarPages = ['dashboard', 'statistics']
        if (tabBarPages.includes(page)) {
          uni.switchTab({
            url: `/pages/${page}/index`
          })
        } else {
          uni.navigateTo({
            url: `/pages/${page}/index`
          })
        }
      }
    },

    // 跳转到账单详情页面
    navigateToRecordDetail(recordId) {
      if (recordId) {
        uni.navigateTo({
          url: `/pages/record/detail?id=${recordId}`
        });
      }
    },
    
    // 格式化金额显示
    formatAmount(amount) {
      if (isNaN(amount)) return '0.00';
      return parseFloat(amount).toFixed(2);
    },
    
    // 跳转到预算管理页面
    navigateToBudget() {
      uni.navigateTo({
        url: '/pages/budget/index'
      });
    },
    
    // 年份切换
    changeYear(delta) {
      this.currentYear += delta;
    },
    
    // 检查是否为当前选中月份
    isCurrentMonth(month) {
      const [currentYear, currentMonth] = this.currentMonth.split('-');
      return parseInt(currentYear) === this.currentYear && parseInt(currentMonth) === month;
    },
    
    // 选择月份
    selectMonth(month) {
      const monthStr = month.toString().padStart(2, '0');
      this.currentMonth = `${this.currentYear}-${monthStr}`;
      this.periodType = 'month';
      this.showDatePicker = false;
      // 重新加载数据
      this.fetchRecords(true);
      this.fetchMonthSummary();
      this.fetchBudgetData();
    },
    
    // 检查是否为当前选中年份
    isCurrentYear(year) {
      const [currentYear] = this.currentMonth.split('-');
      return parseInt(currentYear) === year;
    },
    
    // 选择年份
    selectYear(year) {
      this.currentYear = year;
      this.currentMonth = `${year}`;
      this.periodType = 'year';
      this.showDatePicker = false;
      // 重新加载数据
      this.fetchRecords(true);
      this.fetchMonthSummary();
      this.fetchBudgetData();
    },
    
    // 显示分享弹窗
    showShare() {
      // 更新分享数据，包含当前账本信息和统计数据
      this.shareData = {
        title: `科科记账 - 面向个人用户与小微企业的全场景记账工具`,
        desc: `科科记账是一款简洁的记账小程序，覆盖收支记录、预算管理、财务分析、报表生成等核心需求，力求极简，专注个人记账`,
        path: '/pages/dashboard/index',
        imageUrl: '/static/logo.png'
      };
      
      // 调用分享组件的方法
      this.$refs.shareComponent.showSharePopupMethod();
    }

  },
  computed: {
    groupedRecords() {
      const groups = {};
      const sortedDates = [];
      
      this.records.forEach(item => {
        // 提取日期部分（去掉时间），添加安全检查
        const date = item.transaction_date ? item.transaction_date.split(' ')[0] : '';
        if (date) {
          if (!groups[date]) {
            groups[date] = [];
            sortedDates.push(date);
          }
          groups[date].push(item);
        }
      });
      
      // 按日期倒序排序（最新的在前）
      sortedDates.sort((a, b) => {
        const dateA = new Date(a);
        const dateB = new Date(b);
        return dateB - dateA;
      });
      
      // 按排序后的日期顺序重建对象
      const sortedGroups = {};
      sortedDates.forEach(date => {
        // 确保同一天内的记录也按时间倒序排列
        groups[date].sort((a, b) => {
          const timeA = a.transaction_date || '00:00:00';
          const timeB = b.transaction_date || '00:00:00';
          return timeB.localeCompare(timeA);
        });
        sortedGroups[date] = groups[date];
      });
      
      return sortedGroups;
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
    // 预算相关计算属性
    totalBudget() {
      return this.formatAmount(this.monthlyBudget);
    },
    usedBudget() {
      // 使用API返回的已使用预算数据
      const used = this.budgetUsed || parseFloat(this.expense) || 0;
      return this.formatAmount(used);
    },
    remainingBudget() {
      const remaining = this.monthlyBudget - this.budgetUsed;
      return this.formatAmount(Math.max(0, remaining));
    },
    dailyRemaining() {
      const today = new Date();
      const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
      const daysRemaining = daysInMonth - today.getDate() + 1;
      const remaining = this.monthlyBudget - this.budgetUsed;
      const daily = remaining / daysRemaining;
      return this.formatAmount(Math.max(0, daily));
    },
    budgetProgress() {
      const used = this.budgetUsed || 0;
      const progress = (used / this.monthlyBudget) * 100;
      return Math.min(100, parseFloat(progress.toFixed(2)));
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
  }
}
</script>

<style>
/* 引入iconfont图标库 */
@import '@/static/iconfont/iconfont.css';
</style>

<style scoped>
.page-container {
  height: 100vh;
  background-color: #f5f5f5;
}

.dashboard {
  padding: 20rpx;
  padding-bottom: 100rpx; /* 为底部加载提示留出空间 */
  box-sizing: border-box;
  min-height: calc(100vh - var(--window-top));
}

/* 选择器行样式 */
.selection-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.ledger-selector {
  flex: 1;
  min-width: 0;
  display: flex;
  justify-content: flex-start;
}

.month-selector {
  flex: 1;
  min-width: 0;
  display: flex;
  justify-content: flex-end;
}

.selector-content {
  display: inline-flex;
  align-items: center;
  padding: 8px 12px;
  font-size: 14px;
  font-weight: 500;
  color: #07C160;
  background: transparent;
  border: none;
  transition: all 0.3s ease;
}

.selector-content:active {
  opacity: 0.7;
}

.selector-text {
  margin-right: 6px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-weight: 500;
  color: #07C160;
}

.dropdown-icon {
  font-size: 10px;
  color: #07C160;
  font-weight: bold;
  transition: transform 0.3s ease;
}

.selector-content:active .dropdown-icon {
  transform: rotate(180deg);
}

/* 日期选择器样式 */
.dashboard .month-selector .date-picker {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  cursor: pointer;
}

.dashboard .month-selector .date-picker-popup {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;
}

.dashboard .month-selector .date-picker-popup .popup-mask {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
}

.dashboard .month-selector .date-picker-popup .popup-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #fff;
  border-radius: 16px 16px 0 0;
  padding: 20px;
  animation: slide-up 0.3s ease;
  max-height: 60vh;
  display: flex;
  flex-direction: column;
}

.dashboard .month-selector .date-picker-popup .popup-content .popup-header {
  flex-shrink: 0;
}

.dashboard .month-selector .date-picker-popup .popup-content .mode-switch {
  flex-shrink: 0;
}

.dashboard .month-selector .date-picker-popup .popup-content .year-selector {
  flex-shrink: 0;
}

.dashboard .month-selector .date-picker-popup .popup-content .month-grid,
.dashboard .month-selector .date-picker-popup .popup-content .year-grid {
  flex: 1;
  min-height: 0;
  overflow-y: auto;
  height: 280px; /* 固定高度，保持切换时高度一致 */
}

.dashboard .month-selector .date-picker-popup .popup-content .year-selector + .month-grid {
  height: 240px; /* 月份网格高度 = 总高度280px - 年份选择器高度40px */
}

.dashboard .month-selector .date-picker-popup .popup-content .year-grid {
  height: 280px; /* 年份网格使用完整高度 */
}

.dashboard .month-selector .date-picker-popup .popup-content .popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 15px;
  border-bottom: 1px solid #f5f5f5;
  margin-bottom: 20px;
}

.dashboard .month-selector .date-picker-popup .popup-content .popup-header .popup-title {
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.dashboard .month-selector .date-picker-popup .popup-content .popup-header .close-btn {
  font-size: 24px;
  color: #999;
  cursor: pointer;
}

.dashboard .month-selector .date-picker-popup .popup-content .year-selector {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 0 20px;
}

.dashboard .month-selector .date-picker-popup .popup-content .year-selector .year-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background-color: #f8f9fa;
  cursor: pointer;
  transition: all 0.2s ease;
}

.dashboard .month-selector .date-picker-popup .popup-content .year-selector .year-btn:active {
  background-color: #e9ecef;
}

.dashboard .month-selector .date-picker-popup .popup-content .year-selector .current-year {
  font-size: 20px;
  font-weight: 600;
  color: #333;
}

.dashboard .month-selector .date-picker-popup .popup-content .month-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  padding: 0 10px;
}

.dashboard .month-selector .date-picker-popup .popup-content .month-grid .month-item {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 60px;
  border-radius: 8px;
  background-color: #f8f9fa;
  font-size: 16px;
  color: #666;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.dashboard .month-selector .date-picker-popup .popup-content .month-grid .month-item:active {
  background-color: #e9ecef;
}

.dashboard .month-selector .date-picker-popup .popup-content .month-grid .month-item.active {
  background-color: #07C160;
  color: #fff;
}

/* 模式切换按钮样式 */
.dashboard .month-selector .date-picker-popup .popup-content .mode-switch {
  display: flex;
  background-color: #f8f9fa;
  border-radius: 20px;
  padding: 2px;
  margin-bottom: 20px;
  width: 120px;
  margin-left: auto;
  margin-right: auto;
}

.dashboard .month-selector .date-picker-popup .popup-content .mode-switch .mode-btn {
  flex: 1;
  text-align: center;
  padding: 6px 8px;
  border-radius: 18px;
  font-size: 12px;
  font-weight: 500;
  color: #666;
  cursor: pointer;
  transition: all 0.3s ease;
}

.dashboard .month-selector .date-picker-popup .popup-content .mode-switch .mode-btn.active {
  background-color: #07C160;
  color: #fff;
  box-shadow: 0 2px 4px rgba(7, 193, 96, 0.2);
}

/* 年份网格样式 */
.dashboard .month-selector .date-picker-popup .popup-content .year-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  padding: 0 10px;
}

.dashboard .month-selector .date-picker-popup .popup-content .year-grid .year-item {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 60px;
  border-radius: 8px;
  background-color: #f8f9fa;
  font-size: 16px;
  color: #666;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.dashboard .month-selector .date-picker-popup .popup-content .year-grid .year-item:active {
  background-color: #e9ecef;
}

.dashboard .month-selector .date-picker-popup .popup-content .year-grid .year-item.active {
  background-color: #07C160;
  color: #fff;
}

@keyframes slide-up {
  from {
    transform: translateY(100%);
  }
  to {
    transform: translateY(0);
  }
}

.dashboard .header {
  background-color: white;
  border-radius: 20rpx;
  padding: 40rpx;
  margin-bottom:20rpx;
}

.dashboard .header .row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.dashboard .header .row:last-child {
  margin-bottom: 0;
}

.dashboard .header .first-row {
  justify-content: flex-start;
}

.dashboard .header .second-row {
  justify-content: space-between;
}

.dashboard .header .stat-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.dashboard .header .first-row .stat-item {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 2px;
}

.dashboard .header .stat-item .label {
  font-size: 14px;
  color: #666;
  font-weight: 600;
}

.dashboard .header .second-row .stat-item .label {
  font-size: 14px;
  color: #999;
  font-weight: 500;
}

.dashboard .header .first-row .stat-item .amount {
  font-size: 20px;
  font-weight: 600;
}

.dashboard .header .stat-item .amount {
  font-size: 16px;
}


.dashboard .header .stat-item .amount.income {
  color: #07C160;
}

.dashboard .header .stat-item .amount.expense {
  color: #FF4500;
}

.dashboard .header .balance-info {
  display: flex;
  align-items: center;
  gap: 8px;
  text-align: right;
}

.dashboard .header .balance-info .balance-label {
  font-size: 14px;
  color: #999;
  font-weight: 500;
}

.dashboard .header .balance-info .balance-amount {
  font-size: 16px;
}

.dashboard .header .balance-info .balance-amount.income {
  color: #07C160;
}

.dashboard .header .balance-info .balance-amount.expense {
  color: #FF4500;
}

.dashboard .float-add-btn {
  position: fixed;
  right: 20px;
  bottom: 80px;
  width: 68px;
  height: 68px;
  background: linear-gradient(135deg, #07C160, #05a854);
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  box-shadow: 0 4px 15px rgba(7, 193, 96, 0.3);
  z-index: 100;
  transition: all 0.3s ease;
}

.dashboard .float-add-btn:active {
  transform: scale(0.95);
  box-shadow: 0 2px 8px rgba(7, 193, 96, 0.4);
}

.dashboard .float-add-btn .icon {
  font-size: 28px;
  color: white;
  font-weight: bold;
  line-height: 1;
  margin-bottom: 2px;
}

.dashboard .float-add-btn .label {
  font-size: 11px;
  color: white;
  line-height: 1;
  font-weight: 500;
}

/* 预算卡片样式 */
.dashboard .budget-card {
  background-color: white;
  border-radius: 10px;
  padding: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.dashboard .budget-card .budget-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.dashboard .budget-card .budget-header .budget-title {
  font-size: 16px;
  font-weight: 600;
  color: #999;
}

.dashboard .budget-card .budget-header .budget-remaining {
  font-size: 14px;
  font-weight: 600;
}

.dashboard .budget-card .budget-header .budget-remaining .remaining-text {
  font-size: 12px;
  color: #999;
  font-weight: 500;
  margin-right: 5px;
}

.dashboard .budget-card .budget-header .budget-remaining.income {
  color: #07C160;
}

.dashboard .budget-card .budget-progress {
  margin-bottom: 5px;
}

.dashboard .budget-card .budget-progress .progress-bar {
  width: 100%;
  height: 6px;
  background-color: #f0f0f0;
  border-radius: 3px;
  overflow: hidden;
}

.dashboard .budget-card .budget-progress .progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #07C160, #05a854);
  border-radius: 3px;
  transition: width 0.3s ease;
}

.dashboard .budget-card .budget-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dashboard .budget-card .budget-footer .budget-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.dashboard .budget-card .budget-footer .budget-info .budget-total {
  font-size: 13px;
  color: #666;
}

.dashboard .budget-card .budget-footer .budget-info .budget-daily {
  font-size: 13px;
  color: #666;
}

.dashboard .budget-card .budget-footer .budget-percentage {
  font-size: 13px;
  font-weight: 600;
  color: #666666;
}

.dashboard .recent-records .record-list{
  margin-top: 10px;
}

.dashboard .recent-records .record-list .date-card {
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  margin-bottom: 15px;
  overflow: hidden;
}

.dashboard .recent-records .record-list .date-card .card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 15px;
  background-color: #f9f9f9;
  border-bottom: 1px solid #f0f0f0;
}

.dashboard .recent-records .record-list .date-card .card-header .date-title {
  font-size: 14px;
  font-weight: bold;
  color: #333;
}

.dashboard .recent-records .record-list .date-card .card-header .date-total {
  display: flex;
  gap: 10px;
}

.dashboard .recent-records .record-list .date-card .card-header .date-total .income-total {
  color: #07C160;
  font-size: 13px;
}

.dashboard .recent-records .record-list .date-card .card-header .date-total .expense-total {
  color: #FF4500;
  font-size: 13px;
}

.dashboard .recent-records .record-list .date-card .card-body {
  padding: 0 15px;
}

.dashboard .recent-records .record-list .date-card .record-item {
  display: flex;
  padding: 15px 0;
  border-bottom: 1px solid #f5f5f5;
  transition: all 0.2s ease;
  cursor: pointer;
}

.dashboard .recent-records .record-list .date-card .record-item:last-child {
  border-bottom: none;
}

.dashboard .recent-records .record-list .date-card .record-item:active {
  background-color: #f8f8f8;
  transform: scale(0.98);
}

.dashboard .recent-records .record-list .date-card .record-item .icon-column {
  width: 40px;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-right: 10px;
  background-color: #f0f9f4;
  border-radius: 20px;
}

.dashboard .recent-records .record-list .date-card .record-item .icon-column .category-icon {
  font-size: 24px;
  background-color: #05a854;
  color: #f0f9f4;
  border-radius: 44rpx;
  padding:4rpx 20rpx;
}

.dashboard .recent-records .record-list .date-card .record-item .middle-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.dashboard .recent-records .record-list .date-card .record-item .middle-content .category {
  font-size: 15px;
  color: #333;
  margin-bottom: 4px;
}

.dashboard .recent-records .record-list .date-card .record-item .middle-content .date {
  font-size: 12px;
  color: #999;
}

.dashboard .recent-records .record-list .date-card .record-item .right-content {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: center;
  min-width: 80px;
}

.dashboard .recent-records .record-list .date-card .record-item .right-content .amount {
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 4px;
}

.dashboard .recent-records .record-list .date-card .record-item .right-content .amount.income {
  color: #07C160;
}

.dashboard .recent-records .record-list .date-card .record-item .right-content .amount.expense {
  color: #FF4500;
}

.dashboard .recent-records .record-list .date-card .record-item .right-content .remark {
  font-size: 12px;
  color: #999;
}

/* 加载更多提示 */
.dashboard .loading-more {
  text-align: center;
  padding: 20rpx 0;
}

.dashboard .loading-more text {
  font-size: 24rpx;
  color: #999;
}

/* 没有更多数据提示 */
.dashboard .no-more {
  text-align: center;
  padding: 20rpx 0;
}

.dashboard .no-more text {
  font-size: 24rpx;
  color: #999;
}

/* iconfont图标样式 */
.iconfont {
  font-size: 20px;
  color: #666;
}

.category-icon {
  font-size: 24px;
}

/* 导航栏标题居中 */
.page-container ::v-deep .uni-navbar__header-title {
  text-align: center !important;
  justify-content: center !important;
  display: flex !important;
  align-items: center !important;
}

.page-container ::v-deep .uni-navbar__content {
  justify-content: center !important;
}

/* 导航栏右侧分享按钮 */
.nav-right {
  padding-right: 15px;
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
</style>