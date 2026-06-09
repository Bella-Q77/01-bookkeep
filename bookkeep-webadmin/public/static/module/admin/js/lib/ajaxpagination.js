// 多实例分页插件********************************************************************
class AjaxPagination {
    constructor() {
        this.instances = new Map(); // 存储多个分页实例
        this.tableColumnUrl = "/admin/Index/getTableColumn"; // 获取表字段地址
        this.init();
    }

    init() {
        this.bindEvents();
        this.initAllTables();
    }

    // 初始化页面上所有表格
    initAllTables() {
        const self = this;
        $('.ajax-list-table').each(function () {
            const $table = $(this);
            const tableId = self.getTableId($table);
            if (tableId) {
                self.createInstance(tableId, $table);
                // 初始化表格列设置
                self.initTableFields(tableId, $table);
            }
        });
    }

    // 获取表格ID
    // 参数解释
    // $table：当前的表格元素（.ajax-list-table）
    // .closest()：jQuery方法，用于向上查找最近的匹配元素
    // '.page-list-container, .ibox-content'：查找的目标元素类名，可以是：
    // .page-list-container：分页列表容器
    // .ibox-content：内容容器
    getTableId($table) {

        const $container = $table.closest('.page-list-container, .ibox-content');

        let tableId = $container.attr('data-list-table');

        // 如果没有data-list-table属性，使用表格的data-url作为标识
        if (!tableId) {
            tableId = $table.attr('data-url') || '';
        }
        return tableId;
    }

    // 创建分页实例
    createInstance(tableId, $tableElement) {

        const $container = $tableElement.closest('.page-list-container, .ibox-content');

        // 查找相关的搜索表单
        let $searchForm = $();
        let searchData = '';

        // 多种方式查找搜索表单
        if ($container.length > 0) {
            // 方式1: 在同一容器内查找搜索表单
            $searchForm = $container.find('form.searchForm, form.form-inline').first();

            // 方式2: 如果没找到，查找同级或父级容器中的搜索表单
            if ($searchForm.length === 0) {
                $searchForm = $container.closest('.row, .container').find('form.searchForm, form.form-inline').first();
            }
        }

        // 方式3: 查找页面上任意的搜索表单（作为备选）
        if ($searchForm.length === 0) {
            $searchForm = $('form.searchForm, form.form-inline').first();
        }

        // 如果找到了搜索表单，序列化其数据
        if ($searchForm.length > 0) {
            searchData = $searchForm.serialize();
            log('找到搜索表单，序列化数据:'+searchData);
        } else {
            log('未找到相关搜索表单');
        }

        // 多种方式查找btn-field-set按钮
        let $fieldSetButton = $();
        let hasFieldSetButton = false;
        // 方式1: 在同一容器内查找btn-field-set按钮
        if ($container.length > 0) {
            $fieldSetButton = $container.find('.btn-field-set').first();

            // 方式2: 如果没找到，查找同级或父级容器中的btn-field-set按钮
            if ($fieldSetButton.length === 0) {
                $fieldSetButton = $container.closest('.row, .container').find('.btn-field-set').first();
            }
        }

        // 方式3: 查找页面上任意的btn-field-set按钮（作为备选）
        if ($fieldSetButton.length === 0) {
            $fieldSetButton = $('.btn-field-set').first();
        }

        // 判断是否存在btn-field-set按钮
        hasFieldSetButton = $fieldSetButton.length > 0;

        const instance = {
            tableId: tableId,
            $table: $tableElement,
            $container: $tableElement.closest('.page-list-container, .ibox-content'),
            orderField: '',
            orderDirection: '',
            pageSize: this.getDefaultPageSize($tableElement),
            pageNum: 1,
            searchData: searchData,  // 保存搜索数据
            $searchForm: $searchForm, // 保存搜索表单引用
            totalCount: 0,
            hasFieldSetButton: hasFieldSetButton, // 保存是否有字段设置按钮的信息
            $fieldSetButton: $fieldSetButton // 保存字段设置按钮引用
        };

        this.instances.set(tableId, instance);
        return instance;
    }

