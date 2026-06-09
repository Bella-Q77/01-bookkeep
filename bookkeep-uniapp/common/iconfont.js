/**
 * iconfont图标类名配置文件
 * 提供统一的图标类名常量，方便在项目中调用
 */

export const ICONFONT_CLASSES = {
  // 转账相关
  TRANSFER: 'icon-zhuanzhang',
  
  // 图片相关
  IMAGE: 'icon-tupian',
  
  // 水果相关
  FRUITS: 'icon-sop_fruits',
  FRUIT_COPY: 'icon-shuiguo',
  
  // 删除相关
  DELETE: 'icon-delete',
  DELETE2: 'icon-delete2',
  
  // 蔬菜相关
  VEGETABLE: 'icon-huluobu',
  
  // 技术相关
  USB_CABLE: 'icon-shujuxian',
  
  // 键盘操作
  KEYBOARD_HIDE: 'icon-shouqijianpan',
  
  // 生活用品
  TISSUE: 'icon-shijin',
  
  // 基础操作
  PLUS: 'icon-plus',
  CLOSE: 'icon-close',
  
  // 数据状态
  NO_DATA: 'icon-wushuju',
  
  // 方向箭头
  ARROW_RIGHT: 'icon-arrow-right',
  ARROW_LEFT: 'icon-arrow-left',
  ARROW_DOWN: 'icon-arrow-down',
  
  // 日期时间
  CALENDAR: 'icon-riliriqi2',
  
  // 分类相关
  ALL_CATEGORIES: 'icon-quanbufenlei',
  
  // 提示信息
  TIP: 'icon-icon',
  
  // 设置相关
  SETTING: 'icon-setting',
  PERSONALIZATION: 'icon-owner_set',
  
  // 货币相关
  RMB: 'icon-rmb',
  
  // 购物相关
  CART: 'icon-cart',
  
  // 社交活动
  PARTY: 'icon-juhui',
  
  // 笔记相关
  NOTE: 'icon-biji-copy',
  
  // 交通工具
  AIRPLANE: 'icon-feiji',
  PHONE: 'icon-dianhua',
  
  // 居家生活
  HOME: 'icon-jujia1',
  
  // 金融相关
  RED_PACKET: 'icon-hongbao',
  
  // 工作相关
  PART_TIME_JOB: 'icon-jianzhi',
  
  // 交通出行
  TRANSPORTATION: 'icon-jiaotong',
  
  // 食品相关
  SNACK: 'icon-lingshi',
  
  // 娱乐休闲
  ENTERTAINMENT: 'icon-yule',
  
  // 住房相关
  HOUSING: 'icon-zhufang',
  
  // 公益慈善
  CHARITY: 'icon-gongyi01',
  
  // 图表统计
  PIE_CHART: 'icon-tubiao-bingtu',
  
  // 餐饮美食
  DINING: 'icon-canyin',
  
  // 办公用品
  OFFICE_SUPPLIES: 'icon-bangongyongpin',
  
  // 工资认证
  SALARY_CERTIFICATION: 'icon-gongziqiarenzheng',
  
  // 服装服饰
  T_SHIRT: 'icon-Txu',
  
  // 庆典聚会
  CELEBRATION: 'icon-qingdianhejuhui-',
  
  // 化妆用品
  MAKEUP_MIRROR: 'icon-huazhuangjing',
  
  // 账单管理
  BILL: 'icon-zhangdan',
  
  // 医疗健康
  MEDICAL: 'icon-yiliao',
  
  // 快递物流
  EXPRESS: 'icon-kuaidi1',
  
  // 娱乐宝
  ENTERTAINMENT_TREASURE: 'icon-yulebao',
  
  // 理财投资
  FINANCE: 'icon-licai',
  
  // 汽车交通
  CAR: 'icon-qiche',
  
  // 居家生活
  HOME_LIFE: 'icon-jujiashenghuo',
  
  // 维修服务
  REPAIR: 'icon-weixiu',
  
  // 学习教育
  STUDY: 'icon-xuexi',
  
  // 图表模板
  CHART_TEMPLATE: 'icon-tubiaozhizuomoban-70',
  
  // 运动健身
  SPORTS: 'icon-sport',
  
  // 账簿管理
  ACCOUNT_BOOK: 'icon-zhangbenzhangdanjizhangzhangbu'
};

