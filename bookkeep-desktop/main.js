const { app, BrowserWindow, ipcMain, dialog } = require('electron');
const path = require('path');
const fs = require('fs');

let mainWindow;

const dataPath = path.join(app.getPath('userData'), 'bookkeep-data.json');

function loadData() {
  try {
    if (fs.existsSync(dataPath)) {
      const data = fs.readFileSync(dataPath, 'utf-8');
      return JSON.parse(data);
    }
  } catch (error) {
    console.error('加载数据失败:', error);
  }
  return getDefaultData();
}

function saveData(data) {
  try {
    fs.writeFileSync(dataPath, JSON.stringify(data, null, 2));
    return true;
  } catch (error) {
    console.error('保存数据失败:', error);
    return false;
  }
}

function getDefaultData() {
  return {
    ledgers: [
      { id: 1, name: '个人账本', isDefault: true, createTime: new Date().toISOString() }
    ],
    categories: {
      income: [
        { id: 'income-1', name: '工资', icon: 'wallet' },
        { id: 'income-2', name: '奖金', icon: 'gift' },
        { id: 'income-3', name: '投资', icon: 'upload' },
        { id: 'income-4', name: '兼职', icon: 'staff' },
        { id: 'income-5', name: '红包', icon: 'notification' },
        { id: 'income-6', name: '其他', icon: 'more' }
      ],
      expense: [
        { id: 'expense-1', name: '餐饮', icon: 'shop' },
        { id: 'expense-2', name: '交通', icon: 'car' },
        { id: 'expense-3', name: '购物', icon: 'cart' },
        { id: 'expense-4', name: '娱乐', icon: 'star' },
        { id: 'expense-5', name: '居家', icon: 'home' },
        { id: 'expense-6', name: '医疗', icon: 'help' },
        { id: 'expense-7', name: '教育', icon: 'book' },
        { id: 'expense-8', name: '旅行', icon: 'paperplane' },
        { id: 'expense-9', name: '其他', icon: 'more' }
      ]
    },
    records: [],
    budgets: []
  };
}

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1200,
    height: 800,
    minWidth: 900,
    minHeight: 600,
    webPreferences: {
      nodeIntegration: false,
      contextIsolation: true,
      preload: path.join(__dirname, 'preload.js')
    },
    icon: path.join(__dirname, 'assets', 'icon.png')
  });

  mainWindow.loadFile(path.join(__dirname, 'renderer', 'index.html'));

  mainWindow.on('closed', function () {
    mainWindow = null;
  });
}

app.whenReady().then(createWindow);

app.on('window-all-closed', function () {
  if (process.platform !== 'darwin') app.quit();
});

app.on('activate', function () {
  if (mainWindow === null) createWindow();
});

ipcMain.handle('load-data', () => {
  return loadData();
});

ipcMain.handle('save-data', (event, data) => {
  return saveData(data);
});

ipcMain.handle('export-data', async () => {
  const result = await dialog.showSaveDialog(mainWindow, {
    title: '导出数据',
    defaultPath: path.join(app.getPath('documents'), `bookkeep-export-${Date.now()}.json`),
    filters: [
      { name: 'JSON文件', extensions: ['json'] },
      { name: '所有文件', extensions: ['*'] }
    ]
  });

  if (!result.canceled && result.filePath) {
    try {
      const data = loadData();
      fs.writeFileSync(result.filePath, JSON.stringify(data, null, 2));
      return { success: true, path: result.filePath };
    } catch (error) {
      return { success: false, error: error.message };
    }
  }
  return { success: false, canceled: true };
});

ipcMain.handle('import-data', async () => {
  const result = await dialog.showOpenDialog(mainWindow, {
    title: '导入数据',
    defaultPath: app.getPath('documents'),
    filters: [
      { name: 'JSON文件', extensions: ['json'] },
      { name: '所有文件', extensions: ['*'] }
    ],
    properties: ['openFile']
  });

  if (!result.canceled && result.filePaths.length > 0) {
    try {
      const data = JSON.parse(fs.readFileSync(result.filePaths[0], 'utf-8'));
      if (saveData(data)) {
        return { success: true, data };
      }
      return { success: false, error: '保存数据失败' };
    } catch (error) {
      return { success: false, error: error.message };
    }
  }
  return { success: false, canceled: true };
});