    // 获取默认每页条数
    getDefaultPageSize($tableElement) {
        const $pageSizeInput = $tableElement.find("tfoot td input[name='pageSize']");
        return $pageSizeInput.val() || '';// 获取每页条数,为空默认为后台指定的条数
    }

    // 获取实例
    getInstance(tableId) {
        return this.instances.get(tableId);
    }

    // 通过元素查找实例
    getInstanceByElement($element) {

        log('getInstanceByElement called with element:', $element);

        // 首先尝试通过分页按钮所在的容器查找tableID
        const $pageList = $element.closest('.page-list-container, .table-page-bar-pagination');
        if ($pageList.length > 0) {
            log('找到 => .page-list-container .table-page-bar-pagination容器:'+$pageList);
            const tableId = $pageList.attr('data-list-table');
            if (tableId) {
                log('从容器获取tableId:'+tableId);
                const instance = this.getInstance(tableId);
                if (instance) {
                    log('找到实例:'+instance);
                    return instance;
                }
            }
        }

        // 尝试通过.table-page-bar类查找
        const $tablePageBar = $element.closest('.table-page-bar');
        if ($tablePageBar.length > 0) {
            log('找到 => .table-page-bar:'+$tablePageBar);
            const $parentContainer = $tablePageBar.closest('.ibox-content');
            if ($parentContainer.length > 0) {
                log('找到.ibox-content容器:', $parentContainer);
                const $tableInContainer = $parentContainer.find('.ajax-list-table').first();
                if ($tableInContainer.length > 0) {
                    log('找到.ajax-list-table:'+$tableInContainer);
                    const tableId = this.getTableId($tableInContainer);
                    log('获取到tableId:'+ tableId);
                    return this.getInstance(tableId);
                }
            }
        }

        // 在未设置页page-list-container时 通过搜索表单查找实例，兼容老版本的
        const $searchForm = $element.closest('form');
        if ($searchForm.length > 0) {
            log('找到搜索表单=>'+$searchForm);
            const $parentContainer = $searchForm.closest('.ibox-content');
            if ($parentContainer.length > 0) {
                log('找到表单的 => .ibox-content容器 => ');
                console.log($parentContainer);
                const $tableInContainer = $parentContainer.find('.ajax-list-table').first();
                if ($tableInContainer.length > 0) {
                    log('找到.ajax-list-table:'+ $tableInContainer);
                    const tableId = this.getTableId($tableInContainer);
                    log('获取到tableId:'+tableId);
                    return this.getInstance(tableId);
                }
            }
        }

        // 通过.ajax-list-table直接查找
        const $table = $element.closest('.ajax-list-table');
        if ($table.length > 0) {
            log('找到.ajax-list-table:'+$table);
            const tableId = this.getTableId($table);
            log('获取到tableId:'+tableId);
            return this.getInstance(tableId);
        }

        // 5. 通过按钮查找关联的表格（针对.btn-field-set）
        if ($element.hasClass('btn-field-set')) {
            const $container = $element.closest('.ibox-content');
            if ($container.length > 0) {
                const $tableInContainer = $container.find('.ajax-list-table').first();
                if ($tableInContainer.length > 0) {
                    const tableId = this.getTableId($tableInContainer);
                    const instance = this.getInstance(tableId);
                    if (instance) {
                        log('通过按钮容器找到实例，tableId:'+ tableId);
                        return instance;
                    }
                    // 如果实例不存在，创建一个
                    if (tableId) {
                        const newInstance = this.createInstance(tableId, $tableInContainer);
                        log('创建新实例，tableId:'+tableId);
                        return newInstance;
                    }
                }
            }
        }

        // 最后尝试通过容器查找
        const $container = $element.closest('.ibox-content');
        if ($container.length > 0) {
            log('找到.ibox-content容器:'+$container);
            // 查找容器内的.ajax-list-table
            const $tableInContainer = $container.find('.ajax-list-table').first();
            if ($tableInContainer.length > 0) {
                log('找到.ajax-list-table:'+ $tableInContainer);
                const tableId = this.getTableId($tableInContainer);
                log('获取到tableId:'+tableId);
                return this.getInstance(tableId);
            }
        }

        // 如果以上方法都找不到实例，且只有一个分页实例，则使用该实例
        if (this.instances.size === 1) {
            log('未找到特定实例，但只有一个分页实例，使用该实例');
            const firstInstance = this.instances.values().next().value;
            if (firstInstance) {
                log('使用唯一实例:'+firstInstance);
                return firstInstance;
            }
        }

        log('未找到分页实例');
        return null;
    }


