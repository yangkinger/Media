$(document).ready(function () {
    // 启动频道的AJAX请求
    $('.startChannelButton').on('click', function () {
        var channelId = $(this).data('channel-id');
        var playurl = $(this).data('channel-playurl');
        var rtmpurl = $(this).data('channel-rtmpurl');
        var ffmpeg = $(this).data('ffmpeg');
        var ffmpeg_name = $(this).data('ffmpeg-name');
        
        // $('.startChannelButton').attr('disabled', true).text('启动中...');

        $.ajax({
            url: '/action/start_channel.php',
            method: 'POST',
            data: { channel_id: channelId ,playurl:playurl,rtmpurl:rtmpurl,ffmpeg:ffmpeg,ffmpeg_name:ffmpeg_name},
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    // alert(response.message || '频道启动成功！');
                } else {
                    // alert(response.message || '启动失败！');
                }
            },
            error: function (xhr, status, error) {
                alert('请求失败：' + error);
                
            },
            complete: function () {
                // $('.startChannelButton').attr('disabled', true).text('启动');
                // $('.stopChannelButton').attr('disabled', false).text('停止');
                location.reload();
            }
        });
    });

    // 停止切片的AJAX请求
    $('.stopChannelButton').on('click', function () {
        var channelId = $(this).data('channel-id');
        // $(this).attr('disabled', true).text('停止中...');
        $.ajax({
            url: '/action/stop_channel.php',
            method: 'POST',
            data: { channel_id: channelId },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    // alert(response.message || '频道已停止！');
                    
                } else {
                    // alert(response.message || '停止失败！');
                }
            },
            error: function (xhr, status, error) {
                alert('请求失败：' + error);
            },
            complete: function () {
                // $('.startChannelButton').attr('disabled', false).text('启动');
                // $('.stopChannelButton').attr('disabled', true).text('停止');
                location.reload(); 
            }
        });
    });
    
    
        // 删除频道的AJAX请求
    $('.deleteChannelButton').on('click', function () {
        var channelId = $(this).data('channel-id');

        if (confirm('确定要删除该频道吗？')) {
            $.ajax({
                url: '/action/delete_channel.php',
                method: 'POST',
                data: { channel_id: channelId },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        alert('频道已删除！');
                        location.reload(); // 刷新页面，删除操作生效
                    } else {
                        alert(response.message || '删除失败！');
                    }
                },
                error: function (xhr, status, error) {
                    alert('请求失败：' + error);
                }
            });
        }
    });
    

                // 打开编辑模态窗口
    $('.editButton').on('click', function () {
        const channelId = $(this).data('id');
        const channelName = $(this).data('name');
        const channelUrl = $(this).data('url');
        const rtmpurl = $(this).data('rtmpurl');
        const ffmpeg = $(this).data('ffmpeg');

        $('#editChannelId').val(channelId);
        $('#editChannelName').val(channelName);
        $('#editChannelUrl').val(channelUrl);
        $('#editrtmpUrl').val(rtmpurl);
        $('#editmodalItemSelect').val(rtmpurl);
        
        $.ajax({
            url: '/action/action_setting.php',
            type: 'POST',
            data: { action: 'getAll' },
            success: function (response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    $('#editmodalItemSelect').empty();
                    // 填充下拉框
                    data.data.forEach(option => {
                        // $('#editmodalItemSelect').append(`<option value="${option.id}">${option.name}</option>`);
                        isSelected = false; // 根据实际条件判断
                        if(option.name == ffmpeg){
                            isSelected = true
                        }
                        const optionTag = `<option value="${option.id}" ${isSelected ? 'selected' : ''}>${option.name}</option>`;
                        $('#editmodalItemSelect').append(optionTag);
            
                    });
                } else {
                    alert(data.message);
                }
            },
            error: function (xhr, status, error) {
                alert('请求失败：' + error);
            }
        });
        
        $('#editModal').show();
    });

    // 关闭编辑模态窗口
    $('#closeEditModalBtn').on('click', function () {
        $('#editModal').hide();
    });

    // 提交编辑表单
    $('#editChannelForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();
        $.post('/action/edit_channel.php', formData, function (response) {
            alert(response.message);
            if (response.status === 'success') {
                location.reload();
            }
        }, 'json');
    });
    

    $('#addChannelForm').on('submit', function (e) {
        e.preventDefault();
    
        const formData = $(this).serialize();
        $.post('/action/add_channel.php', formData, function (response) {
            alert(response.message);
            if (response.status === 'success') {
                location.reload();
            }
        }, 'json');
    });
    
    
    
    
    
    $('#hlsconfig').on('click', function () {
        $('#hlsModal').show();
    });
    
    
    $('#flashpage').on('click', function () {
        location.reload(); // 刷新页面，删除操作生效
    });



                // 获取模态窗口元素
    var modal = document.getElementById("myModal");
    var openModalBtn = document.getElementById("openModalBtn");
    var closeModalBtn = document.getElementById("closeModalBtn");

    // 打开模态窗口
    openModalBtn.onclick = function() {
        $.ajax({
            url: '/action/action_setting.php',
            type: 'POST',
            data: { action: 'getAll' },
            success: function (response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    $('#modalItemSelect').empty();
                    // 填充下拉框
                    data.data.forEach(option => {
                        $('#modalItemSelect').append(`<option value="${option.id}">${option.name}</option>`);
                    });
                } else {
                    alert(data.message);
                }
            },
            error: function (xhr, status, error) {
                alert('请求失败：' + error);
            }
        });
        modal.style.display = "block";
    }

    // 关闭模态窗口
    closeModalBtn.onclick = function() {
        modal.style.display = "none";
    }

    // 点击模态窗口外部关闭窗口
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
});