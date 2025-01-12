// 获取 JSON 列表
    function fetchData() {
        $.ajax({
            type: 'POST',
            url: '/action/action_setting.php',
            data: { action: 'getAll' },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    // 清空表格
                    $('#jsonTable tbody').empty();
                    // 插入新数据
                    data.data.forEach(item => {
                        const row = `<tr>
                                        <td>${item.id}</td>
                                        <td>${item.name}</td>
                                        <td>${item.value}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" onclick="editItem(${item.id})">编辑</button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteItem(${item.id})">删除</button>
                                        </td>
                                      </tr>`;
                        $('#jsonTable tbody').append(row);
                    });
                } else {
                    alert(data.message);
                }
            }
        });
    }

    // 打开新增模态窗口
    function openAddModal() {
        // 清空表单
        $('#modalItemName').val('');
        $('#modalItemValue').val('');
        $('#modalSubmitBtn').data('action', 'add'); // 设置操作为新增
        $('#editModalLabel').text('新增项');
        // 打开模态框
        $('#editModal').modal('show');
    }

    // 编辑操作
    function editItem(id) {
        $.ajax({
            type: 'POST',
            url: '/action/action_setting.php',
            data: { action: 'get', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    const item = data.item;
                    $('#modalItemName').val(item.name);
                    $('#modalItemValue').val(item.value);
                    $('#modalSubmitBtn').data('id', item.id); // 存储 ID 以供提交使用
                    $('#modalSubmitBtn').data('action', 'update'); // 设置操作为更新
                    $('#editModalLabel').text('编辑项');
                    // 打开模态框
                    $('#editModal').modal('show');
                } else {
                    alert(data.message);
                }
            }
        });
    }

    // 提交表单：新增或更新项
    function submitItem() {
        const action = $('#modalSubmitBtn').data('action');
        const id = $('#modalSubmitBtn').data('id');
        const name = $('#modalItemName').val();
        const value = $('#modalItemValue').val();

        $.ajax({
            type: 'POST',
            url: '/action/action_setting.php',
            data: {
                action: action,
                id: id,
                name: name,
                value: value
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    alert(data.message);
                    fetchData(); // 刷新列表
                    $('#editModal').modal('hide'); // 关闭模态框
                } else {
                    alert(data.message);
                }
            }
        });
    }

    // 删除操作
    function deleteItem(id) {
        if (!confirm('确定要删除该项吗？')) return;

        $.ajax({
            type: 'POST',
            url: '/action/action_setting.php',
            data: { action: 'delete', id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    alert(data.message);
                    fetchData(); // 刷新列表
                } else {
                    alert(data.message);
                }
            }
        });
    }

    // 页面加载时获取数据
    $(document).ready(function() {
        fetchData(); // 获取并展示数据
    });