    // 绑定全局事件
    bindEvents() {
        const self = this;

        // 数据排序、样式操作
        // 在绑定事件前先解绑，避免重复绑定
        $("body").off("click", ".ajax-list-table .sort-filed");
        $("body").on("click", ".ajax-list-table .sort-filed", function () {
            const instance = self.getInstanceByElement($(this));
            if (!instance) return;

            const orderField = $(this).attr('orderField');
            let orderDirection = 'asc';

            $(this).toggleClass(function () {
                if ($(this).hasClass('asc')) {
                    $(this).removeClass('asc');
                    orderDirection = 'desc';
                } else {
                    $(this).removeClass('desc');
                    orderDirection = 'asc';
                }
                instance.orderField = orderField;
                instance.orderDirection = orderDirection;
                self.turnPage(1, instance.tableId);
                return orderDirection;
            });
        });

        // 查询数据，刷新
        $("body").on("click", '.ajaxSearchForm', function () {
            const $this = $(this);
            const $searchForm = $this.closest("form");
            const instance = self.getInstanceByElement($this);

            if (!instance) {
                log('未找到分页实例，无法执行搜索');
                return;
            }
            // 是否重置搜索条件
            if ($this.hasClass('resetForm')) {
                $searchForm[0].reset();
            }
            instance.searchData = $searchForm.serialize();
            instance.$searchForm = $searchForm; // 更新实例中的表单引用
            log('搜索数据:'+instance.searchData);
            self.turnPage(1, instance.tableId);
        });

        // 查询条件下拉时搜索
        $("body").on("change", ".searchForm select", function () {
            const $searchForm = $(this).closest("form");
            const instance = self.getInstanceByElement($(this));
            if (!instance) return;

            instance.searchData = $searchForm.serialize();
            instance.$searchForm = $searchForm; // 更新实例中的表单引用
            self.turnPage(1, instance.tableId);
        });

        // 添加对 radio 按钮的支持
        $("body").on("change", ".searchForm input[type=radio]", function () {
            console.log("radio change")
            const $searchForm = $(this).closest("form");
            const instance = self.getInstanceByElement($(this));
            if (!instance) return;

            instance.searchData = $searchForm.serialize();
            instance.$searchForm = $searchForm; // 更新实例中的表单引用
            self.turnPage(1, instance.tableId);
        });

        // 设置分页每页条数及跳转页数
        $("body").on("change", ".tfootPageBar", function () {
            const $this = $(this);
            const instance = self.getInstanceByElement($this);
            if (!instance) return;

            // 区分是 pageSize 还是 pageNum 输入框
            if ($this.hasClass('pageSize')) {
                // 只更新每页条数，保持当前页码
                const newPageSize = $this.val() || instance.pageSize;
                instance.pageSize = newPageSize;
                console.log('更新每页条数为:', newPageSize);
                // 使用当前页码和新的每页条数重新加载数据
                self.turnPage(instance.pageNum, instance.tableId);
            } else if ($this.hasClass('pageNum')) {
                // 跳转到指定页码
                const pageNum = $this.val() || 1;
                console.log('跳转到页码:', pageNum);
                self.turnPage(pageNum, instance.tableId);
            }
        });

        // 点击页码
        $("body").on("click", ".tfootClickPageNum", function () {
            const instance = self.getInstanceByElement($(this));
            if (!instance) return;

            let pageNum = $(this).attr('data-id') || 1;
            if ($(this).hasClass('pageNum')) {
                const $input = $(this).closest('.page-list-container, .ibox-content')
                    .find(".tfootPageBar.pageNum");
                pageNum = $input.val() || 1;
            }

            self.turnPage(pageNum, instance.tableId);
        });

        // 添加这一段：列设置按钮点击事件
        $("body").on("click", ".btn-field-set", function () {
            const $this = $(this);
            const instance = self.getInstanceByElement($this);
            const tableId = instance.tableId;
            log('列设置按钮点=》tableId=》' + tableId);
            if (tableId) {
                self.showFieldSettingDialog(tableId);
            }
        });
    }

