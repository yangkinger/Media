<?php
// 开启会话
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>媒体配置管理</title>

    <?php include  __DIR__ .'/static/head.html';?>
    <script src="/static/setting.js"></script>
</head>
<body>
    
        <!-- 顶部栏 -->
    <div class="header">
        欢迎
    </div>


     <?php include  __DIR__ .'/static/p_head.html';?>
     
    <div class="content">
        <div style="float:right; padding:10px"><button class="btn btn-success mb-4" onclick="openAddModal()">添加配置</button></div>

        <table class="table table-striped" id="jsonTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Value</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <!-- 数据将通过 jQuery 动态填充 -->
            </tbody>
        </table>


        
        <!-- 新增/编辑项目的模态窗口 -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">新增/编辑项</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                    <label for="modalItemName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="modalItemName" required>
                </div>
                <div class="mb-3">
                    <label for="modalItemValue" class="form-label">Value</label>
                    <textarea class="form-control" id="modalItemValue" rows="8" required></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="modalSubmitBtn" onclick="submitItem()">提交</button>
              </div>
            </div>
          </div>
        </div>

        
        
        
        
        <div class="footer">
            <p>© 2024 频道管理平台 | <a href="#">帮助中心</a></p>
        </div>
        
        
    </div>
    
    

    


    

</body>
</html>
