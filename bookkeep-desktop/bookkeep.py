import tkinter as tk
from tkinter import ttk, messagebox, filedialog
import sqlite3
import json
from datetime import datetime
from collections import defaultdict

class BookkeepApp:
    def __init__(self, root):
        self.root = root
        self.root.title("科科记账 - 桌面版")
        self.root.geometry("1200x800")
        self.root.minsize(900, 600)
        
        self.current_ledger_id = 1
        self.current_page = "dashboard"
        self.editing_record_id = None
        
        self.init_database()
        self.create_ui()
        self.refresh_all()
        
    def init_database(self):
        self.conn = sqlite3.connect('bookkeep.db')
        self.cursor = self.conn.cursor()
        
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS ledgers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                is_default INTEGER DEFAULT 0,
                create_time TEXT
            )
        ''')
        
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS categories (
                id TEXT PRIMARY KEY,
                name TEXT NOT NULL,
                type TEXT NOT NULL,
                icon TEXT
            )
        ''')
        
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS records (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ledger_id INTEGER NOT NULL,
                type TEXT NOT NULL,
                category_id TEXT NOT NULL,
                amount REAL NOT NULL,
                date TEXT NOT NULL,
                remark TEXT,
                create_time TEXT,
                FOREIGN KEY (ledger_id) REFERENCES ledgers (id),
                FOREIGN KEY (category_id) REFERENCES categories (id)
            )
        ''')
        
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS budgets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ledger_id INTEGER NOT NULL,
                month TEXT NOT NULL,
                amount REAL NOT NULL,
                create_time TEXT,
                FOREIGN KEY (ledger_id) REFERENCES ledgers (id)
            )
        ''')
        
        self.cursor.execute('SELECT COUNT(*) FROM ledgers')
        if self.cursor.fetchone()[0] == 0:
            self.cursor.execute('''
                INSERT INTO ledgers (name, is_default, create_time)
                VALUES (?, ?, ?)
            ''', ('个人账本', 1, datetime.now().isoformat()))
            
            default_categories = [
                ('income-1', '工资', 'income', '💼'),
                ('income-2', '奖金', 'income', '🎁'),
                ('income-3', '投资', 'income', '📈'),
                ('income-4', '兼职', 'income', '💻'),
                ('income-5', '红包', 'income', '🧧'),
                ('income-6', '其他', 'income', '📝'),
                ('expense-1', '餐饮', 'expense', '🍜'),
                ('expense-2', '交通', 'expense', '🚗'),
                ('expense-3', '购物', 'expense', '🛒'),
                ('expense-4', '娱乐', 'expense', '🎮'),
                ('expense-5', '居家', 'expense', '🏠'),
                ('expense-6', '医疗', 'expense', '💊'),
                ('expense-7', '教育', 'expense', '📚'),
                ('expense-8', '旅行', 'expense', '✈️'),
                ('expense-9', '其他', 'expense', '📝'),
            ]
            self.cursor.executemany('''
                INSERT INTO categories (id, name, type, icon)
                VALUES (?, ?, ?, ?)
            ''', default_categories)
            
            self.conn.commit()
            
    def create_ui(self):
        self.main_frame = ttk.Frame(self.root)
        self.main_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        self.sidebar = ttk.Frame(self.main_frame, width=200)
        self.sidebar.pack(side=tk.LEFT, fill=tk.Y)
        self.sidebar.pack_propagate(False)
        
        style = ttk.Style()
        style.configure('Sidebar.TButton', font=('Microsoft YaHei', 11), padding=10)
        style.configure('SidebarActive.TButton', font=('Microsoft YaHei', 11, 'bold'), padding=10, background='#07C160')
        style.configure('Title.TLabel', font=('Microsoft YaHei', 16, 'bold'))
        style.configure('Stat.TLabel', font=('Microsoft YaHei', 20, 'bold'))
        style.configure('Card.TFrame', relief=tk.RAISED, borderwidth=1)
        
        logo_frame = ttk.Frame(self.sidebar)
        logo_frame.pack(pady=20)
        
        ttk.Label(logo_frame, text="📊 科科记账", font=('Microsoft YaHei', 14, 'bold')).pack()
        
        nav_items = [
            ("dashboard", "🏠 仪表盘"),
            ("records", "📝 账单记录"),
            ("add", "➕ 记一笔"),
            ("statistics", "📈 统计分析"),
            ("budget", "💰 预算管理"),
            ("settings", "⚙️ 设置"),
        ]
        
        self.nav_buttons = {}
        for page_id, text in nav_items:
            btn = ttk.Button(
                self.sidebar,
                text=text,
                style='Sidebar.TButton',
                command=lambda p=page_id: self.switch_page(p)
            )
            btn.pack(fill=tk.X, pady=2, padx=5)
            self.nav_buttons[page_id] = btn
        
        ledger_frame = ttk.Frame(self.sidebar)
        ledger_frame.pack(side=tk.BOTTOM, fill=tk.X, pady=20, padx=5)
        
        ttk.Label(ledger_frame, text="当前账本:", font=('Microsoft YaHei', 9)).pack(anchor=tk.W)
        self.ledger_label = ttk.Label(ledger_frame, text="个人账本", font=('Microsoft YaHei', 10, 'bold'))
        self.ledger_label.pack(anchor=tk.W)
        
        self.content_frame = ttk.Frame(self.main_frame)
        self.content_frame.pack(side=tk.RIGHT, fill=tk.BOTH, expand=True, padx=(10, 0))
        
        self.ledger_bar = ttk.Frame(self.content_frame)
        
        self.ledger_bar_label = ttk.Label(
            self.ledger_bar, 
            text="", 
            font=('Microsoft YaHei', 10),
            foreground='#07C160'
        )
        self.ledger_bar_label.pack(pady=5)
        
        self.pages = {}
        self.create_dashboard_page()
        self.create_records_page()
        self.create_add_page()
        self.create_statistics_page()
        self.create_budget_page()
        self.create_settings_page()
        
    def create_dashboard_page(self):
        frame = ttk.Frame(self.content_frame)
        self.pages["dashboard"] = frame
        
        header_frame = ttk.Frame(frame)
        header_frame.pack(fill=tk.X, pady=10)
        
        ttk.Label(header_frame, text="仪表盘", style='Title.TLabel').pack(side=tk.LEFT)
        
        self.dashboard_month_var = tk.StringVar(value=datetime.now().strftime("%Y-%m"))
        months = []
        for y in range(datetime.now().year, datetime.now().year - 5, -1):
            for m in range(12, 0, -1):
                if y == datetime.now().year and m > datetime.now().month:
                    continue
                months.append(f"{y}-{m:02d}")
        
        self.dashboard_month_combo = ttk.Combobox(
            header_frame, 
            textvariable=self.dashboard_month_var,
            values=months,
            state='readonly',
            width=12
        )
        self.dashboard_month_combo.pack(side=tk.RIGHT)
        self.dashboard_month_combo.bind('<<ComboboxSelected>>', lambda e: self.refresh_dashboard())
        
        stats_frame = ttk.Frame(frame)
        stats_frame.pack(fill=tk.X, pady=10)
        
        self.income_label = ttk.Label(stats_frame, text="¥0.00", style='Stat.TLabel', foreground='#07C160')
        self.income_label.pack(side=tk.LEFT, expand=True, fill=tk.X)
        
        self.expense_label = ttk.Label(stats_frame, text="¥0.00", style='Stat.TLabel', foreground='#FF4D4F')
        self.expense_label.pack(side=tk.LEFT, expand=True, fill=tk.X)
        
        self.balance_label = ttk.Label(stats_frame, text="¥0.00", style='Stat.TLabel', foreground='#FAAD14')
        self.balance_label.pack(side=tk.LEFT, expand=True, fill=tk.X)
        
        recent_frame = ttk.LabelFrame(frame, text="最近记录", padding=10)
        recent_frame.pack(fill=tk.BOTH, expand=True, pady=10)
        
        columns = ('date', 'type', 'category', 'amount', 'remark')
        self.recent_tree = ttk.Treeview(recent_frame, columns=columns, show='headings', height=10)
        
        self.recent_tree.heading('date', text='日期')
        self.recent_tree.heading('type', text='类型')
        self.recent_tree.heading('category', text='分类')
        self.recent_tree.heading('amount', text='金额')
        self.recent_tree.heading('remark', text='备注')
        
        self.recent_tree.column('date', width=100)
        self.recent_tree.column('type', width=60)
        self.recent_tree.column('category', width=100)
        self.recent_tree.column('amount', width=100)
        self.recent_tree.column('remark', width=200)
        
        scrollbar = ttk.Scrollbar(recent_frame, orient=tk.VERTICAL, command=self.recent_tree.yview)
        self.recent_tree.configure(yscrollcommand=scrollbar.set)
        
        self.recent_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        
    def create_records_page(self):
        frame = ttk.Frame(self.content_frame)
        self.pages["records"] = frame
        
        header_frame = ttk.Frame(frame)
        header_frame.pack(fill=tk.X, pady=10)
        
        ttk.Label(header_frame, text="账单记录", style='Title.TLabel').pack(side=tk.LEFT)
        
        ttk.Button(
            header_frame,
            text="➕ 新增记录",
            command=lambda: self.switch_page("add")
        ).pack(side=tk.RIGHT)
        
        filter_frame = ttk.Frame(frame)
        filter_frame.pack(fill=tk.X, pady=5)
        
        ttk.Label(filter_frame, text="类型:").pack(side=tk.LEFT, padx=(0, 5))
        self.filter_type_var = tk.StringVar(value="all")
        type_combo = ttk.Combobox(
            filter_frame,
            textvariable=self.filter_type_var,
            values=['all', 'income', 'expense'],
            state='readonly',
            width=10
        )
        type_combo.pack(side=tk.LEFT, padx=(0, 15))
        type_combo.bind('<<ComboboxSelected>>', lambda e: self.refresh_records())
        
        ttk.Label(filter_frame, text="分类:").pack(side=tk.LEFT, padx=(0, 5))
        self.filter_category_var = tk.StringVar(value="all")
        self.filter_category_combo = ttk.Combobox(
            filter_frame,
            textvariable=self.filter_category_var,
            state='readonly',
            width=12
        )
        self.filter_category_combo.pack(side=tk.LEFT, padx=(0, 15))
        self.filter_category_combo.bind('<<ComboboxSelected>>', lambda e: self.refresh_records())
        
        ttk.Label(filter_frame, text="月份:").pack(side=tk.LEFT, padx=(0, 5))
        self.records_month_var = tk.StringVar(value=datetime.now().strftime("%Y-%m"))
        months = []
        for y in range(datetime.now().year, datetime.now().year - 5, -1):
            for m in range(12, 0, -1):
                if y == datetime.now().year and m > datetime.now().month:
                    continue
                months.append(f"{y}-{m:02d}")
        
        records_month_combo = ttk.Combobox(
            filter_frame,
            textvariable=self.records_month_var,
            values=months,
            state='readonly',
            width=12
        )
        records_month_combo.pack(side=tk.LEFT)
        records_month_combo.bind('<<ComboboxSelected>>', lambda e: self.refresh_records())
        
        list_frame = ttk.Frame(frame)
        list_frame.pack(fill=tk.BOTH, expand=True, pady=10)
        
        columns = ('id', 'date', 'type', 'category', 'amount', 'remark')
        self.records_tree = ttk.Treeview(list_frame, columns=columns, show='headings', height=15)
        
        self.records_tree.heading('id', text='ID')
        self.records_tree.heading('date', text='日期')
        self.records_tree.heading('type', text='类型')
        self.records_tree.heading('category', text='分类')
        self.records_tree.heading('amount', text='金额')
        self.records_tree.heading('remark', text='备注')
        
        self.records_tree.column('id', width=40)
        self.records_tree.column('date', width=100)
        self.records_tree.column('type', width=60)
        self.records_tree.column('category', width=100)
        self.records_tree.column('amount', width=100)
        self.records_tree.column('remark', width=200)
        
        scrollbar = ttk.Scrollbar(list_frame, orient=tk.VERTICAL, command=self.records_tree.yview)
        self.records_tree.configure(yscrollcommand=scrollbar.set)
        
        self.records_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        
        action_frame = ttk.Frame(frame)
        action_frame.pack(fill=tk.X, pady=5)
        
        ttk.Button(
            action_frame,
            text="编辑",
            command=self.edit_selected_record
        ).pack(side=tk.LEFT, padx=5)
        
        ttk.Button(
            action_frame,
            text="删除",
            command=self.delete_selected_record
        ).pack(side=tk.LEFT, padx=5)
        
    def create_add_page(self):
        frame = ttk.Frame(self.content_frame)
        self.pages["add"] = frame
        
        header_frame = ttk.Frame(frame)
        header_frame.pack(fill=tk.X, pady=10)
        
        self.add_title_label = ttk.Label(header_frame, text="记一笔", style='Title.TLabel')
        self.add_title_label.pack(side=tk.LEFT)
        
        form_frame = ttk.LabelFrame(frame, text="记录信息", padding=20)
        form_frame.pack(fill=tk.BOTH, expand=True, pady=10, padx=50)
        
        ttk.Label(form_frame, text="类型:", font=('Microsoft YaHei', 10)).grid(row=0, column=0, sticky=tk.W, pady=10)
        
        type_frame = ttk.Frame(form_frame)
        type_frame.grid(row=0, column=1, sticky=tk.W, pady=10)
        
        self.add_type_var = tk.StringVar(value="expense")
        self.expense_radio = ttk.Radiobutton(
            type_frame,
            text="支出",
            value="expense",
            variable=self.add_type_var,
            command=self.update_category_options
        )
        self.expense_radio.pack(side=tk.LEFT, padx=10)
        
        self.income_radio = ttk.Radiobutton(
            type_frame,
            text="收入",
            value="income",
            variable=self.add_type_var,
            command=self.update_category_options
        )
        self.income_radio.pack(side=tk.LEFT, padx=10)
        
        ttk.Label(form_frame, text="金额:", font=('Microsoft YaHei', 10)).grid(row=1, column=0, sticky=tk.W, pady=10)
        
        self.add_amount_var = tk.StringVar()
        amount_entry = ttk.Entry(form_frame, textvariable=self.add_amount_var, font=('Microsoft YaHei', 12), width=20)
        amount_entry.grid(row=1, column=1, sticky=tk.W, pady=10)
        
        ttk.Label(form_frame, text="分类:", font=('Microsoft YaHei', 10)).grid(row=2, column=0, sticky=tk.NW, pady=10)
        
        self.category_frame = ttk.Frame(form_frame)
        self.category_frame.grid(row=2, column=1, sticky=tk.W, pady=10)
        
        ttk.Label(form_frame, text="日期:", font=('Microsoft YaHei', 10)).grid(row=3, column=0, sticky=tk.W, pady=10)
        
        self.add_date_var = tk.StringVar(value=datetime.now().strftime("%Y-%m-%d"))
        date_entry = ttk.Entry(form_frame, textvariable=self.add_date_var, font=('Microsoft YaHei', 10), width=20)
        date_entry.grid(row=3, column=1, sticky=tk.W, pady=10)
        
        ttk.Label(form_frame, text="备注:", font=('Microsoft YaHei', 10)).grid(row=4, column=0, sticky=tk.NW, pady=10)
        
        self.add_remark_text = tk.Text(form_frame, height=3, width=40, font=('Microsoft YaHei', 10))
        self.add_remark_text.grid(row=4, column=1, sticky=tk.W, pady=10)
        
        btn_frame = ttk.Frame(frame)
        btn_frame.pack(fill=tk.X, pady=20, padx=50)
        
        ttk.Button(
            btn_frame,
            text="取消",
            command=self.reset_add_form
        ).pack(side=tk.RIGHT, padx=10)
        
        ttk.Button(
            btn_frame,
            text="保存",
            command=self.save_record
        ).pack(side=tk.RIGHT, padx=10)
        
    def create_statistics_page(self):
        frame = ttk.Frame(self.content_frame)
        self.pages["statistics"] = frame
        
        header_frame = ttk.Frame(frame)
        header_frame.pack(fill=tk.X, pady=10)
        
        ttk.Label(header_frame, text="统计分析", style='Title.TLabel').pack(side=tk.LEFT)
        
        self.stats_type_var = tk.StringVar(value="expense")
        type_combo = ttk.Combobox(
            header_frame,
            textvariable=self.stats_type_var,
            values=['expense', 'income'],
            state='readonly',
            width=10
        )
        type_combo.pack(side=tk.RIGHT, padx=10)
        type_combo.bind('<<ComboboxSelected>>', lambda e: self.refresh_statistics())
        
        self.stats_month_var = tk.StringVar(value=datetime.now().strftime("%Y-%m"))
        months = []
        for y in range(datetime.now().year, datetime.now().year - 5, -1):
            for m in range(12, 0, -1):
                if y == datetime.now().year and m > datetime.now().month:
                    continue
                months.append(f"{y}-{m:02d}")
        
        stats_month_combo = ttk.Combobox(
            header_frame,
            textvariable=self.stats_month_var,
            values=months,
            state='readonly',
            width=12
        )
        stats_month_combo.pack(side=tk.RIGHT)
        stats_month_combo.bind('<<ComboboxSelected>>', lambda e: self.refresh_statistics())
        
        summary_frame = ttk.LabelFrame(frame, text="统计摘要", padding=15)
        summary_frame.pack(fill=tk.X, pady=10)
        
        self.stats_total_label = ttk.Label(summary_frame, text="总金额: ¥0.00", font=('Microsoft YaHei', 12, 'bold'))
        self.stats_total_label.pack(side=tk.LEFT, padx=20)
        
        self.stats_count_label = ttk.Label(summary_frame, text="记录数: 0", font=('Microsoft YaHei', 12))
        self.stats_count_label.pack(side=tk.LEFT, padx=20)
        
        self.stats_avg_label = ttk.Label(summary_frame, text="平均: ¥0.00", font=('Microsoft YaHei', 12))
        self.stats_avg_label.pack(side=tk.LEFT, padx=20)
        
        detail_frame = ttk.LabelFrame(frame, text="分类明细", padding=15)
        detail_frame.pack(fill=tk.BOTH, expand=True, pady=10)
        
        columns = ('category', 'amount', 'percent', 'count')
        self.stats_tree = ttk.Treeview(detail_frame, columns=columns, show='headings', height=15)
        
        self.stats_tree.heading('category', text='分类')
        self.stats_tree.heading('amount', text='金额')
        self.stats_tree.heading('percent', text='占比')
        self.stats_tree.heading('count', text='笔数')
        
        self.stats_tree.column('category', width=150)
        self.stats_tree.column('amount', width=150)
        self.stats_tree.column('percent', width=100)
        self.stats_tree.column('count', width=100)
        
        scrollbar = ttk.Scrollbar(detail_frame, orient=tk.VERTICAL, command=self.stats_tree.yview)
        self.stats_tree.configure(yscrollcommand=scrollbar.set)
        
        self.stats_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        
    def create_budget_page(self):
        frame = ttk.Frame(self.content_frame)
        self.pages["budget"] = frame
        
        header_frame = ttk.Frame(frame)
        header_frame.pack(fill=tk.X, pady=10)
        
        ttk.Label(header_frame, text="预算管理", style='Title.TLabel').pack(side=tk.LEFT)
        
        self.budget_month_var = tk.StringVar(value=datetime.now().strftime("%Y-%m"))
        months = []
        for y in range(datetime.now().year, datetime.now().year - 5, -1):
            for m in range(12, 0, -1):
                if y == datetime.now().year and m > datetime.now().month:
                    continue
                months.append(f"{y}-{m:02d}")
        
        budget_month_combo = ttk.Combobox(
            header_frame,
            textvariable=self.budget_month_var,
            values=months,
            state='readonly',
            width=12
        )
        budget_month_combo.pack(side=tk.RIGHT, padx=10)
        budget_month_combo.bind('<<ComboboxSelected>>', lambda e: self.refresh_budget())
        
        ttk.Button(
            header_frame,
            text="设置预算",
            command=self.show_budget_dialog
        ).pack(side=tk.RIGHT)
        
        self.budget_detail_frame = ttk.LabelFrame(frame, text="预算详情", padding=20)
        self.budget_detail_frame.pack(fill=tk.BOTH, expand=True, pady=10, padx=50)
        
        self.budget_info_label = ttk.Label(self.budget_detail_frame, text="暂无预算设置", font=('Microsoft YaHei', 12))
        self.budget_info_label.pack(pady=20)
        
    def create_settings_page(self):
        frame = ttk.Frame(self.content_frame)
        self.pages["settings"] = frame
        
        header_frame = ttk.Frame(frame)
        header_frame.pack(fill=tk.X, pady=10)
        
        ttk.Label(header_frame, text="设置", style='Title.TLabel').pack(side=tk.LEFT)
        
        ledger_frame = ttk.LabelFrame(frame, text="账本管理", padding=15)
        ledger_frame.pack(fill=tk.X, pady=10)
        
        ttk.Label(ledger_frame, text="当前账本:").pack(side=tk.LEFT, padx=(0, 10))
        
        self.settings_ledger_var = tk.StringVar()
        self.settings_ledger_combo = ttk.Combobox(
            ledger_frame,
            textvariable=self.settings_ledger_var,
            state='readonly',
            width=15
        )
        self.settings_ledger_combo.pack(side=tk.LEFT, padx=(0, 20))
        self.settings_ledger_combo.bind('<<ComboboxSelected>>', self.on_ledger_changed)
        
        ttk.Button(
            ledger_frame,
            text="新建账本",
            command=self.show_create_ledger_dialog
        ).pack(side=tk.LEFT)
        
        data_frame = ttk.LabelFrame(frame, text="数据管理", padding=15)
        data_frame.pack(fill=tk.X, pady=10)
        
        ttk.Button(
            data_frame,
            text="📤 导出数据",
            command=self.export_data
        ).pack(side=tk.LEFT, padx=10)
        
        ttk.Button(
            data_frame,
            text="📥 导入数据",
            command=self.import_data
        ).pack(side=tk.LEFT, padx=10)
        
        about_frame = ttk.LabelFrame(frame, text="关于", padding=15)
        about_frame.pack(fill=tk.X, pady=10)
        
        ttk.Label(about_frame, text="科科记账 - 桌面版", font=('Microsoft YaHei', 11, 'bold')).pack(anchor=tk.W)
        ttk.Label(about_frame, text="版本: 1.0.0", font=('Microsoft YaHei', 10)).pack(anchor=tk.W, pady=5)
        ttk.Label(about_frame, text="一个简单易用的个人记账应用", font=('Microsoft YaHei', 10)).pack(anchor=tk.W)
        
    def switch_page(self, page):
        self.current_page = page
        
        for page_id, btn in self.nav_buttons.items():
            if page_id == page:
                btn.configure(style='SidebarActive.TButton')
            else:
                btn.configure(style='Sidebar.TButton')
        
        self.ledger_bar.pack_forget()
        for p in self.pages.values():
            p.pack_forget()
        
        if page != "settings":
            self.ledger_bar.pack(fill=tk.X, pady=(0, 5))
        
        self.pages[page].pack(fill=tk.BOTH, expand=True)
        
        self.refresh_page()
        
    def refresh_page(self):
        if self.current_page == "dashboard":
            self.refresh_dashboard()
        elif self.current_page == "records":
            self.refresh_records()
        elif self.current_page == "add":
            self.reset_add_form()
        elif self.current_page == "statistics":
            self.refresh_statistics()
        elif self.current_page == "budget":
            self.refresh_budget()
        elif self.current_page == "settings":
            self.refresh_settings()
            
    def refresh_all(self):
        self.refresh_ledger_display()
        self.refresh_page()
        
    def refresh_dashboard(self):
        month = self.dashboard_month_var.get()
        
        query = '''
            SELECT r.type, r.amount, c.name as category_name
            FROM records r
            JOIN categories c ON r.category_id = c.id
            WHERE r.ledger_id = ? AND r.date LIKE ?
        '''
        self.cursor.execute(query, (self.current_ledger_id, f"{month}%"))
        records = self.cursor.fetchall()
        
        income = sum(r[1] for r in records if r[0] == 'income')
        expense = sum(r[1] for r in records if r[0] == 'expense')
        balance = income - expense
        
        self.income_label.config(text=f"收入: ¥{income:.2f}")
        self.expense_label.config(text=f"支出: ¥{expense:.2f}")
        self.balance_label.config(text=f"结余: ¥{balance:.2f}")
        
        for item in self.recent_tree.get_children():
            self.recent_tree.delete(item)
        
        query = '''
            SELECT r.date, r.type, c.name, r.amount, r.remark
            FROM records r
            JOIN categories c ON r.category_id = c.id
            WHERE r.ledger_id = ?
            ORDER BY r.date DESC, r.id DESC
            LIMIT 10
        '''
        self.cursor.execute(query, (self.current_ledger_id,))
        recent = self.cursor.fetchall()
        
        for record in recent:
            type_text = "收入" if record[1] == 'income' else "支出"
            amount_text = f"¥{record[3]:.2f}"
            self.recent_tree.insert('', tk.END, values=(
                record[0], type_text, record[2], amount_text, record[4] or ''
            ))
            
    def refresh_records(self):
        month = self.records_month_var.get()
        record_type = self.filter_type_var.get()
        category = self.filter_category_var.get()
        
        all_categories = self.get_categories(record_type)
        self.filter_category_combo['values'] = ['all'] + [c[1] for c in all_categories]
        
        query = '''
            SELECT r.id, r.date, r.type, c.name, r.amount, r.remark
            FROM records r
            JOIN categories c ON r.category_id = c.id
            WHERE r.ledger_id = ? AND r.date LIKE ?
        '''
        params = [self.current_ledger_id, f"{month}%"]
        
        if record_type != 'all':
            query += ' AND r.type = ?'
            params.append(record_type)
        
        if category != 'all':
            query += ' AND c.name = ?'
            params.append(category)
        
        query += ' ORDER BY r.date DESC, r.id DESC'
        
        self.cursor.execute(query, params)
        records = self.cursor.fetchall()
        
        for item in self.records_tree.get_children():
            self.records_tree.delete(item)
        
        for record in records:
            type_text = "收入" if record[2] == 'income' else "支出"
            amount_text = f"¥{record[4]:.2f}"
            self.records_tree.insert('', tk.END, values=(
                record[0], record[1], type_text, record[3], amount_text, record[5] or ''
            ))
            
    def update_category_options(self):
        record_type = self.add_type_var.get()
        categories = self.get_categories(record_type)
        
        for widget in self.category_frame.winfo_children():
            widget.destroy()
        
        self.selected_category_id = None
        
        row, col = 0, 0
        for cat_id, cat_name, cat_icon in categories:
            btn = ttk.Button(
                self.category_frame,
                text=f"{cat_icon} {cat_name}",
                command=lambda cid=cat_id: self.select_category(cid)
            )
            btn.grid(row=row, column=col, padx=5, pady=5)
            
            col += 1
            if col >= 4:
                col = 0
                row += 1
                
    def select_category(self, category_id):
        self.selected_category_id = category_id
        
    def get_categories(self, category_type=None):
        query = 'SELECT id, name, icon FROM categories'
        params = []
        
        if category_type:
            query += ' WHERE type = ?'
            params.append(category_type)
        
        self.cursor.execute(query, params)
        return self.cursor.fetchall()
        
    def reset_add_form(self):
        self.editing_record_id = None
        self.add_type_var.set("expense")
        self.add_amount_var.set("")
        self.add_date_var.set(datetime.now().strftime("%Y-%m-%d"))
        self.add_remark_text.delete(1.0, tk.END)
        self.add_title_label.config(text="记一笔")
        self.update_category_options()
        self.selected_category_id = None
        
    def save_record(self):
        amount = self.add_amount_var.get().strip()
        
        if not amount or float(amount) <= 0:
            messagebox.showerror("错误", "请输入有效的金额")
            return
        
        if not self.selected_category_id:
            messagebox.showerror("错误", "请选择分类")
            return
        
        amount = float(amount)
        record_type = self.add_type_var.get()
        category_id = self.selected_category_id
        date = self.add_date_var.get()
        remark = self.add_remark_text.get(1.0, tk.END).strip()
        
        if self.editing_record_id:
            query = '''
                UPDATE records
                SET type = ?, category_id = ?, amount = ?, date = ?, remark = ?
                WHERE id = ?
            '''
            self.cursor.execute(query, (record_type, category_id, amount, date, remark, self.editing_record_id))
            messagebox.showinfo("成功", "记录已更新")
        else:
            query = '''
                INSERT INTO records (ledger_id, type, category_id, amount, date, remark, create_time)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            '''
            self.cursor.execute(query, (
                self.current_ledger_id, record_type, category_id, amount, date, remark,
                datetime.now().isoformat()
            ))
            messagebox.showinfo("成功", "记录已添加")
        
        self.conn.commit()
        self.reset_add_form()
        self.switch_page("records")
        
    def edit_selected_record(self):
        selection = self.records_tree.selection()
        if not selection:
            messagebox.showwarning("提示", "请先选择要编辑的记录")
            return
        
        item = self.records_tree.item(selection[0])
        record_id = item['values'][0]
        
        query = '''
            SELECT r.id, r.type, r.category_id, r.amount, r.date, r.remark
            FROM records r
            WHERE r.id = ?
        '''
        self.cursor.execute(query, (record_id,))
        record = self.cursor.fetchone()
        
        if record:
            self.editing_record_id = record[0]
            self.add_type_var.set(record[1])
            self.add_amount_var.set(f"{record[3]:.2f}")
            self.add_date_var.set(record[4])
            self.add_remark_text.delete(1.0, tk.END)
            if record[5]:
                self.add_remark_text.insert(tk.END, record[5])
            
            self.selected_category_id = record[2]
            self.update_category_options()
            self.add_title_label.config(text="编辑记录")
            self.switch_page("add")
            
    def delete_selected_record(self):
        selection = self.records_tree.selection()
        if not selection:
            messagebox.showwarning("提示", "请先选择要删除的记录")
            return
        
        if not messagebox.askyesno("确认", "确定要删除这条记录吗？"):
            return
        
        item = self.records_tree.item(selection[0])
        record_id = item['values'][0]
        
        query = 'DELETE FROM records WHERE id = ?'
        self.cursor.execute(query, (record_id,))
        self.conn.commit()
        
        messagebox.showinfo("成功", "记录已删除")
        self.refresh_records()
        
    def refresh_statistics(self):
        month = self.stats_month_var.get()
        stats_type = self.stats_type_var.get()
        
        query = '''
            SELECT c.name, c.icon, r.amount
            FROM records r
            JOIN categories c ON r.category_id = c.id
            WHERE r.ledger_id = ? AND r.type = ? AND r.date LIKE ?
        '''
        self.cursor.execute(query, (self.current_ledger_id, stats_type, f"{month}%"))
        records = self.cursor.fetchall()
        
        category_stats = defaultdict(lambda: {'amount': 0, 'count': 0})
        total_amount = 0
        
        for name, icon, amount in records:
            category_stats[name]['amount'] += amount
            category_stats[name]['count'] += 1
            total_amount += amount
        
        count = len(records)
        avg_amount = total_amount / count if count > 0 else 0
        
        type_text = "收入" if stats_type == 'income' else "支出"
        self.stats_total_label.config(text=f"{type_text}总金额: ¥{total_amount:.2f}")
        self.stats_count_label.config(text=f"记录数: {count}")
        self.stats_avg_label.config(text=f"平均每笔: ¥{avg_amount:.2f}")
        
        for item in self.stats_tree.get_children():
            self.stats_tree.delete(item)
        
        sorted_categories = sorted(
            category_stats.items(),
            key=lambda x: x[1]['amount'],
            reverse=True
        )
        
        for name, data in sorted_categories:
            percent = (data['amount'] / total_amount * 100) if total_amount > 0 else 0
            self.stats_tree.insert('', tk.END, values=(
                name, f"¥{data['amount']:.2f}", f"{percent:.1f}%", data['count']
            ))
            
    def refresh_budget(self):
        month = self.budget_month_var.get()
        
        query = '''
            SELECT amount FROM budgets
            WHERE ledger_id = ? AND month = ?
        '''
        self.cursor.execute(query, (self.current_ledger_id, month))
        budget = self.cursor.fetchone()
        
        if budget:
            budget_amount = budget[0]
            
            query = '''
                SELECT SUM(amount) FROM records
                WHERE ledger_id = ? AND type = 'expense' AND date LIKE ?
            '''
            self.cursor.execute(query, (self.current_ledger_id, f"{month}%"))
            used = self.cursor.fetchone()[0] or 0
            
            remaining = budget_amount - used
            progress = (used / budget_amount * 100) if budget_amount > 0 else 0
            
            for widget in self.budget_detail_frame.winfo_children():
                widget.destroy()
            
            ttk.Label(
                self.budget_detail_frame,
                text=f"{month} 预算",
                font=('Microsoft YaHei', 14, 'bold')
            ).pack(pady=10)
            
            info_frame = ttk.Frame(self.budget_detail_frame)
            info_frame.pack(pady=10)
            
            ttk.Label(
                info_frame,
                text=f"预算金额: ¥{budget_amount:.2f}",
                font=('Microsoft YaHei', 12)
            ).pack(side=tk.LEFT, padx=20)
            
            ttk.Label(
                info_frame,
                text=f"已使用: ¥{used:.2f}",
                font=('Microsoft YaHei', 12),
                foreground='#FF4D4F'
            ).pack(side=tk.LEFT, padx=20)
            
            ttk.Label(
                info_frame,
                text=f"剩余: ¥{remaining:.2f}",
                font=('Microsoft YaHei', 12),
                foreground='#07C160'
            ).pack(side=tk.LEFT, padx=20)
            
            progress_frame = ttk.Frame(self.budget_detail_frame)
            progress_frame.pack(fill=tk.X, pady=20, padx=50)
            
            ttk.Label(progress_frame, text=f"进度: {progress:.1f}%").pack()
            
            canvas = tk.Canvas(progress_frame, width=400, height=30, bg='white')
            canvas.pack(pady=5)
            
            fill_width = min(progress, 100) * 4
            fill_color = '#07C160' if progress <= 90 else '#FF4D4F'
            canvas.create_rectangle(0, 5, 400, 25, fill='#f0f0f0', outline='')
            canvas.create_rectangle(0, 5, fill_width, 25, fill=fill_color, outline='')
            
            btn_frame = ttk.Frame(self.budget_detail_frame)
            btn_frame.pack(pady=10)
            
            ttk.Button(
                btn_frame,
                text="修改预算",
                command=self.show_budget_dialog
            ).pack(side=tk.LEFT, padx=10)
            
            ttk.Button(
                btn_frame,
                text="删除预算",
                command=self.delete_budget
            ).pack(side=tk.LEFT, padx=10)
        else:
            for widget in self.budget_detail_frame.winfo_children():
                widget.destroy()
            
            self.budget_info_label = ttk.Label(
                self.budget_detail_frame,
                text="暂无预算设置，请点击上方\"设置预算\"按钮",
                font=('Microsoft YaHei', 12)
            )
            self.budget_info_label.pack(pady=20)
            
    def show_budget_dialog(self):
        month = self.budget_month_var.get()
        
        query = 'SELECT amount FROM budgets WHERE ledger_id = ? AND month = ?'
        self.cursor.execute(query, (self.current_ledger_id, month))
        existing = self.cursor.fetchone()
        
        dialog = tk.Toplevel(self.root)
        dialog.title("设置预算")
        dialog.geometry("300x150")
        dialog.transient(self.root)
        dialog.grab_set()
        
        ttk.Label(dialog, text=f"{month} 预算金额:", font=('Microsoft YaHei', 10)).pack(pady=10)
        
        amount_var = tk.StringVar(value=f"{existing[0]:.2f}" if existing else "")
        amount_entry = ttk.Entry(dialog, textvariable=amount_var, font=('Microsoft YaHei', 12), width=20)
        amount_entry.pack(pady=5)
        
        def save_budget():
            amount = amount_var.get().strip()
            if not amount or float(amount) <= 0:
                messagebox.showerror("错误", "请输入有效的预算金额")
                return
            
            amount = float(amount)
            
            if existing:
                query = 'UPDATE budgets SET amount = ? WHERE ledger_id = ? AND month = ?'
                self.cursor.execute(query, (amount, self.current_ledger_id, month))
            else:
                query = '''
                    INSERT INTO budgets (ledger_id, month, amount, create_time)
                    VALUES (?, ?, ?, ?)
                '''
                self.cursor.execute(query, (self.current_ledger_id, month, amount, datetime.now().isoformat()))
            
            self.conn.commit()
            dialog.destroy()
            messagebox.showinfo("成功", "预算已保存")
            self.refresh_budget()
        
        btn_frame = ttk.Frame(dialog)
        btn_frame.pack(pady=20)
        
        ttk.Button(btn_frame, text="取消", command=dialog.destroy).pack(side=tk.LEFT, padx=10)
        ttk.Button(btn_frame, text="保存", command=save_budget).pack(side=tk.LEFT, padx=10)
        
    def delete_budget(self):
        if not messagebox.askyesno("确认", "确定要删除这个预算吗？"):
            return
        
        month = self.budget_month_var.get()
        query = 'DELETE FROM budgets WHERE ledger_id = ? AND month = ?'
        self.cursor.execute(query, (self.current_ledger_id, month))
        self.conn.commit()
        
        messagebox.showinfo("成功", "预算已删除")
        self.refresh_budget()
        
    def refresh_settings(self):
        self.cursor.execute('SELECT id, name FROM ledgers')
        ledgers = self.cursor.fetchall()
        
        self.settings_ledger_combo['values'] = [l[1] for l in ledgers]
        
        current_ledger = self.cursor.execute(
            'SELECT name FROM ledgers WHERE id = ?', (self.current_ledger_id,)
        ).fetchone()
        
        if current_ledger:
            self.settings_ledger_var.set(current_ledger[0])
            
    def refresh_ledger_display(self):
        query = 'SELECT name FROM ledgers WHERE id = ?'
        self.cursor.execute(query, (self.current_ledger_id,))
        ledger = self.cursor.fetchone()
        
        if ledger:
            self.ledger_label.config(text=ledger[0])
            self.ledger_bar_label.config(text=f"📒 当前账本: {ledger[0]}")
            
    def on_ledger_changed(self, event):
        ledger_name = self.settings_ledger_var.get()
        
        query = 'SELECT id FROM ledgers WHERE name = ?'
        self.cursor.execute(query, (ledger_name,))
        ledger = self.cursor.fetchone()
        
        if ledger:
            self.current_ledger_id = ledger[0]
            self.refresh_ledger_display()
            self.refresh_page()
            
    def show_create_ledger_dialog(self):
        dialog = tk.Toplevel(self.root)
        dialog.title("新建账本")
        dialog.geometry("350x180")
        dialog.transient(self.root)
        dialog.grab_set()
        
        ttk.Label(dialog, text="账本名称:", font=('Microsoft YaHei', 10)).pack(pady=10)
        
        name_var = tk.StringVar()
        name_entry = ttk.Entry(dialog, textvariable=name_var, font=('Microsoft YaHei', 10), width=30)
        name_entry.pack(pady=5)
        
        default_var = tk.BooleanVar(value=False)
        default_check = ttk.Checkbutton(dialog, text="设为默认账本", variable=default_var)
        default_check.pack(pady=5)
        
        def create_ledger():
            name = name_var.get().strip()
            if not name:
                messagebox.showerror("错误", "请输入账本名称")
                return
            
            if default_var.get():
                self.cursor.execute('UPDATE ledgers SET is_default = 0')
            
            query = '''
                INSERT INTO ledgers (name, is_default, create_time)
                VALUES (?, ?, ?)
            '''
            self.cursor.execute(query, (name, 1 if default_var.get() else 0, datetime.now().isoformat()))
            
            if default_var.get():
                self.current_ledger_id = self.cursor.lastrowid
            
            self.conn.commit()
            dialog.destroy()
            messagebox.showinfo("成功", "账本创建成功")
            self.refresh_settings()
            self.refresh_ledger_display()
        
        btn_frame = ttk.Frame(dialog)
        btn_frame.pack(pady=15)
        
        ttk.Button(btn_frame, text="取消", command=dialog.destroy).pack(side=tk.LEFT, padx=10)
        ttk.Button(btn_frame, text="创建", command=create_ledger).pack(side=tk.LEFT, padx=10)
        
    def export_data(self):
        file_path = filedialog.asksaveasfilename(
            title="导出数据",
            defaultextension=".json",
            filetypes=[("JSON文件", "*.json"), ("所有文件", "*.*")]
        )
        
        if not file_path:
            return
        
        try:
            data = {
                'ledgers': [],
                'categories': [],
                'records': [],
                'budgets': []
            }
            
            self.cursor.execute('SELECT * FROM ledgers')
            for row in self.cursor.fetchall():
                data['ledgers'].append({
                    'id': row[0],
                    'name': row[1],
                    'is_default': row[2],
                    'create_time': row[3]
                })
            
            self.cursor.execute('SELECT * FROM categories')
            for row in self.cursor.fetchall():
                data['categories'].append({
                    'id': row[0],
                    'name': row[1],
                    'type': row[2],
                    'icon': row[3]
                })
            
            self.cursor.execute('SELECT * FROM records')
            for row in self.cursor.fetchall():
                data['records'].append({
                    'id': row[0],
                    'ledger_id': row[1],
                    'type': row[2],
                    'category_id': row[3],
                    'amount': row[4],
                    'date': row[5],
                    'remark': row[6],
                    'create_time': row[7]
                })
            
            self.cursor.execute('SELECT * FROM budgets')
            for row in self.cursor.fetchall():
                data['budgets'].append({
                    'id': row[0],
                    'ledger_id': row[1],
                    'month': row[2],
                    'amount': row[3],
                    'create_time': row[4]
                })
            
            with open(file_path, 'w', encoding='utf-8') as f:
                json.dump(data, f, ensure_ascii=False, indent=2)
            
            messagebox.showinfo("成功", f"数据已导出到:\n{file_path}")
        except Exception as e:
            messagebox.showerror("错误", f"导出失败: {str(e)}")
            
    def import_data(self):
        if not messagebox.askyesno("确认", "导入数据将覆盖现有数据，确定要继续吗？"):
            return
        
        file_path = filedialog.askopenfilename(
            title="导入数据",
            filetypes=[("JSON文件", "*.json"), ("所有文件", "*.*")]
        )
        
        if not file_path:
            return
        
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                data = json.load(f)
            
            self.cursor.execute('DELETE FROM records')
            self.cursor.execute('DELETE FROM budgets')
            self.cursor.execute('DELETE FROM categories')
            self.cursor.execute('DELETE FROM ledgers')
            
            for ledger in data.get('ledgers', []):
                self.cursor.execute('''
                    INSERT INTO ledgers (id, name, is_default, create_time)
                    VALUES (?, ?, ?, ?)
                ''', (ledger['id'], ledger['name'], ledger['is_default'], ledger['create_time']))
            
            for category in data.get('categories', []):
                self.cursor.execute('''
                    INSERT INTO categories (id, name, type, icon)
                    VALUES (?, ?, ?, ?)
                ''', (category['id'], category['name'], category['type'], category['icon']))
            
            for record in data.get('records', []):
                self.cursor.execute('''
                    INSERT INTO records (id, ledger_id, type, category_id, amount, date, remark, create_time)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ''', (
                    record['id'], record['ledger_id'], record['type'],
                    record['category_id'], record['amount'], record['date'],
                    record['remark'], record['create_time']
                ))
            
            for budget in data.get('budgets', []):
                self.cursor.execute('''
                    INSERT INTO budgets (id, ledger_id, month, amount, create_time)
                    VALUES (?, ?, ?, ?, ?)
                ''', (budget['id'], budget['ledger_id'], budget['month'], budget['amount'], budget['create_time']))
            
            self.conn.commit()
            
            self.cursor.execute('SELECT id FROM ledgers WHERE is_default = 1')
            default_ledger = self.cursor.fetchone()
            if default_ledger:
                self.current_ledger_id = default_ledger[0]
            
            messagebox.showinfo("成功", "数据导入成功")
            self.refresh_all()
        except Exception as e:
            messagebox.showerror("错误", f"导入失败: {str(e)}")

if __name__ == "__main__":
    root = tk.Tk()
    app = BookkeepApp(root)
    root.mainloop()