    // 初始化表格字段设置
    initTableFields(tableId, $table) {

        //log('initTableFields=>初始化表格字段设置=》tableId=》' + tableId);

        // 查找列设置按钮（不依赖data-id）
        const $container = $table.closest('.ibox-content');
        const $fieldSetBtn = $container.find('.btn-field-set').first();

        //log('找到列设置按钮数量:' + $fieldSetBtn.length);

        // 收集所有列字段信息
        const listAll = [];
        $table.find("thead tr th").each(function (e) {
            const f_name = $(this).find("span").html();
            if (f_name != null) {
                listAll[e] = f_name;
            }
        });

        //log('所有列字段:' + listAll);

        // 存所有字段（无论是否有按钮都存储）
        if (listAll.length > 0) {
            localStorage.setItem("listAll" + tableId, JSON.stringify(listAll));
        }

        // 未设置显示全部列时，初始化默认
        if (localStorage.getItem("listSave" + tableId) == null) {
            // 如果找到了按钮，则尝试从服务器获取设置
            if ($fieldSetBtn.length > 0 && listAll.length > 0) {
                const self = this;
                $.ajax({
                    url: this.tableColumnUrl,
                    type: "post",
                    dataType: "json",
                    data: {
                        "table": tableId
                    },
                    success: function (result) {
                        log(result);
                        if (result.data && result.data.length > 0) {
                            localStorage.setItem("listSave" + tableId, JSON.stringify(result.data));
                        } else {
                            localStorage.setItem("listSave" + tableId, JSON.stringify(listAll));
                        }
                        //log('初始化列设置完成，tableId:' + tableId);
                    },
                    error: function (xhr, status, error) {
                        // 如果ajax请求失败，使用默认列设置
                        if (listAll.length > 0) {
                            localStorage.setItem("listSave" + tableId, JSON.stringify(listAll));
                        }
                        //log('初始化列设置失败，使用默认设置，tableId:' + tableId);
                    }
                });
            } else {
                // 如果没有按钮或没有列数据，直接使用默认设置
                if (listAll.length > 0) {
                    localStorage.setItem("listSave" + tableId, JSON.stringify(listAll));
                    log('无按钮或列数据，直接使用默认设置，tableId:' + tableId);
                }
            }
        } else {
            //log('列设置已存在，tableId:' + tableId);
        }
    }


