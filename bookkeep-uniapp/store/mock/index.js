/**
 * 模拟数据入口文件
 * 统一导出所有模拟数据
 */

import recordMock from './records';
import ledgerMock from './ledgers';
import categoryMock from './categories';

export default {
  record: recordMock,
  ledger: ledgerMock,
  category: categoryMock
};

// 也可以单独导出各模块，方便按需引入
export { default as recordMock } from './records';
export { default as ledgerMock } from './ledgers';
export { default as categoryMock } from './categories';