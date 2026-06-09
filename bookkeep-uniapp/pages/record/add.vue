<template>
	<view class="add-record-container">
		<!-- 固定顶部区域 -->
		<view class="fixed-top">
			<!-- 顶部导航栏 -->
			<uni-nav-bar 
			  :title="isEditMode ? '编辑记录' : '添加记录'" 
			  background-color="#07C160" 
			  color="#FFFFFF" 
			  :status-bar="true"
			  :fixed="true"
			  left-icon="back"
			  @click-left="cancelRecord"
			></uni-nav-bar>
			
			<!-- 支出/收入切换区域 -->
			<view class="tab-switch-container">
				<view class="tab-container">
					<view class="tab-item" :class="{ 'active-tab': recordType === 'expense' }"
						@click="switchRecordType('expense')">
						<text>支出</text>
					</view>
					<view class="tab-item" :class="{ 'active-tab': recordType === 'income' }"
						@click="switchRecordType('income')">
						<text>收入</text>
					</view>
				</view>
			</view>

			<!-- 金额显示区域 -->
			<view class="amount-display">
				<!-- 内容区域 -->
				<view class="content-area">
					<text class="amount-value">{{ formattedAmount }}</text>
				</view>
			</view>
		</view>



		<!-- 日期选择器弹窗 -->
		<view class="date-picker-popup" v-if="showDatePicker">
			<view class="date-picker-mask" @click="showDatePicker = false"></view>
			<view class="date-picker-content">
				<view class="date-picker-header">
					<text class="date-picker-cancel" @click="showDatePicker = false">取消</text>
					<text class="date-picker-title">选择日期</text>
					<text class="date-picker-confirm" @click="confirmDateSelection">确定</text>
				</view>
				<picker-view class="date-picker-view" :value="datePickerValue" @change="onDatePickerChange">
					<picker-view-column>
						<view class="picker-item" v-for="(year, index) in years" :key="index">{{ year }}年</view>
					</picker-view-column>
					<picker-view-column>
						<view class="picker-item" v-for="(month, index) in months" :key="index">{{ month }}月
						</view>
					</picker-view-column>
					<picker-view-column>
						<view class="picker-item" v-for="(day, index) in days" :key="index">{{ day }}日</view>
					</picker-view-column>
				</picker-view>
			</view>
		</view>



		<!-- 分类选择区域 -->
		<view class="category-section">
			<text class="section-title">{{ recordType === 'expense' ? '支出分类' : '收入分类' }}</text>
			<view class="category-grid">
				<view class="category-item" v-for="(category, index) in currentCategories" :key="category.id"
					:class="{ 'selected': selectedCategoryId === category.id }" @click="selectCategory(category.id)">
					<view class="category-icon">
						<text class="iconfont" :class="category.category_icon"
							:style="{ color: selectedCategoryId === category.id ? '#FFFFFF' : '#333333' }"></text>
					</view>
					<text class="category-name">{{ category.name }}</text>
				</view>
				<!-- 添加分类按钮 -->
				<view class="category-item add-category-btn" @click="navigateToCategoryPage">
					<view class="category-icon">
						<uni-icons type="plus" size="32" color="#07C160"></uni-icons>
					</view>
					<text class="category-name">编辑分类</text>
				</view>
			</view>

		</view>

		<!-- 键盘占位区域 -->
		<view class="keyboard-placeholder" :style="{height: keyboardVisible ? '460rpx' : '0'}"></view>

		<!-- 数字键盘区域（包含日期选择） -->
		<view class="keyboard-container" v-if="selectedCategoryId || isEditMode" :class="{'keyboard-show': keyboardVisible}">
			
			<!-- 备注输入框 -->
			<view v-if="showRemarkInput" class="remark-wrapper">
				<view class="remark-wrapper-bg">
					<view class="remark-header">
						<view class="back-arrow" @click="hideRemarkInput">
							<uni-icons type="left" size="24" color="#333"></uni-icons>
						</view>
						<text class="remark-title">添加备注</text>
					</view>
					<view class="remark-input-container">
						<input type="text" v-model="remark" placeholder="输入备注内容" :focus="showRemarkInput" />
					</view>
					<view class="confirm-btn" @click="hideRemarkInput">
						<text>确定</text>
					</view>
				</view>
			</view>

			<!-- 日期和账户选择区域 -->
			<view class="date-account-picker">
				<view class="date-value" @click="showDatePicker = true">
					<text>{{ formattedSelectedDate }}</text>
				</view>
				<view class="account-value" @click="toggleAccountPicker">
					<text>{{ selectedAccountName }}</text>
				</view>
				<view class="remark-btn" @click="toggleRemarkInput">
					<uni-icons type="compose" size="18" color="#666"></uni-icons>
				</view>
			</view>

			<!-- 账户选择器弹窗 -->
			<view class="account-picker-popup" v-if="showAccountPicker">
				<view class="account-picker-mask" @click="showAccountPicker = false"></view>
				<view class="account-picker-content">
					<view class="account-picker-header">
						<text class="account-picker-title">选择账户</text>
						<text class="account-picker-close" @click="showAccountPicker = false">×</text>
					</view>
					<view class="account-list">
						<view class="account-item" v-for="account in accounts" :key="account.id" 
							:class="{ 'selected': selectedAccountId === account.id }"
							@click="selectAccount(account)">
							<text class="account-name">{{ account.name }}</text>
						</view>
					</view>
				</view>
			</view>

			<!-- 键盘区域 -->
			<view class="keyboard-row">
				<view class="keyboard-key" @click="inputNumber('1')">1</view>
				<view class="keyboard-key" @click="inputNumber('2')">2</view>
				<view class="keyboard-key" @click="inputNumber('3')">3</view>
				<view class="keyboard-key operator-key" @click="calculate('+')">+</view>
			</view>
			<view class="keyboard-row">
				<view class="keyboard-key" @click="inputNumber('4')">4</view>
				<view class="keyboard-key" @click="inputNumber('5')">5</view>
				<view class="keyboard-key" @click="inputNumber('6')">6</view>
				<view class="keyboard-key operator-key" @click="calculate('-')">-</view>
			</view>
			<view class="keyboard-row">
				<view class="keyboard-key" @click="inputNumber('7')">7</view>
				<view class="keyboard-key" @click="inputNumber('8')">8</view>
				<view class="keyboard-key" @click="inputNumber('9')">9</view>
				<view class="keyboard-key operator-key" @click="calculate('=')">=</view>
			</view>
			<view class="keyboard-row">
				<view class="keyboard-key" @click="inputNumber('.')">.</view>
				<view class="keyboard-key" @click="inputNumber('0')">0</view>
				<view class="keyboard-key delete-key" @click="deleteNumber()">
					<uni-icons type="trash" size="24" color="#333333"></uni-icons>
				</view>
				<view class="keyboard-key complete-key" @click="completeRecord">完成</view>
			</view>
		</view>
	</view>