    // 获取分页数据及模板
    turnPage(pageNum, tableId) {
        const instance = this.getInstance(tableId);
        if (!instance) return;

        instance.pageNum = pageNum;

        const ajaxUrl = instance.$table.attr("data-url");
        if (!ajaxUrl) {
            layer.msg('请求地址不存在');
            return false;
        }

        log('请求地址' + ajaxUrl);

        // 构建请求参数
        let ajaxPostJsonData = instance.searchData || '';
        ajaxPostJsonData += "&pageNum=" + pageNum + "&pageSize=" + instance.pageSize;

        if (instance.orderField) {
            ajaxPostJsonData += "&orderField=" + instance.orderField + "&orderDirection=" + instance.orderDirection;
        }

        const self = this;
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: ajaxPostJsonData,
            dataType: 'json',
            beforeSend: function () {
                layer.msg('加载数据', {
                    time: 1000,
                    icon: 16,
                    shade: 0.01
                });
            },
            success: function (returnJsonData) {
                if (returnJsonData.code == 0) {
                    toast.error(returnJsonData.msg);
                    return;
                }

                // 移除原来的文档
                instance.$table.find("tbody").empty();

                instance.totalCount = returnJsonData.total || 0;
                instance.pageSize = returnJsonData.per_page || instance.pageSize;
                instance.pageNum = returnJsonData.current_page || pageNum;

                // 模板引擎使用
                const tableListTpl = self.getTemplateId(tableId);
                const tpl = baidu.template;
                const html = tpl(tableListTpl, returnJsonData);
                instance.$table.find("tbody").html(html);

                // 把表格列表数据保存localStorage
                localStorage.setItem('tableListTpl-' + tableId, JSON.stringify(returnJsonData));
            },
            complete: function () {

                // 1、添加分页按钮栏
                self.getPageBar(instance);

                // 2、判断表格是否设置显示列（通过检查实例中是否包含字段设置按钮来判断）
                if (instance.hasFieldSetButton) {
                    log('判断表格是否设置显示列=>');
                    self.initTableCell(instance);
                }

                // 3、绑定设置超出部分隐藏
                self.bindClass();

                // 4、判断是否有表格需要合并
                if (instance.$table.hasClass('merge-table-rowspan')) {
                    self.mergeTableRowspan(instance);
                    log('判断是否有表格需要合并');
                }

                // 5、设置数据区域高度，如果表格设置了sticky-table
                const stickyTable = instance.$table.parents('.sticky-table');
                if (stickyTable.hasClass('sticky-table')) {
                    self.setStickyTableHeight(stickyTable);

                    // 窗口大小改变的时候，重新设置高度
                    $(window).off('resize.stickyTable').on('resize.stickyTable', function () {
                        self.setStickyTableHeight(stickyTable);
                    });

                    // 滚动条滚动的时候，判断是否需要添加固定列第一列，二列，固定样式
                    stickyTable.off('scroll.stickyTable').on('scroll.stickyTable', function (e) {
                        const left = $(this).scrollLeft();
                        if (left > 10) {
                            $(this).addClass("scroll-left");
                        } else {
                            $(this).removeClass("scroll-left");
                        }
                    });
                }
            },
            error: function () {
                layer.msg('数据加载失败', {
                    icon: 5,
                    shade: 0.01
                });
            }
        });
    }

    // 获取模板ID
    getTemplateId(tableId) {
        // 如果tableId包含特殊字符或者路径，生成简化版本
        if (tableId.includes('/') || tableId.includes('.')) {
            return 'tableListTpl';
        }
        return tableId ? 'tableListTpl-' + tableId : 'tableListTpl';
    }

    // 设置stickyTable的高度
    setStickyTableHeight(stickyTable) {
        const $stickyTable = $(stickyTable);
        const distanceFromTop = $stickyTable.offset().top;
        const height = $(window).height();
        const centerHight = height - distanceFromTop - 60;

        $stickyTable.height(centerHight).css("overflow", "auto");
        $stickyTable.css("background", "#fff");

        const stickyTableWidth = $stickyTable.width();
        const sorttableWidth = $stickyTable.find('table.sorttable').width();

        if (stickyTableWidth < sorttableWidth) {
            $stickyTable.find('table.sorttable').addClass('fixed-last-td');
        } else {
            $stickyTable.find('table.sorttable').removeClass('fixed-last-td');
        }
    }

    // 获取分页条
    getPageBar(instance) {
        const pageNum = parseInt(instance.pageNum);
        const pageSize = parseInt(instance.pageSize);
        const totalCount = parseInt(instance.totalCount);
        const totalPage = Math.max(1, Math.ceil(totalCount / pageSize));

        const currentPage = Math.max(1, Math.min(pageNum, totalPage));

        let pageBar = "<div class='table-page-bar-pagination' data-list-table='" + instance.tableId + "'>";
        pageBar += "<div class=\"btn-group\"> <span class='btn btn-white'> 共 " + totalCount + "条 </span>";
        pageBar += "<span class='btn btn-white'> 每页 <input type='text' name='pageSize' class='tfootPageBar pageSize' style='width:50px;height:20px;border:solid #ccc 1px;' value='" + pageSize + "'> 条 </span>";
        pageBar += "<span class='btn btn-white tfootClickPageNum' data-id='1'><a>首页</a></span>";
        pageBar += "<span type=\"button\" class=\"btn btn-white tfootClickPageNum\" data-id='" + Math.max(1, currentPage - 1) + "'><a><< </a> </span>";


        // 显示的页码按钮(5个)
        let start = 1, end = 0;
        if (totalPage <= 5) {
            start = 1;
            end = totalPage;
        } else {
            if (currentPage - 2 <= 0) {
                start = 1;
                end = 5;
            } else {
                if (totalPage - currentPage < 2) {
                    start = totalPage - 4;
                    end = totalPage;
                } else {
                    start = currentPage - 2;
                    end = currentPage + 2;
                }
            }
        }

        for (let i = start; i <= end; i++) {
            if (i == currentPage) {
                pageBar += "<span class='btn btn-white tfootClickPageNum active' data-id='" + i + "'><a>" + i + "</a></span>";
            } else {
                pageBar += "<span class='btn btn-white tfootClickPageNum' data-id='" + i + "'><a>" + i + "</a></span>";
            }
        }

        pageBar += "<span class='btn btn-white tfootClickPageNum' data-id='" + Math.min(totalPage, currentPage + 1) + "'><a> >> </a></span>";
        pageBar += "<span class='btn btn-white tfootClickPageNum' data-id='" + totalPage + "'><a>尾页</a></span>";
        pageBar += "<span class='btn btn-white'> 跳 <input type='text' name='pageNum' class='tfootPageBar pageNum' style='width:50px;height:20px;border:solid #ccc 1px;'> 页 <a class='tfootClickPageNum'>GO</a></span>";
        pageBar += "</div></div>";

// 显示分页条 - 兼容多种情况
        let $pageBarContainer = null;

        // 首先尝试查找专门的分页栏表格
        $pageBarContainer = instance.$container.find(".table-page-bar tfoot td");

        // 如果没有找到，尝试在当前表格的 tfoot 中显示分页
        if ($pageBarContainer.length === 0) {
            $pageBarContainer = instance.$table.find("tfoot td");
        }

        // 如果还是没有找到，尝试在当前表格后面查找分页栏
        if ($pageBarContainer.length === 0) {
            $pageBarContainer = instance.$table.next(".table-page-bar").find("tfoot td");
        }

        if ($pageBarContainer.length > 0) {
            if (totalCount == 0) {
                $pageBarContainer.html('噢噢噢，暂时没有查询到数据~~');
            } else {
                $pageBarContainer.html(pageBar);
            }
        } else {
            console.warn('未找到分页栏容器，无法显示分页控件');
        }
    }

    // 初始化隐藏表的列
    initTableCell(instance) {
        const tableId = instance.tableId;
        const listSave = JSON.parse(localStorage.getItem("listSave" + tableId) || '[]');
        let colspan = 0;

        instance.$table.find("thead tr th").each(function (index) {
            const f_name = $(this).find("span").html();
            const cell = index + 1;
            // 确保比较的是有效字符串
            let item = -1;
            if (f_name && typeof f_name === 'string') {
                // 过滤掉listSave中的null值后再查找
                const validListSave = listSave.filter(val => val !== null && val !== undefined);
                item = $.inArray(f_name, validListSave);
            }
            //log('字段=>'+f_name+', 查找结果=>'+item);

            if (item >= 0 || typeof (f_name) == 'undefined') {
                instance.$table.find("tbody tr td:nth-child(" + cell + ")").show();
                instance.$table.find("thead tr th:nth-child(" + cell + ")").show();
                colspan++;
            } else {
                instance.$table.find("tbody tr td:nth-child(" + cell + ")").hide();
                instance.$table.find("thead tr th:nth-child(" + cell + ")").hide();
            }
        });

        instance.$table.find("tfoot tr td").attr('colspan', colspan);
        this.bindClass();
    }

    // 表格显示列设置
    showFieldSettingDialog(tableId) {
        const listAll = JSON.parse(localStorage.getItem("listAll" + tableId) || '[]');
        const listSave = JSON.parse(localStorage.getItem("listSave" + tableId) || '[]');

        let listHtml = "<div class='ibox-content row list-all-field' style='width:80%;' data-table-id='" + tableId + "'>";
        for (let i = 0; i < listAll.length; i++) {
            if (typeof (listAll[i]) != "undefined" && listAll[i] != null) {
                const index = $.inArray(listAll[i], listSave);
                const chk = index >= 0 ? "checked" : "";
                listHtml += "<div class='col-sm-4'><input type='checkbox' name='listFieldCheckbox' value='" + listAll[i] + "' " + chk + "> " + listAll[i] + "</div>";
            }
        }
        listHtml += "</div>";

        const self = this;
        layer.open({
            type: 1,
            title: "列表字段设置",
            scrollbar: false,
            skin: 'layui-layer-demo',
            area: ['80%', '60%'],
            content: listHtml,
            btn: ['保存', '取消', '默认保存'],
            yes: function (index, layero) {
                self.saveFieldSettings(index, tableId);
            },
            btn2: function (index, layero) {
                layer.close(index);
            },
            btn3: function (index, layero) {
                self.saveFieldSettings(index, tableId, true);
            }
        });
    }

    // 保存字段设置
    saveFieldSettings(index, tableId, saveToServer = false) {
        const $container = $(".list-all-field[data-table-id='" + tableId + "']");
        const listSave = [];

        $container.find("input[name='listFieldCheckbox']:checked").each(function (e) {
            if (true == $(this).prop("checked")) {
                const value = $(this).prop('value');
                listSave.push(value);
            }
        });

        localStorage.setItem("listSave" + tableId, JSON.stringify(listSave));

        log('saveFieldSettings=>保存字段设置=>tableId=>' + tableId)

        log(JSON.stringify(listSave))

        // 刷新对应表格
        this.turnPage(1, tableId);

        if (saveToServer) {
            $.ajax({
                url: this.tableColumnUrl,
                type: "post",
                dataType: "json",
                data: {
                    "table": tableId,
                    "column": listSave
                },
                success: function (result) {
                    layer.msg(result.msg, {icon: 1, time: 500, shade: [0.5, '#000', true]}, function () {
                        layer.close(index);
                    });
                }
            });
        }

        layer.msg('操作成功', {icon: 1, time: 500, shade: [0.5, '#000', true]}, function () {
            layer.close(index);
        });
    }

    // 绑定鼠标事件
    bindClass() {
        $(".MALL").hide();

        $(".MHover").off('mouseover.pagination').on('mouseover.pagination', function (e) {
            const clientWidth = document.body.clientWidth;
            const divWidth = clientWidth - e.pageX - 45;
            $(this).next(".MALL").css({
                "color": "#ffffff",
                "z-index": "1000",
                "width": divWidth + "px",
                "padding": "1rem",
                "line-height": "1.5rem",
                "position": "absolute",
                "opacity": "1",
                "background-color": "#3595CC",
                "top": e.pageY - 50,
                "left": e.pageX
            }).show();
        });

        $(".MHover").off('mousemove.pagination').on('mousemove.pagination', function (e) {
            const clientWidth = document.body.clientWidth;
            const divWidth = clientWidth - e.pageX - 45;
            $(this).next(".MALL").css({
                "color": "#ffffff",
                "z-index": "1000",
                "width": divWidth + "px",
                "padding": "1rem",
                "line-height": "1.5rem",
                "position": "absolute",
                "opacity": "1",
                "background-color": "#3595CC",
                "top": e.pageY - 50,
                "left": e.pageX
            });
        });

        $(".MHover").off('mouseout.pagination').on('mouseout.pagination', function () {
            $(this).next(".MALL").hide();
        });
    }

    // 合并表格行
    mergeTableRowspan(instance) {
        // 实现表格行合并逻辑
        const $table = instance.$table;
        // 遍历表头，查找需要合并的列
        $table.find("thead th").each(function(index) {
            log($(this).attr('data-merge'));
            // 检查是否有 data-merge="true" 属性
            if ($(this).attr('data-merge')=="true") {
                log('mergeTableRowspan=>需要合并的列=>index=>' + index)
                // 对该列执行合并操作
                $table.rowspanDataId(index);
            }
        });
    }

    // 表格长文字的过滤
    filterTd(v) {
        if (this.isEmpty(v)) {
            return '无';
        } else {
            return '<div class="MHover">' + v + '</div>' +
                '<div class="MALL">' + v + '</div>';
        }
    }

    // 判断字符是否为空的方法
    isEmpty(obj) {
        return (typeof obj == "undefined" || obj == null || obj == "");
    }
}


