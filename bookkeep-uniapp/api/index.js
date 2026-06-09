/**
 * API模块入口
 * 统一导出所有API模块
 */

// 导入各API模块
import recordApi from './record';
import booksApi from './books';
import userApi from './user';
import categoryApi from './category';

// 命名导出各模块（按需引入）
export { recordApi, booksApi, userApi, categoryApi };

// 默认导出所有API模块的集合
export default {
  recordApi,
  booksApi,
  userApi,
  categoryApi
};