/**
 * 获取图标类名
 * @param {string} key - 图标键名
 * @returns {string} 图标类名
 */
export function getIconClass(key) {
  return ICONFONT_CLASSES[key] || '';
}

/**
 * 获取所有图标类名
 * @returns {Object} 所有图标类名对象
 */
export function getAllIconClasses() {
  return { ...ICONFONT_CLASSES };
}

/**
 * 根据分类名称获取对应的图标类名
 * @param {string} categoryName - 分类名称
 * @returns {string} 图标类名
 */
export function getIconByCategoryName(categoryName) {
  if (!categoryName) return ICONFONT_CLASSES.ALL_CATEGORIES;
  
  const categoryIconMap = {
    // 收入分类
    '工资': ICONFONT_CLASSES.SALARY_CERTIFICATION,
    '奖金': ICONFONT_CLASSES.RED_PACKET,
    '兼职': ICONFONT_CLASSES.PART_TIME_JOB,
    '投资': ICONFONT_CLASSES.FINANCE,
    '理财': ICONFONT_CLASSES.FINANCE,
    '其他收入': ICONFONT_CLASSES.PLUS,
    
    // 支出分类
    '餐饮': ICONFONT_CLASSES.DINING,
    '交通': ICONFONT_CLASSES.TRANSPORTATION,
    '购物': ICONFONT_CLASSES.CART,
    '娱乐': ICONFONT_CLASSES.ENTERTAINMENT,
    '居家': ICONFONT_CLASSES.HOME,
    '医疗': ICONFONT_CLASSES.MEDICAL,
    '教育': ICONFONT_CLASSES.STUDY,
    '其他支出': ICONFONT_CLASSES.MINUS,
    
    // 通用分类
    '全部': ICONFONT_CLASSES.ALL_CATEGORIES,
    '转账': ICONFONT_CLASSES.TRANSFER,
    '水果': ICONFONT_CLASSES.FRUITS,
    '蔬菜': ICONFONT_CLASSES.VEGETABLE,
    '零食': ICONFONT_CLASSES.SNACK,
    '住房': ICONFONT_CLASSES.HOUSING,
    '汽车': ICONFONT_CLASSES.CAR,
    '快递': ICONFONT_CLASSES.EXPRESS,
    '运动': ICONFONT_CLASSES.SPORTS,
    '维修': ICONFONT_CLASSES.REPAIR,
    '办公': ICONFONT_CLASSES.OFFICE_SUPPLIES,
    '服装': ICONFONT_CLASSES.T_SHIRT,
    '化妆': ICONFONT_CLASSES.MAKEUP_MIRROR,
    '聚会': ICONFONT_CLASSES.PARTY,
    '公益': ICONFONT_CLASSES.CHARITY,
    '账单': ICONFONT_CLASSES.BILL,
    '账簿': ICONFONT_CLASSES.ACCOUNT_BOOK
  };
  
  // 查找匹配的分类名称（支持模糊匹配）
  for (const [key, iconClass] of Object.entries(categoryIconMap)) {
    if (categoryName.includes(key) || key.includes(categoryName)) {
      return iconClass;
    }
  }
  
  // 默认返回
  return categoryName.includes('收入') ? ICONFONT_CLASSES.SALARY_CERTIFICATION : 
         categoryName.includes('支出') ? ICONFONT_CLASSES.MINUS : 
         ICONFONT_CLASSES.ALL_CATEGORIES;
}

export default ICONFONT_CLASSES;