</template>

<script>
	import uniIcons from '@dcloudio/uni-ui/lib/uni-icons/uni-icons.vue';
	import uniNavBar from '@dcloudio/uni-ui/lib/uni-nav-bar/uni-nav-bar.vue';
	import recordApi from '@/api/record';
	import categoryApi from '@/api/category';
	import assetsApi from '@/api/assets';

	export default {
		components: {
			uniIcons,
			uniNavBar
		},
		data() {
			// 获取当前日期
			const currentDate = new Date();
			const currentYear = currentDate.getFullYear();
			const currentMonth = currentDate.getMonth();
			const currentDay = currentDate.getDate();

			// 生成年份、月份和日期数组
			const years = [];
			for (let i = currentYear - 5; i <= currentYear + 5; i++) {
				years.push(i);
			}

			const months = [];
			for (let i = 1; i <= 12; i++) {
				months.push(i);
			}

			const days = [];
			for (let i = 1; i <= 31; i++) {
				days.push(i);
			}

			return {
				isEditMode: false, // 是否为编辑模式
				recordId: null, // 编辑时的记录ID
				recordType: 'expense', // 'expense' 或 'income'
				amount: '0', // 金额字符串
				remark: '', // 备注
				remarkFocus: false, // 备注输入框是否聚焦
				selectedCategoryId: null, // 选中的分类ID
				selectedCategoryName: '', // 选中的分类名称（用于显示）

				// 日期选择相关
				selectedDate: currentDate,
				showDatePicker: false,
				years: years,
				months: months,
				days: days,
				datePickerValue: [5, currentMonth, currentDay - 1], // 默认选中当前日期
				tempDatePickerValue: [5, currentMonth, currentDay - 1], // 临时存储日期选择器的值
				keyboardVisible: false, // 控制键盘显示动画
				showRemarkInput: false, // 控制备注输入框显示

				// 支出分类 - 初始为空数组，将通过API加载
				expenseCategories: [],

				// 收入分类 - 初始为空数组，将通过API加载
				incomeCategories: [],

				// 账户相关
				selectedAccountId: 0, // 选中的账户ID
				selectedAccountName: '', // 选中的账户名称
				showAccountPicker: false, // 是否显示账户选择器
				accounts: [] // 账户列表，将通过API加载
			};
		},
		computed: {
			// 根据当前记录类型获取对应的分类列表
			currentCategories() {
				return this.recordType === 'expense' ? this.expenseCategories : this.incomeCategories;
			},

			// 格式化金额显示
			formattedAmount() {
				if (this.amount == '0') return '0.00';
				// 处理小数点
				if (this.amount.includes('.')) {
					const parts = this.amount.split('.');
					const decimal = parts[1].padEnd(2, '0').substring(0, 2);
					
					// 编辑模式下，如果小数点后面全是0，只显示整数部分
					if (this.isEditMode && (decimal === '00' || decimal === '0' || /^0+$/.test(decimal))) {
						return parts[0];
					}
					
					return `${parts[0]}.${decimal}`;
				} else {
					return `${this.amount}`;
				}
			},

			// 格式化选中日期显示
			formattedSelectedDate() {
				const month = this.selectedDate.getMonth() + 1;
				const day = this.selectedDate.getDate();
				return `${month}月${day}日`;
			}
		},
		onLoad(options) {
			console.log('页面参数:', options);
			// 处理页面参数
			if (options.edit === 'true' && options.id) {
				this.isEditMode = true;
				this.recordId = options.id;
				console.log('进入编辑模式，记录ID:', this.recordId);
				this.loadEditRecordData();
			} else {
				console.log('进入添加模式');
			}
			
			// 加载支出分类
			this.loadCategories('expense');
			
			// 加载收入分类
			this.loadCategories('income');
			
			// 加载资产账户数据
			this.loadAssetsData();
		},
		
		onShow() {
			console.log('record Add 页面显示，刷新数据...');

			// 加载支出分类
			this.loadCategories('expense');
			
			// 加载收入分类
			this.loadCategories('income');
			
			// 加载资产账户数据
			this.loadAssetsData();
		},
		methods: {
			// 加载编辑记录数据
			async loadEditRecordData() {
				try {
					uni.showLoading({ title: '加载中...' });
					const response = await recordApi.getRecordDetail(this.recordId);
					if (response.code === 1) {
						const record = response.data;
						console.log('加载的编辑记录数据:', record);
						
						// 设置记录数据
						this.recordType = record.type === 1 ? 'income' : 'expense';
						
						// 处理金额：如果金额为负数，转为正数；如果小数点后面全是0，转换为整数格式
						if (record.amount) {
							let amountStr = record.amount.toString();
							
							// 如果金额为负数，转为正数
							if (amountStr.startsWith('-')) {
								amountStr = amountStr.substring(1);
							}
							
							if (amountStr.includes('.')) {
								const parts = amountStr.split('.');
								// 检查小数点后面是否全是0
								if (parts[1] === '00' || parts[1] === '0' || /^0+$/.test(parts[1])) {
									this.amount = parts[0]; // 只保留整数部分
								} else {
									this.amount = amountStr;
								}
								
							} else {
								this.amount = amountStr;
							}
						} else {
							this.amount = '0';
						}
						
						this.remark = record.remark || '';
						this.selectedCategoryId = record.category_id;
						this.selectedAccountId = record.account_id || 1;
						this.selectedAccountName = this.getAccountName(record.account_id || 1);
						
						// 设置日期
						if (record.transaction_date) {
							this.selectedDate = new Date(record.transaction_date);
						}
						
						// 显示键盘 - 使用 $nextTick 确保 DOM 更新完成
						this.$nextTick(() => {
							this.keyboardVisible = true;
						});
						console.log('编辑模式数据设置完成:', {
							recordType: this.recordType,
							amount: this.amount,
							selectedCategoryId: this.selectedCategoryId,
							selectedAccountId: this.selectedAccountId
						});
					} else {
						uni.showToast({ title: response.msg || '加载记录失败', icon: 'none' });
					}
				} catch (error) {
					console.error('加载编辑记录失败:', error);
					uni.showToast({ title: '加载记录失败', icon: 'none' });
				} finally {
					uni.hideLoading();
				}
			},
			
			// 根据账户ID获取账户名称
			getAccountName(accountId) {
				const account = this.accounts.find(acc => acc.id === accountId);
				return account ? account.name : '现金';
			},
			
			// 加载分类数据 - 先从session获取，不存在再调用API
			async loadCategories(type) {
				try {
					// 先从session中获取分类数据
					const sessionKey = type === 'expense' ? 'expenseCategories' : 'incomeCategories';
					const cachedCategories = uni.getStorageSync(sessionKey);
					
					if (cachedCategories && Array.isArray(cachedCategories) && cachedCategories.length > 0) {
						console.log(`使用缓存的${type === 'expense' ? '支出' : '收入'}分类数据，数量:`, cachedCategories.length);
						if (type === 'expense') {
							this.expenseCategories = cachedCategories;
						} else {
							this.incomeCategories = cachedCategories;
						}
						return; // 有缓存数据，直接返回
					}
					
					// session中没有数据，调用API获取
					console.log(`session中没有${type === 'expense' ? '支出' : '收入'}分类数据，调用API获取`);
					const ledgerId = this.getcurrentBookId();
					const response = await categoryApi.getCategories(type, ledgerId);
					const categories = response?.data || [];
					
					// 过滤掉 visible=0 的分类
					const filteredCategories = categories.filter(item => item.visible !== 0);
					
					if (type === 'expense') {
						this.expenseCategories = filteredCategories;
					} else {
						this.incomeCategories = filteredCategories;
					}
					
					// 将获取的数据存入session
					uni.setStorageSync(sessionKey, filteredCategories);
					console.log(`API获取${type === 'expense' ? '支出' : '收入'}分类成功，数量:`, filteredCategories.length);
					
				} catch (error) {
					console.error(`加载${type === 'expense' ? '支出' : '收入'}分类失败:`, error);
					// 如果API调用失败，尝试使用默认分类
					this.useDefaultCategories(type);
				}
			},
			
			// 加载资产账户数据
			async loadAssetsData() {
				try {
					// 先检查Session中是否已有资产数据
					const cachedAssets = uni.getStorageSync('assetsAccountListData');
					if (cachedAssets) {
						console.log('使用缓存的资产数据');
						console.log(cachedAssets);
						this.accounts = cachedAssets;
						// 设置默认选中的账户
						this.setDefaultSelectedAccount(this.accounts);
						return;
					}
					
					// Session中没有数据，调用API获取
					console.log('调用API获取资产数据');
					const response = await assetsApi.getAssetsData();
					if (response.code === 1) {
						const assetsData = response.data.account_list || [];
						// 转换数据格式，适配页面显示
						const formattedAccounts = assetsData.map(asset => ({
							id: asset.id || asset.account_id,
							name: asset.name || asset.account_name,
							type: asset.type || 'cash',
							balance: asset.balance || 0
						}));
						
						// 更新账户列表
						this.accounts = formattedAccounts;
						
						// 设置默认选中的账户
						this.setDefaultSelectedAccount(this.accounts);
						
						// 保存到Session中，下次直接使用
						uni.setStorageSync('assetsAccountListData', formattedAccounts);
						
						console.log('资产数据加载成功:', formattedAccounts);
					} else {
						console.error('获取资产数据失败:', response.msg);
						// 如果API失败，使用默认账户
						this.useDefaultAccounts();
						// 设置默认选中的账户
						this.setDefaultSelectedAccount(this.accounts);
					}
				} catch (error) {
					console.error('加载资产数据失败:', error);
					// 如果发生错误，使用默认账户
					this.useDefaultAccounts();
					// 设置默认选中的账户
					this.setDefaultSelectedAccount(this.accounts);
				}
			},
			
			// 使用默认账户数据
			useDefaultAccounts() {
				this.accounts = [
					{ id: 1, name: '现金', type: 'cash' },
					{ id: 2, name: '银行卡', type: 'bank' },
					{ id: 3, name: '支付宝', type: 'alipay' },
					{ id: 4, name: '微信支付', type: 'wechat' }
				];
			},

			// 使用默认分类数据
			useDefaultCategories(type) {
				const defaultCategories = {
					expense: [
						{ id: 1, name: '餐饮', category_icon: 'icon-canyin' },
						{ id: 2, name: '交通', category_icon: 'icon-jiaotong' },
						{ id: 3, name: '购物', category_icon: 'icon-gouwu' },
						{ id: 4, name: '娱乐', category_icon: 'icon-yule' },
						{ id: 5, name: '医疗', category_icon: 'icon-yiliao' },
						{ id: 6, name: '教育', category_icon: 'icon-jiaoyu' }
					],
					income: [
						{ id: 101, name: '工资', category_icon: 'icon-gongzi' },
						{ id: 102, name: '奖金', category_icon: 'icon-jiangjin' },
						{ id: 103, name: '投资', category_icon: 'icon-touzi' },
						{ id: 104, name: '兼职', category_icon: 'icon-jianzhi' },
						{ id: 105, name: '红包', category_icon: 'icon-hongbao' },
						{ id: 106, name: '其他', category_icon: 'icon-qita' }
					]
				};
				
				if (type === 'expense') {
					this.expenseCategories = defaultCategories.expense;
				} else {
					this.incomeCategories = defaultCategories.income;
				}
				
				console.log(`使用默认${type === 'expense' ? '支出' : '收入'}分类数据`);
			},
			
			// 根据账户类型ID获取类型名称
			getAccountType(accountTypeId) {
				const typeMap = {
					1: 'cash',      // 现金
					2: 'alipay',    // 支付宝
					3: 'wechat',    // 微信支付
					4: 'bank',      // 银行卡
					5: 'credit',    // 信用卡
					6: 'investment', // 投资账户
					7: 'credit',    // 信用卡（示例中的招商信用卡）
					8: 'other'      // 其他
				};
				return typeMap[accountTypeId] || 'other';
			},
			
			// 设置默认选中的账户
			setDefaultSelectedAccount(accounts) {
				// 先检查是否有用户上次选择的账户
				const lastSelectedAccountId = uni.getStorageSync('lastSelectedAccountId');
				
				if (lastSelectedAccountId && accounts.length > 0) {
					// 查找上次选择的账户是否在列表中
					const lastAccount = accounts.find(account => account.id == lastSelectedAccountId);
					if (lastAccount) {
						// 使用上次选择的账户
						this.selectedAccountId = lastAccount.id;
						this.selectedAccountName = lastAccount.name;
						console.log('使用上次选择的账户:', lastAccount.name);
						return;
					}
				}
				
				// 如果没有上次选择的账户或账户不存在，使用第一个账户
				if (accounts.length > 0) {
					const firstAccount = accounts[0];
					this.selectedAccountId = firstAccount.id;
					this.selectedAccountName = firstAccount.name;
					console.log('使用第一个账户作为默认:', firstAccount.name);
				}
			},
			// 切换记录类型（支出/收入）
			switchRecordType(type) {
				this.recordType = type;
				this.selectedCategoryId = null; // 切换类型时重置选中的分类
				this.selectedCategoryName = ''; // 重置分类名称
				this.keyboardVisible = false; // 隐藏键盘
			},

			// 选择分类
			selectCategory(categoryId) {
				const category = this.currentCategories.find(item => item.id === categoryId);
				this.selectedCategoryId = categoryId;
				this.selectedCategoryName = category?.name || '';
				// 直接显示键盘
				this.keyboardVisible = true;
			},

			// 聚焦备注输入框
			focusRemarkInput() {
				this.remarkFocus = true;
			},

			// 输入数字
			inputNumber(num) {
				// 处理小数点
				if (num === '.') {
					if (this.amount.includes('.')) return; // 已有小数点，不再添加
					if (this.amount === '0') {
						this.amount = '0.';
						return;
					}
				}

				// 处理数字输入
				if (this.amount === '0' && num !== '.') {
					this.amount = num;
				} else {
					this.amount += num;
				}

				// 限制小数点后最多两位
				if (this.amount.includes('.')) {
					const parts = this.amount.split('.');
					if (parts[1].length > 2) {
						this.amount = `${parts[0]}.${parts[1].substring(0, 2)}`;
					}
				}
				
				// 编辑模式下，如果小数点后面全是0，转换为整数格式
				if (this.isEditMode && this.amount.includes('.')) {
					const parts = this.amount.split('.');
					if (parts[1] === '00' || parts[1] === '0' || /^0+$/.test(parts[1])) {
						this.amount = parts[0]; // 只保留整数部分
					}
				}
			},

			// 计算功能 - 使用更安全的计算方式
			calculate(operator) {
				if (operator === '=') {
					try {
						// 更安全的数学计算方式，仅支持加减法
						const expression = this.amount;
						const result = this.evaluateSafeExpression(expression);
						if (result !== null) {
							let resultStr = parseFloat(result.toFixed(2)).toString();
							this.amount = resultStr;
						} else {
							throw new Error('不支持的表达式');
						}
					} catch (e) {
						uni.showToast({
							title: '计算错误',
							icon: 'none'
						});
					}
				} else {
					// 添加运算符
					const lastChar = this.amount.slice(-1);
					if (!'+-'.includes(lastChar)) {
						this.amount += operator;
					} else if (operator !== lastChar) {
						// 替换运算符
						this.amount = this.amount.slice(0, -1) + operator;
					}
				}
			},
			
			// 安全评估简单的数学表达式（仅支持加减法）
			evaluateSafeExpression(expression) {
				// 仅允许数字、小数点和加减运算符
				if (!/^[\d+\-\.]+$/.test(expression)) {
					return null;
				}
				
				// 解析表达式并计算结果
				const parts = [];
				let current = '';
				let lastOperator = '+';
				
				for (let i = 0; i < expression.length; i++) {
					const char = expression[i];
					if (char === '+' || char === '-') {
						if (current !== '') {
							const num = parseFloat(current);
							if (lastOperator === '+') {
								parts.push(num);
							} else {
								parts.push(-num);
							}
							current = '';
						}
						lastOperator = char;
					} else {
						current += char;
					}
				}
				
				// 处理最后一个数字
				if (current !== '') {
					const num = parseFloat(current);
					if (lastOperator === '+') {
						parts.push(num);
					} else {
						parts.push(-num);
					}
				}
				
				// 求和
				return parts.reduce((sum, num) => sum + num, 0);
			},

			// 删除数字
			deleteNumber() {
				if (this.amount.length > 1) {
					this.amount = this.amount.slice(0, -1);
				} else {
					this.amount = '0';
				}
			},

			// 取消记录
			cancelRecord() {
				// 编辑模式下，添加确认提示防止误操作
				if (this.isEditMode && this.amount !== '0' && this.selectedCategoryId) {
					uni.showModal({
						title: '确认返回',
						content: '返回将丢失当前修改，确定要返回吗？',
						success: (res) => {
							if (res.confirm) {
								this.performNavigationBack();
							}
						}
					});
				} else {
					this.performNavigationBack();
				}
			},
			
			// 执行返回导航（兼容所有平台）
			performNavigationBack() {
				// 检查当前平台
				const platform = uni.getSystemInfoSync().platform;
				
				// H5环境下使用history.back()，其他平台使用uni.navigateBack()
				if (platform === 'h5') {
					// H5环境：使用浏览器历史记录返回
					if (window.history && window.history.length > 1) {
						window.history.back();
					} else {
						// 如果没有历史记录，跳转到首页
						uni.navigateTo({
							url: '/pages/dashboard/index'
						});
					}
				} else {
					// 其他平台：使用uni.navigateBack()
					uni.navigateBack({
						delta: 1,
						fail: (err) => {
							console.log('navigateBack失败:', err);
							// 如果失败，直接跳转到首页
							uni.navigateTo({
								url: '/pages/dashboard/index'
							});
						}
					});
				}
			},

			// 获取当前账本ID
			getcurrentBookId() {
				// 从SessionStorage获取当前账本ID
				const currentBookId = uni.getStorageSync('currentBookId');

				console.log('currentBook:', currentBookId);

				return currentBookId;
			},

			// 完成记录
			async completeRecord() {
				// 验证输入
				if (this.amount === '0') {
					uni.showToast({
						title: '请输入金额',
						icon: 'none'
					});
					return;
				}

				if (!this.selectedCategoryId) {
					uni.showToast({
						title: '请选择分类',
						icon: 'none'
					});
					return;
				}

				// 构建记录数据
				const recordData = {
					transaction_date: this.formatDate(this.selectedDate),
					category_id: this.selectedCategoryId,
					amount: parseFloat(this.amount),
					type: this.recordType === 'income' ? 1 : -1,
					remark: this.remark || '',
					book_id: this.getcurrentBookId(), // 获取当前账本ID
					account_id: this.selectedAccountId // 获取选中的账户ID
				};

				// 如果是编辑模式，添加记录ID
				if (this.isEditMode) {
					recordData.id = this.recordId;
				}

				try {
					// 显示加载提示
					uni.showLoading({
						title: this.isEditMode ? '更新中...' : '保存中...'
					});

					let result;
					if (this.isEditMode) {
						// 调用API更新记录
						result = await recordApi.updateRecord(recordData);
					} else {
						// 调用API保存记录
						result = await recordApi.addRecord(recordData);
					}

					// 隐藏加载提示
					uni.hideLoading();

					// 显示成功提示
					uni.showToast({
						title: result.msg || (this.isEditMode ? '记录已更新' : '记录已保存'),
						icon: 'success'
					});

					// 延迟返回上一页
					setTimeout(() => {
						// 返回上一页并刷新列表
						uni.navigateBack({
							delta: 1,
							success: () => {
								// 通过事件总线通知列表页刷新数据
								if (this.isEditMode) {
									console.log('通过事件总线通知列表页刷新数据');
									uni.$emit('recordUpdated', recordData);
								} else {
									uni.$emit('recordAdded', result);
								}
							}
						});
					}, 1500);
				} catch (error) {
					// 隐藏加载提示
					uni.hideLoading();

					// 显示错误提示
					uni.showToast({
						title: this.isEditMode ? '更新失败，请重试' : '保存失败，请重试',
						icon: 'none'
					});

					console.error(this.isEditMode ? '更新记录失败:' : '保存记录失败:', error);
				}
			},

			// 格式化日期为 YYYY-MM-DD
			formatDate(date) {
				const year = date.getFullYear();
				const month = String(date.getMonth() + 1).padStart(2, '0');
				const day = String(date.getDate()).padStart(2, '0');
				return `${year}-${month}-${day}`;
			},

			// 日期选择器变化事件
			onDatePickerChange(e) {
				this.tempDatePickerValue = e.detail.value;
				const yearIndex = e.detail.value[0];
				const monthIndex = e.detail.value[1];
				const dayIndex = e.detail.value[2];

				// 更新临时日期值
				const year = this.years[yearIndex];
				const month = this.months[monthIndex] - 1; // 月份从0开始
				const day = this.days[dayIndex];

				// 检查日期是否有效（例如2月30日是无效的）
				const tempDate = new Date(year, month, day);
				if (tempDate.getFullYear() === year &&
					tempDate.getMonth() === month &&
					tempDate.getDate() === day) {
					this.tempDate = tempDate;
				}
			},

			// 确认日期选择
			confirmDateSelection() {
				// 如果有临时日期，则更新选中日期
				if (this.tempDate) {
					this.selectedDate = this.tempDate;
				}

				// 更新日期选择器的值
				this.datePickerValue = [...this.tempDatePickerValue];

				// 关闭日期选择器
				this.showDatePicker = false;
			},

			// 切换备注输入框显示
			toggleRemarkInput() {
				this.showRemarkInput = !this.showRemarkInput;
			},

			// 隐藏备注输入框
			hideRemarkInput() {
				this.showRemarkInput = false;
			},

			// 切换账户选择器显示
			toggleAccountPicker() {
				this.showAccountPicker = !this.showAccountPicker;
			},

			// 选择账户
			selectAccount(account) {
				this.selectedAccountId = account.id;
				this.selectedAccountName = account.name;
				
				// 保存用户选择的账户到Session中
				uni.setStorageSync('lastSelectedAccountId', account.id);
				console.log('保存用户选择的账户:', account.name);
				
				this.showAccountPicker = false;
			},

			// 跳转到分类页面
			navigateToCategoryPage() {
				uni.navigateTo({
					url: '/pages/category/index'
				});
			},
		}
	};
