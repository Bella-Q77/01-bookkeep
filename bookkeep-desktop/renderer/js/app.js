class BookkeepApp {
    constructor() {
        this.data = null;
        this.currentPage = 'dashboard';
        this.currentLedgerId = 1;
        this.selectedType = 'expense';
        this.selectedCategoryId = null;
        this.editingRecordId = null;
        
        this.init();
    }

    async init() {
        await this.loadData();
        this.bindEvents();
        this.renderMonthSelectors();
        this.setCurrentDate();
        this.render();
    }

    async loadData() {
        try {
            this.data = await window.electronAPI.loadData();
            const defaultLedger = this.data.ledgers.find(l => l.isDefault);
            if (defaultLedger) {
                this.currentLedgerId = defaultLedger.id;
            }
        } catch (error) {
            console.error('加载数据失败:', error);
            this.showToast('加载数据失败', 'error');
        }
    }

    async saveData() {
        try {
            await window.electronAPI.saveData(this.data);
        } catch (error) {
            console.error('保存数据失败:', error);
            this.showToast('保存数据失败', 'error');
        }
    }

    bindEvents() {
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const page = e.currentTarget.dataset.page;
                this.navigateTo(page);
            });
        });

        document.querySelectorAll('.card-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = e.currentTarget.dataset.page;
                if (page) {
                    this.navigateTo(page);
                }
            });
        });

        document.getElementById('addRecordBtn').addEventListener('click', () => {
            this.navigateTo('add');
        });

        document.getElementById('dashboardMonth').addEventListener('change', () => {
            this.renderDashboard();
        });

        document.getElementById('filterType').addEventListener('change', () => {
            this.renderRecords();
        });

        document.getElementById('filterCategory').addEventListener('change', () => {
            this.renderRecords();
        });

        document.getElementById('filterMonth').addEventListener('change', () => {
            this.renderRecords();
        });

        document.getElementById('resetFilter').addEventListener('click', () => {
            document.getElementById('filterType').value = 'all';
            document.getElementById('filterCategory').value = 'all';
            this.setCurrentDate();
            this.renderRecords();
        });

        document.getElementById('statsMonth').addEventListener('change', () => {
            this.renderStatistics();
        });

        document.getElementById('statsType').addEventListener('change', () => {
            this.renderStatistics();
        });

        document.getElementById('budgetMonth').addEventListener('change', () => {
            this.renderBudget();
        });

        document.getElementById('addBudgetBtn').addEventListener('click', () => {
            this.showBudgetModal();
        });

        document.getElementById('settingsLedger').addEventListener('change', (e) => {
            this.currentLedgerId = parseInt(e.target.value);
            this.updateLedgerDisplay();
            this.render();
        });

        document.getElementById('createLedgerBtn').addEventListener('click', () => {
            this.showCreateLedgerModal();
        });

        document.getElementById('exportDataBtn').addEventListener('click', async () => {
            try {
                const result = await window.electronAPI.exportData();
                if (result.success) {
                    this.showToast(`数据已导出到: ${result.path}`, 'success');
                } else if (!result.canceled) {
                    this.showToast('导出失败: ' + result.error, 'error');
                }
            } catch (error) {
                this.showToast('导出失败', 'error');
            }
        });

        document.getElementById('importDataBtn').addEventListener('click', async () => {
            try {
                const result = await window.electronAPI.importData();
                if (result.success) {
                    this.data = result.data;
                    this.showToast('数据导入成功', 'success');
                    this.render();
                } else if (!result.canceled) {
                    this.showToast('导入失败: ' + result.error, 'error');
                }
            } catch (error) {
                this.showToast('导入失败', 'error');
            }
        });

        document.querySelectorAll('.type-option').forEach(option => {
            option.addEventListener('click', (e) => {
                const type = e.currentTarget.dataset.type;
                this.setRecordType(type);
            });
        });

        document.getElementById('recordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.saveRecord();
        });

        document.getElementById('cancelRecord').addEventListener('click', () => {
            this.resetRecordForm();
            this.navigateTo('dashboard');
        });

        document.getElementById('modalOverlay').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                this.closeModal();
            }
        });
    }

    navigateTo(page) {
        this.currentPage = page;
        
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
            if (item.dataset.page === page) {
                item.classList.add('active');
            }
        });

        document.querySelectorAll('.page').forEach(p => {
            p.classList.remove('active');
        });
        const targetPage = document.getElementById(`page-${page}`);
        if (targetPage) {
            targetPage.classList.add('active');
        }

        this.render();
    }

    render() {
        this.updateLedgerSelect();
        this.updateLedgerDisplay();

        switch (this.currentPage) {
            case 'dashboard':
                this.renderDashboard();
                break;
            case 'records':
                this.renderRecords();
                break;
            case 'add':
                this.renderAddRecord();
                break;
            case 'statistics':
                this.renderStatistics();
                break;
            case 'budget':
                this.renderBudget();
                break;
        }
    }

    renderMonthSelectors() {
        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth() + 1;

        const selectors = [
            'dashboardMonth',
            'filterMonth',
            'statsMonth',
            'budgetMonth'
        ];

        selectors.forEach(selectorId => {
            const select = document.getElementById(selectorId);
            if (!select) return;

            select.innerHTML = '';
            for (let year = currentYear; year >= currentYear - 5; year--) {
                for (let month = 12; month >= 1; month--) {
                    if (year === currentYear && month > currentMonth) continue;
                    
                    const option = document.createElement('option');
                    option.value = `${year}-${month.toString().padStart(2, '0')}`;
                    option.textContent = `${year}年${month}月`;
                    
                    if (year === currentYear && month === currentMonth) {
                        option.selected = true;
                    }
                    
                    select.appendChild(option);
                }
            }
        });
    }

    setCurrentDate() {
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}`;
        
        document.getElementById('recordDate').value = now.toISOString().split('T')[0];
        
        const selectors = ['filterMonth'];
        selectors.forEach(selectorId => {
            const select = document.getElementById(selectorId);
            if (select) {
                select.value = currentMonth;
            }
        });
    }

    getSelectedMonth() {
        const selectorMap = {
            'dashboard': 'dashboardMonth',
            'records': 'filterMonth',
            'statistics': 'statsMonth',
            'budget': 'budgetMonth'
        };
        
        const selectorId = selectorMap[this.currentPage] || 'dashboardMonth';
        const select = document.getElementById(selectorId);
        return select ? select.value : null;
    }

    getLedgerRecords(ledgerId) {
        return this.data.records.filter(r => r.ledgerId === ledgerId);
    }

    getRecordsByMonth(records, month) {
        return records.filter(r => r.date.startsWith(month));
    }

    calculateStats(records) {
        const income = records
            .filter(r => r.type === 'income')
            .reduce((sum, r) => sum + parseFloat(r.amount), 0);
        
        const expense = records
            .filter(r => r.type === 'expense')
            .reduce((sum, r) => sum + parseFloat(r.amount), 0);

        return {
            income: income.toFixed(2),
            expense: expense.toFixed(2),
            balance: (income - expense).toFixed(2)
        };
    }

    getCategoryById(categoryId) {
        const allCategories = [...this.data.categories.income, ...this.data.categories.expense];
        return allCategories.find(c => c.id === categoryId);
    }

    getCategoryStats(records, type) {
        const filteredRecords = records.filter(r => r.type === type);
        const categoryMap = {};

        filteredRecords.forEach(record => {
            const category = this.getCategoryById(record.categoryId);
            if (category) {
                if (!categoryMap[category.id]) {
                    categoryMap[category.id] = {
                        id: category.id,
                        name: category.name,
                        icon: category.icon,
                        amount: 0
                    };
                }
                categoryMap[category.id].amount += parseFloat(record.amount);
            }
        });

        const total = Object.values(categoryMap).reduce((sum, c) => sum + c.amount, 0);
        
        return Object.values(categoryMap)
            .map(c => ({
                ...c,
                amount: c.amount.toFixed(2),
                percent: total > 0 ? ((c.amount / total) * 100).toFixed(1) : '0'
            }))
            .sort((a, b) => parseFloat(b.amount) - parseFloat(a.amount));
    }

    renderDashboard() {
        const records = this.getLedgerRecords(this.currentLedgerId);
        const month = this.getSelectedMonth();
        const monthRecords = month ? this.getRecordsByMonth(records, month) : records;
        const stats = this.calculateStats(monthRecords);

        document.getElementById('monthlyIncome').textContent = `¥${stats.income}`;
        document.getElementById('monthlyExpense').textContent = `¥${stats.expense}`;
        document.getElementById('monthlyBalance').textContent = `¥${stats.balance}`;

        this.renderBudgetCard(month);
        this.renderRecentRecords(records);
        this.renderCategoryStats(monthRecords);
    }

    renderBudgetCard(month) {
        const budget = this.data.budgets.find(b => 
            b.ledgerId === this.currentLedgerId && b.month === month
        );

        const records = this.getLedgerRecords(this.currentLedgerId);
        const monthRecords = month ? this.getRecordsByMonth(records, month) : records;
        const expense = monthRecords
            .filter(r => r.type === 'expense')
            .reduce((sum, r) => sum + parseFloat(r.amount), 0);

        if (budget) {
            const budgetAmount = parseFloat(budget.amount);
            const used = Math.min(expense, budgetAmount);
            const remaining = Math.max(0, budgetAmount - expense);
            const progress = budgetAmount > 0 ? Math.min(100, (used / budgetAmount) * 100) : 0;

            document.getElementById('budgetAmount').textContent = `¥${budget.amount}`;
            document.getElementById('budgetUsed').textContent = `已使用 ¥${used.toFixed(2)}`;
            document.getElementById('budgetRemaining').textContent = `剩余 ¥${remaining.toFixed(2)}`;
            document.getElementById('budgetProgressFill').style.width = `${progress}%`;
        } else {
            document.getElementById('budgetAmount').textContent = '¥0.00';
            document.getElementById('budgetUsed').textContent = '已使用 ¥0.00';
            document.getElementById('budgetRemaining').textContent = '剩余 ¥0.00';
            document.getElementById('budgetProgressFill').style.width = '0%';
        }
    }

    renderRecentRecords(records) {
        const container = document.getElementById('recentRecordsList');
        const recentRecords = records
            .sort((a, b) => new Date(b.date) - new Date(a.date))
            .slice(0, 5);

        if (recentRecords.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <span class="empty-icon">📭</span>
                    <span class="empty-text">暂无记录</span>
                </div>
            `;
            return;
        }

        container.innerHTML = recentRecords.map(record => {
            const category = this.getCategoryById(record.categoryId);
            return `
                <div class="record-item">
                    <div class="record-icon-wrapper ${record.type}">
                        <span class="record-icon">${category?.icon || '📝'}</span>
                    </div>
                    <div class="record-info">
                        <div class="record-category">${category?.name || '未分类'}</div>
                        <div class="record-date">${record.date}</div>
                    </div>
                    <div class="record-right">
                        <span class="record-amount ${record.type}">
                            ${record.type === 'income' ? '+' : ''}¥${record.amount}
                        </span>
                        ${record.remark ? `<span class="record-remark">${record.remark}</span>` : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    renderCategoryStats(records) {
        const container = document.getElementById('categoryStats');
        const categoryStats = this.getCategoryStats(records, 'expense');

        if (categoryStats.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <span class="empty-icon">📊</span>
                    <span class="empty-text">暂无统计数据</span>
                </div>
            `;
            return;
        }

        const maxAmount = Math.max(...categoryStats.map(c => parseFloat(c.amount)));

        container.innerHTML = categoryStats.slice(0, 6).map(cat => `
            <div class="category-stat-item">
                <div class="category-stat-icon">${cat.icon}</div>
                <div class="category-stat-info">
                    <div class="category-stat-name">${cat.name}</div>
                    <div class="category-stat-bar">
                        <div class="category-stat-fill" style="width: ${maxAmount > 0 ? (parseFloat(cat.amount) / maxAmount * 100) : 0}%"></div>
                    </div>
                    <div>
                        <span class="category-stat-amount">¥${cat.amount}</span>
                        <span class="category-stat-percent">${cat.percent}%</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    renderRecords() {
        const records = this.getLedgerRecords(this.currentLedgerId);
        const month = this.getSelectedMonth();
        let filteredRecords = month ? this.getRecordsByMonth(records, month) : records;

        const typeFilter = document.getElementById('filterType').value;
        const categoryFilter = document.getElementById('filterCategory').value;

        if (typeFilter !== 'all') {
            filteredRecords = filteredRecords.filter(r => r.type === typeFilter);
        }

        if (categoryFilter !== 'all') {
            filteredRecords = filteredRecords.filter(r => r.categoryId === categoryFilter);
        }

        filteredRecords.sort((a, b) => new Date(b.date) - new Date(a.date));

        this.updateCategoryFilter(typeFilter);
        this.renderRecordsList(filteredRecords);
    }

    updateCategoryFilter(type) {
        const select = document.getElementById('filterCategory');
        const currentValue = select.value;

        let categories = [];
        if (type === 'income') {
            categories = this.data.categories.income;
        } else if (type === 'expense') {
            categories = this.data.categories.expense;
        } else {
            categories = [...this.data.categories.income, ...this.data.categories.expense];
        }

        select.innerHTML = '<option value="all">全部分类</option>';
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            if (cat.id === currentValue) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

    renderRecordsList(records) {
        const container = document.getElementById('recordsList');

        if (records.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <span class="empty-icon">📭</span>
                    <span class="empty-text">暂无账单记录</span>
                    <span class="empty-hint">点击右上角"新增记录"开始记账</span>
                </div>
            `;
            return;
        }

        container.innerHTML = records.map(record => {
            const category = this.getCategoryById(record.categoryId);
            return `
                <div class="record-item">
                    <div class="record-icon-wrapper ${record.type}">
                        <span class="record-icon">${category?.icon || '📝'}</span>
                    </div>
                    <div class="record-info">
                        <div class="record-category">${category?.name || '未分类'}</div>
                        <div class="record-date">${record.date} ${record.remark ? '- ' + record.remark : ''}</div>
                    </div>
                    <div class="record-right">
                        <span class="record-amount ${record.type}">
                            ${record.type === 'income' ? '+' : ''}¥${record.amount}
                        </span>
                        <div class="record-actions">
                            <button class="record-action-btn edit" onclick="app.editRecord(${record.id})">编辑</button>
                            <button class="record-action-btn delete" onclick="app.deleteRecord(${record.id})">删除</button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    renderAddRecord() {
        this.resetRecordForm();
        this.updateCategoryGrid();
    }

    resetRecordForm() {
        this.selectedType = 'expense';
        this.selectedCategoryId = null;
        this.editingRecordId = null;

        document.getElementById('recordAmount').value = '';
        document.getElementById('recordDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('recordRemark').value = '';

        document.querySelectorAll('.type-option').forEach(option => {
            option.classList.remove('active');
            if (option.dataset.type === this.selectedType) {
                option.classList.add('active');
            }
        });
    }

    setRecordType(type) {
        this.selectedType = type;
        this.selectedCategoryId = null;

        document.querySelectorAll('.type-option').forEach(option => {
            option.classList.remove('active');
            if (option.dataset.type === type) {
                option.classList.add('active');
            }
        });

        this.updateCategoryGrid();
    }

    updateCategoryGrid() {
        const container = document.getElementById('categoryGrid');
        const categories = this.data.categories[this.selectedType];

        container.innerHTML = categories.map(cat => `
            <div class="category-item ${this.selectedCategoryId === cat.id ? 'active' : ''}" 
                 data-category-id="${cat.id}">
                <span class="category-icon">${cat.icon}</span>
                <span class="category-name">${cat.name}</span>
            </div>
        `).join('');

        container.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', () => {
                this.selectedCategoryId = item.dataset.categoryId;
                this.updateCategoryGrid();
            });
        });
    }

    async saveRecord() {
        const amount = document.getElementById('recordAmount').value.trim();
        const date = document.getElementById('recordDate').value;
        const remark = document.getElementById('recordRemark').value.trim();

        if (!amount || parseFloat(amount) <= 0) {
            this.showToast('请输入有效的金额', 'error');
            return;
        }

        if (!this.selectedCategoryId) {
            this.showToast('请选择分类', 'error');
            return;
        }

        if (this.editingRecordId) {
            const index = this.data.records.findIndex(r => r.id === this.editingRecordId);
            if (index !== -1) {
                this.data.records[index] = {
                    ...this.data.records[index],
                    amount: parseFloat(amount).toFixed(2),
                    categoryId: this.selectedCategoryId,
                    date: date,
                    remark: remark,
                    type: this.selectedType
                };
            }
            this.showToast('记录已更新', 'success');
        } else {
            const newRecord = {
                id: Date.now(),
                ledgerId: this.currentLedgerId,
                type: this.selectedType,
                categoryId: this.selectedCategoryId,
                amount: parseFloat(amount).toFixed(2),
                date: date,
                remark: remark,
                createTime: new Date().toISOString()
            };

            this.data.records.push(newRecord);
            this.showToast('记录已添加', 'success');
        }

        await this.saveData();
        this.resetRecordForm();
        this.navigateTo('records');
    }

    editRecord(recordId) {
        const record = this.data.records.find(r => r.id === recordId);
        if (!record) return;

        this.editingRecordId = recordId;
        this.selectedType = record.type;
        this.selectedCategoryId = record.categoryId;

        document.getElementById('recordAmount').value = record.amount;
        document.getElementById('recordDate').value = record.date;
        document.getElementById('recordRemark').value = record.remark || '';

        document.querySelectorAll('.type-option').forEach(option => {
            option.classList.remove('active');
            if (option.dataset.type === record.type) {
                option.classList.add('active');
            }
        });

        this.updateCategoryGrid();
        this.navigateTo('add');
    }

    async deleteRecord(recordId) {
        if (!confirm('确定要删除这条记录吗？')) return;

        const index = this.data.records.findIndex(r => r.id === recordId);
        if (index !== -1) {
            this.data.records.splice(index, 1);
            await this.saveData();
            this.showToast('记录已删除', 'success');
            this.renderRecords();
        }
    }

    renderStatistics() {
        const records = this.getLedgerRecords(this.currentLedgerId);
        const month = this.getSelectedMonth();
        const statsType = document.getElementById('statsType').value;
        
        const monthRecords = month ? this.getRecordsByMonth(records, month) : records;
        const filteredRecords = monthRecords.filter(r => r.type === statsType);
        const categoryStats = this.getCategoryStats(monthRecords, statsType);

        const total = filteredRecords.reduce((sum, r) => sum + parseFloat(r.amount), 0);
        const count = filteredRecords.length;
        const average = count > 0 ? total / count : 0;

        document.getElementById('statsTotal').textContent = `¥${total.toFixed(2)}`;
        document.getElementById('statsCount').textContent = count;
        document.getElementById('statsAverage').textContent = `¥${average.toFixed(2)}`;

        this.renderStatsCategoryList(categoryStats);
    }

    renderStatsCategoryList(categoryStats) {
        const container = document.getElementById('statsCategoryList');

        if (categoryStats.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <span class="empty-icon">📊</span>
                    <span class="empty-text">暂无统计数据</span>
                </div>
            `;
            return;
        }

        const maxAmount = Math.max(...categoryStats.map(c => parseFloat(c.amount)));

        container.innerHTML = categoryStats.map(cat => `
            <div class="stats-category-item">
                <div class="stats-category-header">
                    <div class="stats-category-name">
                        <div class="stats-category-icon">${cat.icon}</div>
                        ${cat.name}
                    </div>
                    <span class="stats-category-amount">¥${cat.amount}</span>
                </div>
                <div class="stats-category-bar">
                    <div class="stats-category-fill" style="width: ${maxAmount > 0 ? (parseFloat(cat.amount) / maxAmount * 100) : 0}%"></div>
                </div>
                <div class="stats-category-info">
                    <span>占比 ${cat.percent}%</span>
                    <span>${Math.round(parseFloat(cat.amount))} 笔</span>
                </div>
            </div>
        `).join('');
    }

    renderBudget() {
        const month = this.getSelectedMonth();
        const budget = this.data.budgets.find(b => 
            b.ledgerId === this.currentLedgerId && b.month === month
        );

        const records = this.getLedgerRecords(this.currentLedgerId);
        const monthRecords = month ? this.getRecordsByMonth(records, month) : records;
        const expense = monthRecords
            .filter(r => r.type === 'expense')
            .reduce((sum, r) => sum + parseFloat(r.amount), 0);

        const container = document.getElementById('budgetCard');

        if (!budget) {
            container.innerHTML = `
                <div class="empty-state">
                    <span class="empty-icon">💰</span>
                    <span class="empty-text">暂无预算设置</span>
                    <span class="empty-hint">点击右上角"设置预算"为当前月份设置预算</span>
                </div>
            `;
            return;
        }

        const budgetAmount = parseFloat(budget.amount);
        const used = Math.min(expense, budgetAmount);
        const remaining = Math.max(0, budgetAmount - expense);
        const progress = budgetAmount > 0 ? Math.min(100, (used / budgetAmount) * 100) : 0;

        container.innerHTML = `
            <div class="budget-detail-info">
                <div class="budget-detail-header">
                    <span class="budget-detail-title">${month} 预算</span>
                    <span class="budget-detail-amount">¥${budget.amount}</span>
                </div>
                <div class="budget-detail-stats">
                    <div class="budget-stat-item">
                        <div class="budget-stat-label">已使用</div>
                        <div class="budget-stat-value used">¥${used.toFixed(2)}</div>
                    </div>
                    <div class="budget-stat-item">
                        <div class="budget-stat-label">剩余</div>
                        <div class="budget-stat-value remaining">¥${remaining.toFixed(2)}</div>
                    </div>
                    <div class="budget-stat-item">
                        <div class="budget-stat-label">进度</div>
                        <div class="budget-stat-value">${progress.toFixed(1)}%</div>
                    </div>
                </div>
                <div class="progress-bar" style="height: 10px;">
                    <div class="progress-fill" style="width: ${progress}%; ${progress > 90 ? 'background: linear-gradient(90deg, #FF4D4F, #ff7875)' : ''}"></div>
                </div>
            </div>
            <div class="form-actions" style="background: none; border: none; padding: 0; margin-top: 20px;">
                <button class="btn btn-outline" onclick="app.editBudget()">修改预算</button>
                <button class="btn btn-outline" onclick="app.deleteBudget()" style="color: #FF4D4F; border-color: #FF4D4F;">删除预算</button>
            </div>
        `;
    }

    showBudgetModal() {
        const month = this.getSelectedMonth();
        const existingBudget = this.data.budgets.find(b => 
            b.ledgerId === this.currentLedgerId && b.month === month
        );

        const modalContent = `
            <div class="modal-header">
                <h3 class="modal-title">${existingBudget ? '修改预算' : '设置预算'}</h3>
                <button class="modal-close" onclick="app.closeModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">月份</label>
                    <input type="text" class="form-input" value="${month}" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">预算金额 <span class="required">*</span></label>
                    <input type="number" id="budgetAmountInput" class="form-input" step="0.01" min="0" placeholder="请输入预算金额" value="${existingBudget?.amount || ''}">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="app.closeModal()">取消</button>
                <button class="btn btn-primary" onclick="app.saveBudget()">保存</button>
            </div>
        `;

        this.showModal(modalContent);
    }

    async saveBudget() {
        const month = this.getSelectedMonth();
        const amount = document.getElementById('budgetAmountInput').value.trim();

        if (!amount || parseFloat(amount) <= 0) {
            this.showToast('请输入有效的预算金额', 'error');
            return;
        }

        const existingIndex = this.data.budgets.findIndex(b => 
            b.ledgerId === this.currentLedgerId && b.month === month
        );

        const budgetData = {
            id: Date.now(),
            ledgerId: this.currentLedgerId,
            month: month,
            amount: parseFloat(amount).toFixed(2),
            createTime: new Date().toISOString()
        };

        if (existingIndex !== -1) {
            this.data.budgets[existingIndex] = {
                ...this.data.budgets[existingIndex],
                ...budgetData,
                id: this.data.budgets[existingIndex].id
            };
        } else {
            this.data.budgets.push(budgetData);
        }

        await this.saveData();
        this.closeModal();
        this.showToast('预算已保存', 'success');
        this.renderBudget();
    }

    editBudget() {
        this.showBudgetModal();
    }

    async deleteBudget() {
        if (!confirm('确定要删除这个预算吗？')) return;

        const month = this.getSelectedMonth();
        const index = this.data.budgets.findIndex(b => 
            b.ledgerId === this.currentLedgerId && b.month === month
        );

        if (index !== -1) {
            this.data.budgets.splice(index, 1);
            await this.saveData();
            this.showToast('预算已删除', 'success');
            this.renderBudget();
        }
    }

    updateLedgerSelect() {
        const select = document.getElementById('settingsLedger');
        select.innerHTML = '';

        this.data.ledgers.forEach(ledger => {
            const option = document.createElement('option');
            option.value = ledger.id;
            option.textContent = ledger.name + (ledger.isDefault ? ' (默认)' : '');
            if (ledger.id === this.currentLedgerId) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

    updateLedgerDisplay() {
        const currentLedger = this.data.ledgers.find(l => l.id === this.currentLedgerId);
        if (currentLedger) {
            document.getElementById('currentLedgerName').textContent = currentLedger.name;
        }
    }

    showCreateLedgerModal() {
        const modalContent = `
            <div class="modal-header">
                <h3 class="modal-title">新建账本</h3>
                <button class="modal-close" onclick="app.closeModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">账本名称 <span class="required">*</span></label>
                    <input type="text" id="newLedgerName" class="form-input" placeholder="请输入账本名称">
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" id="setDefaultLedger">
                        设为默认账本
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="app.closeModal()">取消</button>
                <button class="btn btn-primary" onclick="app.createLedger()">创建</button>
            </div>
        `;

        this.showModal(modalContent);
    }

    async createLedger() {
        const name = document.getElementById('newLedgerName').value.trim();
        const setDefault = document.getElementById('setDefaultLedger').checked;

        if (!name) {
            this.showToast('请输入账本名称', 'error');
            return;
        }

        if (setDefault) {
            this.data.ledgers.forEach(l => l.isDefault = false);
        }

        const newLedger = {
            id: Date.now(),
            name: name,
            isDefault: setDefault,
            createTime: new Date().toISOString()
        };

        this.data.ledgers.push(newLedger);
        
        if (setDefault) {
            this.currentLedgerId = newLedger.id;
        }

        await this.saveData();
        this.closeModal();
        this.showToast('账本创建成功', 'success');
        this.render();
    }

    showModal(content) {
        const overlay = document.getElementById('modalOverlay');
        const modalContent = document.getElementById('modalContent');
        modalContent.innerHTML = content;
        overlay.style.display = 'flex';
    }

    closeModal() {
        const overlay = document.getElementById('modalOverlay');
        overlay.style.display = 'none';
    }

    showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        
        toastMessage.textContent = message;
        toast.className = `toast ${type}`;
        toast.style.display = 'block';

        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }
}

const app = new BookkeepApp();