// 保持向后兼容的全局函数
function turnPage(pageNum, tableId = '') {
    if (window.ajaxPagination) {
        // 如果没有指定tableId，且只有一个实例，使用该实例
        if (!tableId && window.ajaxPagination.instances.size === 1) {
            const firstInstance = window.ajaxPagination.instances.values().next().value;
            if (firstInstance) {
                tableId = firstInstance.tableId;
            }
        }

        // 如果仍然没有tableId，尝试使用第一个表格的data-url
        if (!tableId) {
            const $firstTable = $('.ajax-list-table').first();
            if ($firstTable.length > 0) {
                tableId = $firstTable.attr('data-url') || '';
            }
        }

        window.ajaxPagination.turnPage(pageNum, tableId);
    }
}

function getPageBar(ajaxListTableId, pageNum, pageSize, totalCount) {
    if (window.ajaxPagination) {
        const instance = window.ajaxPagination.getInstance(ajaxListTableId);
        if (instance) {
            instance.pageNum = pageNum;
            instance.pageSize = pageSize;
            instance.totalCount = totalCount;
            window.ajaxPagination.getPageBar(instance);
        }
    }
}

function initTableCell() {
    if (window.ajaxPagination) {
        // 遍历所有实例，初始化表格列
        for (let instance of window.ajaxPagination.instances.values()) {
            window.ajaxPagination.initTableCell(instance);
        }
    }
}