</script>
<style>
/* 引入iconfont图标库 */
@import '@/static/iconfont/iconfont.css';
</style>
<style>
	.add-record-container {
		display: flex;
		flex-direction: column;
		min-height: 100vh;
		background-color: #ffffff;
		padding-top: 240rpx;
		/* 为固定顶部区域留出空间 */
	}
	
.uni-navbar--border[data-v-4e85c420] {
    border-bottom-width: 0px !important;
    border-bottom-style: none !important;
    border-bottom-color: transparent !important;
}

/* 去掉导航栏和切换区域之间的白色线条 */
.add-record-container .fixed-top .uni-nav-bar {
    border-bottom: none !important;
}

.add-record-container .fixed-top .uni-navbar__header {
    border-bottom: none !important;
}

.add-record-container .fixed-top .uni-navbar__content {
    border-bottom: none !important;
}
	.add-record-container .fixed-top {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		background: linear-gradient(135deg, #07C160, #07C160);
		z-index: 1000;
		width: 100%;
		border-radius: 0 0 40rpx 40rpx;
		box-shadow: 0 4rpx 20rpx rgba(7, 193, 96, 0.3);
		overflow: hidden;
	}

	.add-record-container .fixed-top .tab-switch-container {
		padding: 20rpx 30rpx;
		display: flex;
		justify-content: center;
		background: linear-gradient(135deg, #07C160, #07C160);
	}

	.add-record-container .fixed-top .tab-switch-container .tab-container {
		display: flex;
		background-color: #f5f5f5;
		border-radius: 40rpx;
		padding: 4rpx;
		width: 300rpx;
	}

	.add-record-container .fixed-top .tab-switch-container .tab-container .tab-item {
		flex: 1;
		text-align: center;
		padding: 10rpx 24rpx;
		border-radius: 36rpx;
		font-size: 28rpx;
		color: #666;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	}

	.add-record-container .fixed-top .tab-switch-container .tab-container .tab-item.active-tab {
		background-color: #07C160;
		color: white;
		font-weight: 500;
		box-shadow: 0 2rpx 8rpx rgba(7, 193, 96, 0.3);
	}

	.add-record-container .nav-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 20rpx 30rpx;
		border-bottom: 1rpx solid #f0f0f0;
	}

	.add-record-container .nav-header .tab-container {
		display: flex;
		background-color: #f5f5f5;
		border-radius: 40rpx;
		padding: 4rpx;
		width: 300rpx;
	}

	.add-record-container .nav-header .tab-container .tab-item {
		flex: 1;
		text-align: center;
		padding: 16rpx 24rpx;
		border-radius: 36rpx;
		font-size: 28rpx;
		color: #666;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	}

	.add-record-container .nav-header .tab-container .tab-item.active-tab {
		background-color: #07C160;
		color: white;
		font-weight: 500;
		box-shadow: 0 2rpx 8rpx rgba(7, 193, 96, 0.3);
	}

	.add-record-container .nav-header .cancel-btn {
		font-size: 30rpx;
		color: #333;
	}

	.add-record-container .amount-display {
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 20rpx 0;
		background: rgba(255, 255, 255, 0.95);
		margin: 0 30rpx;
		border-radius: 20rpx;
		box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.1);
		backdrop-filter: blur(10px);
	}

	.add-record-container .amount-display .currency-symbol {
		font-size: 48rpx;
		color: #333;
		margin-right: 10rpx;
	}

	.add-record-container .amount-display .amount-value {
		font-size: 56rpx;
		font-weight: bold;
		color: #07C160;
		padding: 10rpx 0;
		text-shadow: 0 2rpx 6rpx rgba(7, 193, 96, 0.3);
		letter-spacing: 1rpx;
	}

	.add-record-container .date-picker {
		margin: 0 10rpx 0rpx;
		padding: 20rpx 20rpx;
		background-color: #f5f5f5;
		border-radius: 16rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.add-record-container .date-picker .date-label {
		font-size: 28rpx;
		color: #666;
	}

	.add-record-container .date-picker .date-value {
		display: flex;
		align-items: center;
		width: 120rpx;
		text-align: center;
	}

	.add-record-container .date-picker .date-value text {
		font-size: 24rpx;
		color: #333;
		margin-right: 10rpx;
	}

	.add-record-container .date-picker-popup {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 999;
	}

	.add-record-container .date-picker-popup .date-picker-mask {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: rgba(0, 0, 0, 0.5);
	}

	.add-record-container .date-picker-popup .date-picker-content {
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		background-color: #ffffff;
		border-radius: 24rpx 24rpx 0 0;
		overflow: hidden;
		animation: slideUp 0.3s ease-out;
	}

	.add-record-container .date-picker-popup .date-picker-content .date-picker-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 20rpx 30rpx;
		border-bottom: 1rpx solid #f0f0f0;
	}

	.add-record-container .date-picker-popup .date-picker-content .date-picker-header .date-picker-cancel {
		font-size: 28rpx;
		color: #999;
	}

	.add-record-container .date-picker-popup .date-picker-content .date-picker-header .date-picker-title {
		font-size: 30rpx;
		color: #333;
		font-weight: 500;
	}

	.add-record-container .date-picker-popup .date-picker-content .date-picker-header .date-picker-confirm {
		font-size: 28rpx;
		color: #07C160;
		font-weight: 500;
	}

	.add-record-container .date-picker-popup .date-picker-content .date-picker-view {
		height: 400rpx;
	}

	.add-record-container .date-picker-popup .date-picker-content .date-picker-view .picker-item {
		line-height: 80rpx;
		text-align: center;
		font-size: 32rpx;
		color: #333;
	}

	@keyframes slideUp {
		from {
			transform: translateY(100%);
		}
		to {
			transform: translateY(0);
		}
	}

	.add-record-container .remark-input {
		margin: 0 30rpx;
		padding: 20rpx 30rpx;
		background-color: #f5f5f5;
		border-radius: 16rpx;
	}

	.add-record-container .remark-input input {
		width: 100%;
		height: 80rpx;
		font-size: 28rpx;
		color: #333;
	}

	.add-record-container .remark-input input::placeholder {
		color: #999;
	}

	.add-record-container .category-section {
		margin: 180rpx 30rpx 100rpx 30rpx;
		flex: 1;
		display: flex;
		flex-direction: column;
	}

	.add-record-container .category-section .section-title {
		font-size: 30rpx;
		color: #666;
		margin-bottom: 30rpx;
		display: block;
	}

	.add-record-container .category-section .category-scroll-view {
		height: 400rpx; /* 设置固定高度，超出时可滚动 */
		flex: 1;
	}

	.add-record-container .category-section .category-grid {
		display: flex;
		flex-wrap: wrap;
		padding-bottom: 20rpx;
	}

	.add-record-container .category-section .category-grid .category-item {
		width: 25%;
		display: flex;
		flex-direction: column;
		align-items: center;
		margin-bottom: 40rpx;
	}

	.add-record-container .category-section .category-grid .category-item .category-icon {
		width: 100rpx;
		height: 100rpx;
		border-radius: 50%;
		background-color: #f5f5f5;
		display: flex;
		align-items: center;
		justify-content: center;
		margin-bottom: 16rpx;
		transition: all 0.3s;
	}

	.add-record-container .category-section .category-grid .category-item .category-name {
		font-size: 24rpx;
		color: #666;
	}

	.add-record-container .category-section .category-grid .category-item.selected .category-icon {
		background-color: #FFCC00;
		box-shadow: 0 4rpx 12rpx rgba(255, 204, 0, 0.3);
	}

	.add-record-container .category-section .category-grid .category-item.selected .category-name {
		color: #333;
		font-weight: 500;
	}

	/* 添加分类按钮样式 */
	.add-record-container .category-section .category-grid .add-category-btn {
		opacity: 0.8;
		transition: all 0.3s ease;
	}

	.add-record-container .category-section .category-grid .add-category-btn:active {
		opacity: 1;
		transform: scale(0.95);
	}

	.add-record-container .category-section .category-grid .add-category-btn .category-icon {
		background-color: #f0f0f0;
		border: 2rpx dashed #07C160;
	}

	.add-record-container .category-section .category-grid .add-category-btn .category-name {
		color: #07C160;
		font-weight: 500;
	}

	.add-record-container .remark-input-container {
		position: fixed;
		left: 0;
		right: 0;
		padding: 30rpx;
		z-index: 999;
		height: 160rpx;
	}

	.add-record-container .remark-input-container input {
		height: 120rpx;
		padding: 20rpx;
		border: 1rpx solid #e0e0e0;
		border-radius: 16rpx;
		font-size: 32rpx;
		background-color: #f8f8f8;
	}

	.add-record-container .remark-wrapper {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		background-color: #fff;
		z-index: 1000;
		padding: 20rpx;
		height: 660rpx;
		border-radius: 20rpx 20rpx 0 0;
		box-shadow: 0 -4rpx 12rpx rgba(0, 0, 0, 0.1);
	}

	.add-record-container .remark-header {
		position: relative;
		height: 60rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 10rpx 0;
	}

	.add-record-container .remark-header .back-arrow {
		position: absolute;
		left: 20rpx;
		padding: 10rpx;
	}

	.add-record-container .remark-header .remark-title {
		font-size: 28rpx;
		color: #333;
		font-weight: 500;
	}

	.add-record-container .confirm-btn {
		position: fixed;
		bottom: 80rpx;
		left: 50%;
		transform: translateX(-50%);
		width: 80%;
		height: 80rpx;
		background-color: #07C160;
		border-radius: 40rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		z-index: 999;
	}

	.add-record-container .confirm-btn text {
		color: #fff;
		font-size: 32rpx;
		font-weight: 500;
	}

	.add-record-container .date-account-picker {
		display: flex;
		align-items: center;
		padding: 20rpx 30rpx;
		gap: 10rpx;
	}

	.add-record-container .date-account-picker .date-value {

		display: flex;
		align-items: center;
		background-color: #fff;
		padding: 10rpx 20rpx;
		border-radius: 40rpx;
		box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.08);
		border: 1rpx solid #e0e0e0;
		transition: all 0.2s ease;
	}

	.add-record-container .date-account-picker .account-value {

		display: flex;
		align-items: center;
		background-color: #fff;
		padding: 10rpx 20rpx;
		border-radius: 40rpx;
		box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.08);
		border: 1rpx solid #e0e0e0;
		transition: all 0.2s ease;
		justify-content: center;
	}

	.add-record-container .date-account-picker .date-value:active,
	.add-record-container .date-account-picker .account-value:active {
		background-color: #f8f8f8;
	}

	.add-record-container .date-account-picker .date-value text,
	.add-record-container .date-account-picker .account-value text {
		font-size: 26rpx;
		color: #333;
		font-weight: 500;
	}

	/* 账户选择器弹窗样式 */
	.add-record-container .account-picker-popup {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 999;
	}

	.add-record-container .account-picker-popup .account-picker-mask {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: rgba(0, 0, 0, 0.5);
	}

	.add-record-container .account-picker-popup .account-picker-content {
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		background-color: #ffffff;
		border-radius: 24rpx 24rpx 0 0;
		overflow: hidden;
		animation: slideUp 0.3s ease-out;
		max-height: 60vh;
	}

	.add-record-container .account-picker-popup .account-picker-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 20rpx 30rpx;
		border-bottom: 1rpx solid #f0f0f0;
	}

	.add-record-container .account-picker-popup .account-picker-header .account-picker-title {
		font-size: 30rpx;
		color: #333;
		font-weight: 500;
	}

	.add-record-container .account-picker-popup .account-picker-header .account-picker-close {
		font-size: 40rpx;
		color: #999;
		line-height: 1;
	}

	.add-record-container .account-picker-popup .account-list {
		padding: 20rpx 0;
		max-height: 50vh;
		overflow-y: auto;
	}

	.add-record-container .account-picker-popup .account-item {
		padding: 24rpx 30rpx;
		border-bottom: 1rpx solid #f0f0f0;
		transition: background-color 0.2s ease;
	}

	.add-record-container .account-picker-popup .account-item:last-child {
		border-bottom: none;
	}

	.add-record-container .account-picker-popup .account-item.selected {
		background-color: #f8f8f8;
	}

	.add-record-container .account-picker-popup .account-item .account-name {
		font-size: 28rpx;
		color: #333;
	}

	.add-record-container .account-picker-popup .account-item.selected .account-name {
		color: #07C160;
		font-weight: 500;
	}

	.add-record-container .date-value {
		width: 150rpx;
		/* 固定宽度 */
		display: flex;
		align-items: center;
		background-color: #fff;
		padding: 10rpx 20rpx;
		border-radius: 40rpx;
		box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.08);
		border: 1rpx solid #e0e0e0;
	}

	.add-record-container .remark-wrapper-bg {
		background-color: #fff;
	}

	.add-record-container .remark-btn {
		margin-left: 20rpx;
		padding: 10rpx 20rpx;
		background-color: #f0f0f0;
		border-radius: 20rpx;
		font-size: 24rpx;
		color: #666;
		white-space: nowrap;
	}

	.add-record-container .keyboard-container {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		background: #f0f2f5;
		/* 更深的背景色 */
		padding: 0;
		transform: translateY(100%);
		transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
		z-index: 100;
		border-radius: 30rpx 30rpx 0 0;
		box-shadow: 0 -8rpx 30rpx rgba(0, 0, 0, 0.15);
		/* 更强的阴影 */

		padding-bottom: 20px;;
	}

	.add-record-container .keyboard-container.keyboard-show {
		transform: translateY(0);
	}

	.add-record-container .keyboard-container .date-picker {
		padding: 20rpx 30rpx;
		/* 减少内边距 */
		display: flex;
		justify-content: space-between;
		align-items: center;
		background-color: transparent;
	}

	.add-record-container .keyboard-container .date-picker .date-label {
		font-size: 28rpx;
		color: #666;
		font-weight: 500;
	}

	.add-record-container .keyboard-container .date-picker .date-value {
		display: flex;
		align-items: center;
		background-color: #fff;
		padding: 10rpx 20rpx;
		border-radius: 40rpx;
		box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.08);
		border: 1rpx solid #e0e0e0;
		transition: all 0.2s ease;
	}

	.add-record-container .keyboard-container .date-picker .date-value:active {
		background-color: #f8f8f8;
	}

	.add-record-container .keyboard-container .date-picker .date-value text {
		font-size: 26rpx;
		color: #333;
		margin-right: 8rpx;
		font-weight: 500;
	}

	.add-record-container .keyboard-container .keyboard-row {
		display: flex;
		padding: 8rpx 20rpx;
		margin-bottom: 20rpx;
	}

	.add-record-container .keyboard-container .keyboard-row:last-child {
		margin-bottom: 0;
	}

	.add-record-container .keyboard-container .keyboard-row .keyboard-key {
		flex: 1;
		height: 100rpx;
		margin: 0 10rpx;
		border-radius: 12rpx;
		/* 更大的圆角 */
		font-size: 36rpx;
		background-color: #ffffff;
		box-shadow: 0 4rpx 8rpx rgba(0, 0, 0, 0.08);
		transition: all 0.2s ease;
		border: 1rpx solid #e0e0e0;
		/* 添加边框 */
		text-align: center;
		line-height: 90rpx;
	}

	.add-record-container .keyboard-container .keyboard-row .keyboard-key:active {
		transform: scale(0.96);
		box-shadow: 0 2rpx 4rpx rgba(0, 0, 0, 0.1);
	}

	.add-record-container .keyboard-container .keyboard-row .keyboard-key.operator-key {
		background-color: #f8f8f8;
		color: #07C160;
		/* 绿色运算符 */
		font-weight: bold;
		border-color: #d0d0d0;
	}

	.add-record-container .keyboard-container .keyboard-row .keyboard-key.operator-key:active {
		background-color: #f0f0f0;
	}

	.add-record-container .keyboard-container .keyboard-row .keyboard-key.delete-key {
		background-color: #f8f8f8;
		color: #FF2D55;
		/* 红色删除 */
	}

	.add-record-container .keyboard-container .keyboard-row .keyboard-key.complete-key {
		background: linear-gradient(135deg, #FFCC00, #FFAA00);
		/* 渐变黄色 */
		color: #ffffff;
		font-weight: bold;
		border: none;
		box-shadow: 0 4rpx 12rpx rgba(255, 204, 0, 0.3);
	}

	.add-record-container .keyboard-container .keyboard-row .keyboard-key.complete-key:active {
		background: linear-gradient(135deg, #FFAA00, #FF8800);
	}
</style>