function bindClass() {
    if (window.ajaxPagination) {
        window.ajaxPagination.bindClass();
    }
}

function setStickyTableHeight(stickyTable) {
    if (window.ajaxPagination) {
        window.ajaxPagination.setStickyTableHeight(stickyTable);
    }
}

// 向后兼容的全局变量
if (typeof pageNum === 'undefined') {
    var pageNum = 1;
}

if (typeof pageSize === 'undefined') {
    var pageSize = '';
}

if (typeof orderField === 'undefined') {
    var orderField = '';
}

if (typeof orderDirection === 'undefined') {
    var orderDirection = '';
}

if (typeof ajaxSearchFormData === 'undefined') {
    var ajaxSearchFormData = '';
}
// 初始化分页插件
$(document).ready(function () {
    window.ajaxPagination = new AjaxPagination();
    console.log('初始化分页插件，实例数量:', window.ajaxPagination.instances.size);

    // 确保向后兼容的全局变量已定义
    if (typeof pageNum === 'undefined') {
        window.pageNum = 1;
    }

    if (typeof pageSize === 'undefined') {
        window.pageSize = '';
    }

    if (typeof orderField === 'undefined') {
        window.orderField = '';
    }

    if (typeof orderDirection === 'undefined') {
        window.orderDirection = '';
    }

    if (typeof ajaxSearchFormData === 'undefined') {
        window.ajaxSearchFormData = '';
    }

    // 页面加载完成后初始化第一页数据
    setTimeout(function () {
        $('.ajax-list-table').each(function () {
            const $table = $(this);
            const tableId = window.ajaxPagination.getTableId($table);
            // console.log('初始化表格数据，tableId:', tableId, 'table:', $table);
            if (tableId) {
                window.ajaxPagination.turnPage(1, tableId);
            } else {
                // 如果无法获取tableId，尝试使用表格的data-url作为tableId
                const dataUrl = $table.attr('data-url');
                if (dataUrl) {
                    console.log('使用data-url作为tableId:', dataUrl);
                    window.ajaxPagination.turnPage(1, dataUrl);
                }
            }
        });
    }, 100);
});